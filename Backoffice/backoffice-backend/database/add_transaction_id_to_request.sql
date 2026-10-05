ALTER TABLE public.trx_petty_cash_request
ADD COLUMN transaction_id VARCHAR(50);

CREATE INDEX idx_petty_cash_request_transaction_id ON public.trx_petty_cash_request(transaction_id);
