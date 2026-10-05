-- Function to update trx_balance and master.mst_coa
CREATE OR REPLACE FUNCTION public.update_trx_balance()
RETURNS TRIGGER AS $$
DECLARE
    v_coa_code VARCHAR;
    v_amount DOUBLE PRECISION;
    v_period TIMESTAMP;
    v_branch_id INT;
    v_is_credit_normal BOOLEAN;
    v_trx_date TIMESTAMP;
    v_debit DOUBLE PRECISION := 0;
    v_credit DOUBLE PRECISION := 0;
    v_opening DOUBLE PRECISION := 0;
    v_prev_period TIMESTAMP;
BEGIN
    -- Determine operation type and set variables
    IF TG_OP = 'INSERT' AND TG_TABLE_NAME = 'trx_journal' THEN
        -- Triggered by INSERT on trx_journal (only if Transaction is Published)
        -- We need to check if the parent transaction is published. 
        -- Optimization: pass this check to the WHEN clause or assume logic handles it.
        v_coa_code := NEW.coa_code;
        v_debit := COALESCE(NEW.debit_amount, 0);
        v_credit := COALESCE(NEW.credit_amount, 0);
        v_branch_id := NEW.branch_id;
        
        -- Get transaction date for period
        SELECT transaction_date INTO v_trx_date FROM public.trx_transaction WHERE transaction_id = NEW.transaction_id;
        
    ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'trx_transaction' THEN
        -- Triggered by UPDATE on trx_transaction (Status -> Published)
        -- We loop through journals? No, this function should be called per row.
        -- Wait, a row-level trigger on Transaction can't easily iterate journals efficiently without logic.
        -- Better approach: 
        -- 1. Trigger on Transaction UPDATE calls a statement or iterates journals. 
        -- ACTUALLY, simpler to have the Application still call "Post" but "Post" only updates status.
        -- BUT if we want PURE DB trigger, when Transaction becomes Published, we must iterate its journals.
        
        -- This specific function block is designed to process ONE journal row logic.
        -- Let's separate the logic:
        -- Trigger on Transaction updates Status -> Cursor Loop calls internal logic for each Journal.
        RETURN NEW;
    END IF;

    -- Standardize period to 1st of the month
    v_period := date_trunc('month', v_trx_date);

    -- 1. Update master.mst_coa Balance
    UPDATE master.mst_coa 
    SET balance = COALESCE(balance, 0) + 
        CASE 
            WHEN UPPER(TRIM(normal_position)) IN ('C', 'CREDIT', 'K', 'KREDIT') THEN (v_credit - v_debit)
            ELSE (v_debit - v_credit)
        END,
        updated_date = NOW() AT TIME ZONE 'UTC'
    WHERE coa_code = v_coa_code;

    -- 2. Update or Insert trx_balance
    -- Check if balance exists for this period
    IF EXISTS (SELECT 1 FROM public.trx_balance WHERE coa_code = v_coa_code AND period_date = v_period AND branch_id = v_branch_id) THEN
        UPDATE public.trx_balance
        SET debit_amount = debit_amount + v_debit,
            credit_amount = credit_amount + v_credit,
            updated_date = NOW() AT TIME ZONE 'UTC'
        WHERE coa_code = v_coa_code AND period_date = v_period AND branch_id = v_branch_id;
    ELSE
        -- Get previous closing balance
        v_prev_period := v_period - INTERVAL '1 month';
        SELECT closing_balance INTO v_opening 
        FROM public.trx_balance 
        WHERE coa_code = v_coa_code AND period_date = v_prev_period AND branch_id = v_branch_id;
        
        v_opening := COALESCE(v_opening, 0);

        INSERT INTO public.trx_balance (
            coa_code, period_date, branch_id, opening_balance, debit_amount, credit_amount, closing_balance, created_date, updated_date
        ) VALUES (
            v_coa_code, v_period, v_branch_id, v_opening, v_debit, v_credit, v_opening, NOW() AT TIME ZONE 'UTC', NOW() AT TIME ZONE 'UTC'
        );
    END IF;

    -- 3. Recalculate Closing Balance (Opening + Debit - Credit OR Opening + Credit - Debit)
    SELECT (UPPER(TRIM(normal_position)) IN ('C', 'CREDIT', 'K', 'KREDIT')) INTO v_is_credit_normal FROM master.mst_coa WHERE coa_code = v_coa_code;
    
    UPDATE public.trx_balance
    SET closing_balance = opening_balance + 
        CASE 
            WHEN v_is_credit_normal THEN (credit_amount - debit_amount)
            ELSE (debit_amount - credit_amount)
        END
    WHERE coa_code = v_coa_code AND period_date = v_period AND branch_id = v_branch_id;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger Function to handle Transaction Status Change (Pending -> Published)
