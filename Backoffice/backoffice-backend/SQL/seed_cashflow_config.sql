-- ============================================================
-- SQL Seed Script: CFG_REPORT_CASHFLOW Option
-- Based on the user-provided cash flow template reference.
-- 
-- NOTE: Adjust branch_id (currently 1) as needed for your setup.
-- If the option already exists, use the UPDATE statement instead.
-- ============================================================

-- INSERT (use this if the option does not exist yet)
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES (
    'CFG_REPORT_CASHFLOW',
    '[
        {
            "sectionName": "OPERATING ACTIVITIES",
            "coaCode": "",
            "showChild": true,
            "subSection": [
                {
                    "sectionName": "Revenue",
                    "coaCode": "",
                    "showChild": true,
                    "subSection": [
                        {
                            "sectionName": "Setoran Kasir",
                            "coaCode": "101001003",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Setoran Tunai Lainnya",
                            "coaCode": "",
                            "showChild": true,
                            "subSection": [
                                {
                                    "sectionName": "Umum",
                                    "coaCode": "401101004",
                                    "showChild": false,
                                    "subSection": []
                                },
                                {
                                    "sectionName": "Asuransi",
                                    "coaCode": "103001002",
                                    "showChild": false,
                                    "subSection": []
                                },
                                {
                                    "sectionName": "BPJS TK",
                                    "coaCode": "103001006",
                                    "showChild": false,
                                    "subSection": []
                                },
                                {
                                    "sectionName": "BPJS Kesehatan",
                                    "coaCode": "103001001",
                                    "showChild": false,
                                    "subSection": []
                                }
                            ]
                        },
                        {
                            "sectionName": "Other Income",
                            "coaCode": "722099",
                            "showChild": false,
                            "subSection": []
                        }
                    ]
                },
                {
                    "sectionName": "Obat",
                    "coaCode": "201001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Alkes",
                    "coaCode": "201001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Laboratorium",
                    "coaCode": "201001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "HD",
                    "coaCode": "201001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Penunjang Lainnya",
                    "coaCode": "201001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Labor Cost",
                    "coaCode": "",
                    "showChild": true,
                    "subSection": [
                        {
                            "sectionName": "Biaya Gaji",
                            "coaCode": "602001001",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Tunjangan BPJS TK",
                            "coaCode": "602001014",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Tunjangan BPJS Kesehatan",
                            "coaCode": "602001015",
                            "showChild": false,
                            "subSection": []
                        }
                    ]
                },
                {
                    "sectionName": "General & Administrative Exp.",
                    "coaCode": "",
                    "showChild": true,
                    "subSection": [
                        {
                            "sectionName": "Other GA Expense",
                            "coaCode": "608125",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Kas Pusat",
                            "coaCode": "101001001",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Logistik",
                            "coaCode": "201002",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Aset",
                            "coaCode": "201002",
                            "showChild": false,
                            "subSection": []
                        }
                    ]
                },
                {
                    "sectionName": "Dokter Fee",
                    "coaCode": "602001017",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Midwife (Bidan)",
                    "coaCode": "608106",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Others",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Pajak",
                    "coaCode": "",
                    "showChild": true,
                    "subSection": [
                        {
                            "sectionName": "Pph Pasal 21",
                            "coaCode": "206001",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Pph Pasal 23/26",
                            "coaCode": "206002",
                            "showChild": false,
                            "subSection": []
                        },
                        {
                            "sectionName": "Pph Pasal 25/29",
                            "coaCode": "206003",
                            "showChild": false,
                            "subSection": []
                        }
                    ]
                }
            ]
        },
        {
            "sectionName": "INVESTING ACTIVITIES",
            "coaCode": "",
            "showChild": true,
            "subSection": [
                {
                    "sectionName": "Dividend / OSP Received",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Land Acquisition",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Capital Expenditure",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Investment",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Others",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                }
            ]
        },
        {
            "sectionName": "FINANCING ACTIVITIES",
            "coaCode": "",
            "showChild": true,
            "subSection": [
                {
                    "sectionName": "Affiliated Loan (In)",
                    "coaCode": "205099",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Loan Withdrawal",
                    "coaCode": "205001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Loan Installment & Interest Exp.",
                    "coaCode": "205001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Cicil Bank",
                    "coaCode": "222001",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Affiliated Loan (Out)",
                    "coaCode": "205099",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Transfer (In)",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                },
                {
                    "sectionName": "Transfer (Out)",
                    "coaCode": "",
                    "showChild": false,
                    "subSection": []
                }
            ]
        }
    ]',
    'JSON',
    'Cash Flow Report Configuration',
    'Konfigurasi struktur laporan arus kas (cash flow)',
    1
);

-- UPDATE (use this if the option already exists)
-- UPDATE cfg_options 
-- SET option_value = '<paste the JSON above>'
-- WHERE option_name = 'CFG_REPORT_CASHFLOW' AND branch_id = 1;
