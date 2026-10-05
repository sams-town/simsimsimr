ALTER TABLE public.trx_medical_fee
ADD COLUMN adjustment_date timestamp without time zone;

COMMENT ON COLUMN public.trx_medical_fee.adjustment_date IS 'Date when BPJS adjustment was imported';