CREATE OR REPLACE FUNCTION public.process_transaction_publish()
RETURNS TRIGGER AS $$
DECLARE
    r_journal RECORD;
BEGIN
    -- Only proceed if Status changed to 'Published'
    IF OLD.status != 'Published' AND NEW.status = 'Published' THEN
        
        FOR r_journal IN SELECT * FROM public.trx_journal WHERE transaction_id = NEW.transaction_id LOOP
            PERFORM public.perform_balance_update(
                r_journal.coa_code, 
                NEW.transaction_date::TIMESTAMP, -- Cast to TIMESTAMP
                COALESCE(r_journal.debit_amount, 0), 
                COALESCE(r_journal.credit_amount, 0), 
                r_journal.branch_id
            );
        END LOOP;
        
        NEW.posted_date := NOW() AT TIME ZONE 'UTC';
        NEW.updated_date := NOW() AT TIME ZONE 'UTC';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger Function to handle Journal Insert (when Transaction is already Published)
CREATE OR REPLACE FUNCTION public.process_journal_insert()
RETURNS TRIGGER AS $$
DECLARE
    v_trx_status VARCHAR;
    v_trx_date TIMESTAMP; -- Changed to TIMESTAMP
BEGIN
    SELECT status, transaction_date INTO v_trx_status, v_trx_date 
    FROM public.trx_transaction WHERE transaction_id = NEW.transaction_id;

    IF v_trx_status = 'Published' THEN
        PERFORM public.perform_balance_update(
            NEW.coa_code, 
            v_trx_date, 
            COALESCE(NEW.debit_amount, 0), 
            COALESCE(NEW.credit_amount, 0), 
            NEW.branch_id
        );
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Core Logic Function
CREATE OR REPLACE FUNCTION public.perform_balance_update(
    p_coa_code VARCHAR,
    p_trx_date TIMESTAMP, -- Changed to TIMESTAMP
    p_debit DOUBLE PRECISION,
    p_credit DOUBLE PRECISION,
    p_branch_id INT
) RETURNS VOID AS $$
DECLARE
    v_is_credit_normal BOOLEAN;
    v_opening DOUBLE PRECISION := 0;
    v_closing DOUBLE PRECISION := 0;
    v_net_change DOUBLE PRECISION := 0;
BEGIN
    -- 1. Get Current Balance from Master
    SELECT balance, (UPPER(TRIM(COALESCE(normal_position, 'Debit'))) IN ('C', 'CREDIT', 'K', 'KREDIT')) 
    INTO v_opening, v_is_credit_normal 
    FROM master.mst_coa 
    WHERE coa_code = p_coa_code;

    v_opening := COALESCE(v_opening, 0);

    -- 2. Calculate Net Change
    IF v_is_credit_normal THEN
        v_net_change := p_credit - p_debit;
    ELSE
        v_net_change := p_debit - p_credit;
    END IF;

    -- 3. Calculate Closing Balance
    v_closing := v_opening + v_net_change;

    -- 4. Update master.mst_coa with NEW Closing Balance
    UPDATE master.mst_coa 
    SET balance = v_closing,
        updated_date = NOW() AT TIME ZONE 'UTC'
    WHERE coa_code = p_coa_code;

    -- 5. INSERT into trx_balance
    INSERT INTO public.trx_balance (
        coa_code, 
        period_date, 
        branch_id, 
        opening_balance, 
        debit_amount, 
        credit_amount, 
        closing_balance, 
        created_date, 
        updated_date
    ) VALUES (
        p_coa_code, 
        p_trx_date, 
        p_branch_id, 
        v_opening, 
        p_debit, 
        p_credit, 
        v_closing, 
        NOW() AT TIME ZONE 'UTC', 
        NOW() AT TIME ZONE 'UTC'
    );
END;
$$ LANGUAGE plpgsql;


-- Drop existing triggers if any to avoid errors on rerun
DROP TRIGGER IF EXISTS trg_trx_publish ON public.trx_transaction;
DROP TRIGGER IF EXISTS trg_journal_insert ON public.trx_journal;

-- Create Triggers
CREATE TRIGGER trg_trx_publish
BEFORE UPDATE ON public.trx_transaction
FOR EACH ROW
EXECUTE FUNCTION public.process_transaction_publish();

CREATE TRIGGER trg_journal_insert
AFTER INSERT ON public.trx_journal
FOR EACH ROW
EXECUTE FUNCTION public.process_journal_insert();
