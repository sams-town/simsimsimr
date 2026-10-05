import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    nurse: {
        assessment: {
            anamnesa: {
                anamnesa_type: [
                    { value: 1, title: 'Auto Anamnesa' },
                    { value: 2, title: 'Allo Anamnesa' }
                ],
                family_relation: [
                    { value: 1, title: 'Ayah' },
                    { value: 2, title: 'Ibu' },
                    { value: 3, title: 'Kakak' },
                    { value: 4, title: 'Adik' },
                    { value: 5, title: 'Anak' },
                    { value: 6, title: 'Suami' },
                    { value: 7, title: 'Istri' },
                    { value: 8, title: 'Kakek' },
                    { value: 9, title: 'Nenek' },
                    { value: 10, title: 'paman' },
                    { value: 11, title: 'bibi' },
                    { value: 12, title: 'pribadi' },
                    { value: 13, title: 'saudara' },
                ],
            },

            functional: {
                adl: [
                    { value: 1, title: 'Mandiri' },
                    { value: 2, title: 'Dibantu' },
                    { value: 3, title: 'Dibantu Sebagian' },
                ],

                fall_history: [
                    { value: 0, text: 'Tidak' },
                    { value: 25, text: 'Ya' },
                ],

                secondary_diagnosis: [
                    { value: 0, text: 'Tidak' },
                    { value: 25, text: 'Ya' },
                ],

                walking_ambulation: [
                    { value: 0, text: 'Bedrest / dibantu perawat' },
                    { value: 15, text: 'Penyangga / tongkat / walker / kursi roda' },
                    { value: 30, text: 'Mencengkeram furniture' },
                ],

                falling_side_effects: [
                    { value: 0, text: 'Tidak' },
                    { value: 20, text: 'Ya' },
                ],

                walking_way: [
                    { value: 0, text: 'Normal / bedrest / immobilisasi' },
                    { value: 10, text: 'Kelemahan dan lemah' },
                    { value: 20, text: 'Keterbatasan / terganggu' },
                ],

                mental_status: [
                    { value: 0, text: 'Normal / sesuai kemampuan diri' },
                    { value: 15, text: 'Lupa keterbatasan diri / penuruanan kesadaran' },
                ],

                age: [
                    { value: 4, text: '< 3 tahun' },
                    { value: 3, text: '3-6 tahun' },
                    { value: 2, text: '7-12 tahun' },
                    { value: 1, text: '> 12 tahun' },
                ],

                gender: [
                    { value: 2, text: 'Laki-laki' },
                    { value: 1, text: 'Perempuan' },
                ],

                diagnosis: [
                    { value: 4, text: 'Diagnosis neurologi' },
                    { value: 3, text: 'Perubahan oksigenasi (diagnosis respiratorik, dehidrasi, anemia, anoreksia, sinkop, pusing, dll)' },
                    { value: 2, text: 'Gangguan perilaku / psikiatri' },
                    { value: 1, text: 'Diagnosis lainnya' },
                ],

                cognitive_impairement: [
                    { value: 3, text: 'Tidak menyadari keterbatasan dirinya' },
                    { value: 2, text: 'Lupa akan adanya keterbatasan' },
                    { value: 1, text: 'Orientasi baik terhadap diri sendiri' },
                ],

                environtmental_factor: [
                    { value: 4, text: 'Riwayat jatuh / bayi diletakkan di tempat tidur dewasa' },
                    { value: 3, text: 'Pasien menggunakan alat bantu / bayi diletakkan dalam tempat tidur bayi / perabotan rumah' },
                    { value: 2, text: 'pasien diletakkan di tempat tidur' },
                    { value: 1, text: 'Area diluar rumah sakit' },
                ],

                sedation_anesthesia: [
                    { value: 3, text: 'Dalam 24 jam' },
                    { value: 2, text: 'Dalam 48 jam' },
                    { value: 1, text: '> 48 jam atau tidak menjalani pembedahan / sedasi / anastesi' },
                ],

                medication_use: [
                    { value: 3, text: 'Penggunaan multipel: sedatif, obat hipnosis, barbiturat, fenotiazin, antidepresan, pencahar, diuretik, narkose' },
                    { value: 2, text: 'Penggunaan salah satu obat diatas' },
                    { value: 1, text: 'Penggunaan medikasi lainnya / tidak ada medikasi' },
                ],
            },

            emergency: {
                hospitalized: [
                    { value: 0, title: 'Tidak' },
                    { value: 1, title: 'Ya, dimana?' },
                ],
                surgery: [
                    { value: 0, title: 'Tidak pernah' },
                    { value: 1, title: 'Pernah, kapan?' },
                ],
                allergy: [
                    { value: 0, title: 'Tidak' },
                    { value: 1, title: 'Ya, sebutkan' },
                ],
                family_disease: [
                    { value: 0, title: 'Tidak' },
                    { value: 1, title: 'Ya' },
                ],
                body_mass: [
                    { value: 1, text: '18,5-22.9' },
                    { value: 2, text: '17-18.4 / 23-24.9' },
                    { value: 3, text: '<17 / >=25' },
                ],
                six_months: [
                    { value: 1, text: 'Tidak' },
                    { value: 2, text: 'Tidak Yakin' },
                    { value: 3, text: 'Ya' },
                ],
                lose_weight: [
                    { value: 1, text: '1 - 5 kg' },
                    { value: 2, text: '6 - 10 kg' },
                    { value: 3, text: '11 - 15 kg' },
                    { value: 4, text: '> 15 kg' },
                    { value: 5, text: 'Tidak Tahu Berapa kg' },
                ],
                food_intake: [
                    { value: 1, text: 'Baik' },
                    { value: 2, text: 'Kurang' },
                    { value: 3, text: 'Kurang Semangat' },
                ],
                disease_condition: [
                    { value: 1, text: 'Tidak' },
                    { value: 2, text: 'Ya' },
                ],
                certain_diets: [
                    { value: 1, text: 'Tidak' },
                    { value: 2, text: 'Ya' },
                ],
            },

            hemodialysis: {
                hemo_type: [
                    { value: 1, title: 'Rutin' },
                    { value: 2, title: 'Cito' },
                    { value: 3, title: 'Ekstra' },
                ],
                hemo_frequency: [
                    { value: 1, title: '1x/minggu' },
                    { value: 2, title: '2x/minggu' },
                    { value: 3, title: '3x/minggu' },
                    { value: 4, title: 'Tidak Tentu' },
                ],
                hemo_dialysate: [
                    { value: 1, title: 'Asetat' },
                    { value: 2, title: 'Bikarbonat' },
                    { value: 3, title: 'Suhu Dialisat' },
                ],
                hemo_vascular: [
                    { value: 1, title: 'Cimino' },
                    { value: 2, title: 'Femoral' },
                    { value: 3, title: 'CDL Subciavia' },
                    { value: 4, title: 'CDL Jugularis' },
                ],
                heparin: [
                    { value: 1, title: 'Heparin' },
                    { value: 2, title: 'Regular' },
                    { value: 3, title: 'Minimal' },
                    { value: 4, title: 'Free' },
                ],
                // heparin: [
                //     { value: 1, title: 'Heparin' },
                //     { value: 2, title: 'Tanpa Heparin' },
                // ],
                profiling: [
                    { value: 1, title: 'Ultrafiltrasi Mode' },
                    { value: 2, title: 'Natriuum Mode' },
                    { value: 3, title: 'Bikarbonat Mode' },
                ],
            },

            obgyn: {
                married: [
                    { value: 0, title: 'Belum kawin' },
                    { value: 1, title: 'Kawin' },
                ],
                antenatal: [
                    { value: 1, title: 'Pemeriksaan teratur' },
                    { value: 0, title: 'Pemeriksaan tidak teratur' },
                ],
                his: [
                    { value: 0, title: 'Tidak teratur' },
                    { value: 1, title: 'Teratur' },
                ],
                amniotic: [
                    { value: 0, title: 'Belum pecah' },
                    { value: 1, title: 'Sudah pecah' },
                ],
                amniotic_color: [
                    { value: 1, title: 'Jernih' },
                    { value: 2, title: 'Hijau cair' },
                    { value: 3, title: 'Hijau kental' },
                    { value: 4, title: 'Kemerahan' },
                    { value: 5, title: 'Lainnya' },
                ],
                seizure: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                    { value: 3, title: 'Tonik' },
                    { value: 4, title: 'Klonik' },
                ],
                leopold_2: [
                    { value: 1, title: 'Punggung kanan' },
                    { value: 2, title: 'Punggung kiri' },
                    { value: 3, title: 'Letak lintang' },
                    { value: 4, title: 'Letak oblik' },
                ],
                leopold_3: [
                    { value: 1, title: 'Letak kepala' },
                    { value: 2, title: 'Letak bokong' },
                    { value: 3, title: 'lain-lain' },
                ],
                leopold_4: [
                    { value: 1, title: '1/5' },
                    { value: 2, title: '2/5' },
                    { value: 3, title: '3/5' },
                    { value: 4, title: '4/5' },
                    { value: 5, title: '5/5' },
                ],
                heart_rhythm: [
                    { value: 1, title: 'Teratur' },
                    { value: 2, title: 'Tidak teratur' },
                    { value: 3, title: 'Tidak terdengar' },
                ],
                vulva: [
                    { value: 1, title: 'Varices' },
                    { value: 2, title: 'Kondiloma' },
                    { value: 3, title: 'Oedem' },
                    { value: 4, title: 'Lain-lain' },
                ],
                portio: [
                    { value: 1, title: 'Kaku' },
                    { value: 2, title: 'Lunak' },
                ],
                amniotic_internal: [
                    { value: 1, title: 'Positif' },
                    { value: 2, title: 'Negatif' },
                ],
                presentation: [
                    { value: 1, title: 'Kepala' },
                    { value: 2, title: 'bokong' },
                    { value: 3, title: 'Muka' },
                    { value: 4, title: 'Lain-lain' },
                ],
                ctg_variability: [
                    { value: 1, title: 'Normal' },
                    { value: 2, title: 'Berkurang' },
                    { value: 3, title: 'Silent' },
                    { value: 4, title: 'Tidak ada' },
                    { value: 5, title: 'Saltatory' },
                ],
                ctg_deceleration_type: [
                    { value: 1, title: 'Dini' },
                    { value: 2, title: 'Lambat' },
                    { value: 3, title: 'Variabel' },
                    { value: 4, title: 'Memanjang' },
                ],
                prognosis: [
                    { value: 1, title: 'Bonam' },
                    { value: 2, title: 'Malam' },
                ],
                partus_plan: [
                    { value: 1, title: 'Partum per vaginam' },
                    { value: 2, title: 'Section caesaria' },
                    { value: 3, title: 'Konservatif' },
                    { value: 4, title: 'Partus percobaan' },
                ],
                lowest_drop: [
                    { value: 1, title: 'Hodge 1' },
                    { value: 2, title: 'Hodge 2' },
                    { value: 3, title: 'Hodge 3' },
                    { value: 4, title: 'Hodge 4' },
                ],
            },

            baby_born: {
                heart_frequency: [
                    { value: 1, title: '(0) Tidak ada', subValue: 0 },
                    { value: 2, title: '(1) Kurang dari 100', subValue: 1 },
                    { value: 3, title: '(2) Lebih dari 100', subValue: 2 },
                ],
                respiration: [
                    { value: 1, title: '(0) Tidak ada', subValue: 0 },
                    { value: 2, title: '(1) Lemah Menangis', subValue: 1 },
                    { value: 3, title: '(2) Menangis Kuat dan Baik', subValue: 2 },
                ],
                muscle_activity: [
                    { value: 1, title: '(0) Lembek', subValue: 0  },
                    { value: 2, title: '(1) Gerakan Sedikit', subValue: 1 },
                    { value: 3, title: '(2) Gerakan Baik', subValue: 2 },
                    { value: 4, title: '(0) Tidak beraksi', subValue: 0  },
                ],
                reflect_reaction: [
                    { value: 1, title: '(1) Reaksi Sedikit', subValue: 1 },
                    { value: 2, title: '(2) Sonde Hidung : Bersin atau Batuk', subValue: 2  },
                    { value: 3, title: '(2) Telapak Kaki : Menangis dan Kaki Menarik Kembali', subValue: 2  },
                ],
                skin_color: [
                    { value: 1, title: '(0) Pucat / Biru', subValue: 0 },
                    { value: 2, title: '(1) Badan Merah Muda & Ekstremitas Biru (Anggota-anggota Badan)', subValue: 1  },
                    { value: 3, title: '(2) Seluruhnya Merah Muda', subValue: 2 },
                ],
                caput_succedanium: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ada' },
                ],
                cephal_haematom: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ada' },
                ],
                head: [
                    { value: 1, title: 'Lembek' },
                    { value: 2, title: 'Keras' },
                ],
                suture: [
                    { value: 1, title: 'Rapat' },
                    { value: 2, title: 'Bertindih' },
                    { value: 3, title: 'Renggang' },
                ],
                died_in_pregnancy: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                died_in_childbirth: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                died_after_birth: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                ambu: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                heart_massage: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                endotracheal_tube: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                hfb_mucus_suction: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                hfb_stimulus: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                hfb_oxygen: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                hfb_resuscitation: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
                hfb_delivery: [
                    { value: 1, title: 'SC' },
                    { value: 2, title: 'Spontan' },
                    { value: 3, title: 'VE' },
                    { value: 4, title: 'Lainnya' },
                ],
                hfb_newborn_baby: [
                    { value: 1, title: 'Aterm' },
                    { value: 2, title: 'Prematur' },
                    { value: 3, title: 'Immatur' },
                    { value: 4, title: 'Dysmatur' },
                    { value: 5, title: 'Serotinus' },
                ],
                hfb_baby_condition: [
                    { value: 1, title: 'Segera menangis kuat' },
                    { value: 2, title: 'Segera menangis lemah' },
                    { value: 3, title: 'Tidak segera menangis' },
                    { value: 4, title: 'Menangis setelah disuction' },
                ],
                umbilical_cord_entanglement: [
                    { value: 1, title: 'Tidak' },
                    { value: 2, title: 'Ya' },
                ],
            },

            neonate: {
                labor_type: [
                    { value: 1, title: 'Spontan' },
                    { value: 2, title: 'Vakum / Forcep' },
                    { value: 3, title: 'SC' },
                ],
                mother_blood: [
                    { value: 1, title: 'A' },
                    { value: 2, title: 'B' },
                    { value: 3, title: 'O' },
                    { value: 4, title: 'AB' },
                    { value: 5, title: 'Rhesus' },
                ],
                birth_history: [
                    { value: 1, title: 'Tunggal' },
                    { value: 2, title: 'Kembar' },
                    { value: 3, title: 'LK' },
                    { value: 4, title: 'LD' },
                    { value: 5, title: 'Anus' },
                ],
                residence: [
                    { value: 1, title: 'Penitipan anak' },
                    { value: 2, title: 'Pengasuh' },
                    { value: 3, title: 'Rumah' },
                ],
                caring_client: [
                    { value: 1, title: 'Ayah' },
                    { value: 2, title: 'Ibu' },
                    { value: 3, title: 'Nenek' },
                    { value: 4, title: 'Pengasuh' },
                ],
                sleep_pattern: [
                    { value: 1, title: 'Terganggu' },
                    { value: 2, title: 'Tidak' },
                ],
                is_clean: [
                    { value: 1, title: 'Bersih' },
                    { value: 2, title: 'Kotor' },
                ],
                scrotum: [
                    { value: 1, title: 'Testis' },
                    { value: 2, title: 'Ya' },
                    { value: 3, title: 'Tidak' },
                ],
                urine: [
                    { value: 1, title: 'Jernih' },
                    { value: 2, title: 'Pekat' },
                    { value: 3, title: 'Hematuria' },
                    { value: 4, title: 'Incontinentia urine' },
                    { value: 5, title: 'Retensio urine' },
                ],
            },
        }
    },

    doctor: {
        resume: {
            exit: [
                { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', title: 'Meninggal' },
                { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', title: 'Atas persetujuan dokter' },
                { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', title: 'Dirujuk' },
                { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', title: 'Permintaan sendiri' },
                { value: 'bbc9fdce-affb-4b32-a386-0ed7e91c1e89', title: 'Pulang non ranap' },
                { value: 'adb7ce0b-ede3-4fee-b273-ce159d286732', title: 'Lainnya' },
            ],

            exit_condition: [
                { value: 1, title: 'Perbaikan' },
                { value: 2, title: 'Tidak sembuh' },
                { value: 3, title: 'Meninggal < 48 jam' },
                { value: 5, title: 'Meninggal > 48 jam' },
                { value: 4, title: 'Lainnya' },
            ],

            control_instruction: [
                { value: 1, title: 'RS Taman Harapan Baru' },
                { value: 2, title: 'Dokter Keluarga / Klinik Luar' },
                { value: 3, title: 'Puskesmas' },
                { value: 4, title: 'Lainnya' },
            ],
        },

        assessment: {
            generalis_key: [
                { default: 1, label: 'Kepala', key: "head", name: 'getHead' },
                { default: 1, label: 'Warna Rambut', key: "head_hair_color", name: 'getHairColor' },
                { default: 1, label: 'Kondisi Rambut', key: "head_hair_condition", name: 'getHairCondition' },
                { default: 1, label: 'Conjunctiva', key: "eye_conjunctive", name: 'getEyeConjunctive' },
                { default: 1, label: 'Sklera', key: "eye_sclera", name: 'getEyeSclera' },
                { default: 1, label: 'Pupil', key: "eye_pupil", name: 'getEyePupil' },
                { default: 1, label: 'Reflek Cahaya', key: "eye_light_reflex", name: 'getEyeReflect' },
                { default: 1, label: 'Buta Warna', key: "eye_blind_color", name: 'getEyeBlindColor' },
                { default: 1, label: 'Mukosa', key: "mouth_mucosa", name: 'getMouthMucosa' },
                { default: 1, label: 'Perubahan Warna', key: "mouth_discoloration", name: 'getMouthDiscoloration' },
                { default: 1, label: 'Lidah', key: "mouth_tongue", name: 'getMouthTongue' },
                { default: 1, label: 'Karies', key: "mouth_caries", name: 'getMouthCaries' },
                { default: 1, label: 'KGB', key: "neck_kgb", name: 'getNeckKGB' },
                { default: 1, label: 'Posisi Trakea', key: "neck_trachea_position", name: 'getNeckTracheaPosition' },
                { default: 1, label: 'Massa', key: "neck_massa", name: 'getNeckMassa' },
                { default: 1, label: 'JVP', key: "neck_jvp", name: 'getNeckJVP' },
                { default: 1, label: 'Inspeksi Dada', key: "thorax_insp_chest", name: 'getInspectionChest' },
                { default: 1, label: 'Retraksi', key: "thorax_insp_retraction", name: 'getInspectionRetractiont' },
                { default: 1, label: 'Palpasi Dada', key: "thorax_palp_chest", name: 'getPalpationChest' },
                { default: 1, label: 'Fremitus', key: "thorax_palp_fremitus", name: 'getPalpationFremitus' },
                { default: 1, label: 'Krepitasi', key: "thorax_palp_crepitation", name: 'getPalpationKrepitasi' },
                { default: 1, label: 'Pulmo Vasikuler', key: "thorax_ausk_pul_vasiculer", name: 'getAuskultationPulmoVasikuler' },
                { default: 1, label: 'Pulmo Rhonki', key: "thorax_ausk_pul_rhonki", name: 'getAuskultationPulmoRhonki' },
                { default: 1, label: 'Pulmo Wheezing', key: "thorax_ausk_pul_wheezing", name: 'getAuskultationPulmoWheezing' },
                { default: 1, label: 'Bunyi Jantung', key: "thorax_ausk_cor_heart_sound", name: 'getAuskultationCORBJ' },
                { default: 1, label: 'Murmur', key: "thorax_ausk_cor_murmur", name: 'getAuskultationCORMurmur' },
                { default: 1, label: 'Gallop', key: "thorax_ausk_cor_gallop", name: 'getAuskultationCORGallop' },
                { default: 1, label: 'Supel', key: "abdomen_supel", name: 'getAbdomenSupel' },
                { default: 1, label: 'Bising Usus', key: "abdomen_bowel_sounds", name: 'getAbdomenBising' },
                { default: 1, label: 'Nyeri Tekan', key: "abdomen_tenderness", name: 'getAbdomenPress' },
                { default: 1, label: 'Hepar & Lien', key: "abdomen_hepar_lien", name: 'getAbdomenHepar' },
                { default: 1, label: 'Akral', key: "extremity_acral", name: 'getEkstremitasAkral' },
                { default: 1, label: 'extremity_crt', key: "extremity_crt", name: 'getEkstremitasCRT' },
                { default: 1, label: 'extremity_edema', key: "extremity_edema", name: 'getEkstremitasEdema' },
            ],

            prescription: {
                parent_stock: [
                    { value: 1, title: 'OBAT SATUAN' },
                    { value: 2, title: 'RACIK 1' },
                    { value: 3, title: 'RACIK 2' },
                    { value: 4, title: 'RACIK 3' },
                    { value: 5, title: 'RACIK 4' }
                ],

                parent_package: [
                    { value: 1, title: 'PUYER' },
                    { value: 2, title: 'KAPSUL' },
                    { value: 3, title: 'SALEP' },
                    { value: 4, title: 'SIRUP' },
                    { value: 5, title: 'POT' },
                ],

                utilization: [
                    { value: 'Sebelum makan', title: 'Sebelum makan' },
                    { value: 'Setelah makan', title: 'Setelah makan' },
                    { value: 'Saat makan', title: 'Saat makan' },
                    { value: 'Pemakaian luar', title: 'Pemakaian luar' },
                    { value: 'Pada area lesi', title: 'Pada area lesi' },
                    { value: 'Intravena(IV)', title: 'Intravena(IV)' },
                    { value: 'Intramuscular(IM)', title: 'Intramuscular(IM)' },
                    { value: 'Subcutaneus', title: 'Subcutaneus' },
                    { value: 'Intraouseus', title: 'Intraouseus' },
                    { value: 'other', title: 'Lainnya' },
                    // { value: '1 AMPUL/ 24 JAM', title: '1 AMPUL/ 24 JAM' },
                    // { value: '1 AMPUL/ 12 JAM', title: '1 AMPUL/ 12 JAM' },
                    // { value: '1 AMPUL/ 8 JAM', title: '1 AMPUL/ 8 JAM' },
                    // { value: '1 AMPUL/ 6 JAM', title: '1 AMPUL/ 6 JAM' },
                    // { value: '1 VIAL/ 24 JAM', title: '1 VIAL/ 24 JAM' },
                    // { value: '1 VIAL/ 12 JAM', title: '1 VIAL/ 12 JAM' },
                    // { value: '1 VIAL/ 8 JAM', title: '1 VIAL/ 8 JAM' },
                    // { value: '1 VIAL/ 6 JAM', title: '1 VIAL/ 6 JAM' },
                    // { value: '15 ml/ 24 JAM', title: '15 ml/ 24 JAM' },
                    // { value: '15 ml/ 12 JAM', title: '15 ml/ 12 JAM' },
                    // { value: '15 ml/ 8 JAM', title: '15 ml/ 8 JAM' },
                    // { value: '15 ml/ 6 JAM', title: '15 ml/ 6 JAM' },
                    // { value: '5 ml/ 24 JAM', title: '5 ml/ 24 JAM' },
                    // { value: '5 ml/ 12 JAM', title: '5 ml/ 12 JAM' },
                    // { value: '5 ml/ 8 JAM', title: '5 ml/ 8 JAM' },
                    // { value: '5 ml/ 6 JAM', title: '5 ml/ 6 JAM' },
                    // { value: '1 FLS/ 24 JAM', title: '1 FLS/ 24 JAM' },
                    // { value: '1 FLS/ 12 JAM', title: '1 FLS/ 12 JAM' },
                    // { value: '1 TETES/ 24 JAM', title: '1 TETES/ 24 JAM' },
                    // { value: '1 TETES/ 12 JAM', title: '1 TETES/ 12 JAM' },
                    // { value: '1 TABLET TIAP 24 JAM', title: '1 TABLET TIAP 24 JAM' },
                    // { value: '1 TABLET TIAP 12 JAM', title: '1 TABLET TIAP 12 JAM' },
                    // { value: '1 TABLET TIAP 8 JAM', title: '1 TABLET TIAP 8 JAM' },
                    // { value: '1 TABLET TIAP 6 JAM', title: '1 TABLET TIAP 6 JAM' },
                    // { value: '1/2 TABLET TIAP 24 JAM', title: '1/2 TABLET TIAP 24 JAM' },
                    // { value: '1/2 TABLET TIAP 12 JAM', title: '1/2 TABLET TIAP 12 JAM' },
                    // { value: '1/2 TABLET TIAP 8 JAM', title: '1/2 TABLET TIAP 8 JAM' },
                    // { value: '1/2 TABLET TIAP 6 JAM', title: '1/2 TABLET TIAP 6 JAM' },
                    // { value: '1 KAPSUL TIAP 24 JAM', title: '1 KAPSUL TIAP 24 JAM' },
                    // { value: '1 KAPSUL TIAP 12 JAM', title: '1 KAPSUL TIAP 12 JAM' },
                    // { value: '1 KAPSUL TIAP 8 JAM', title: '1 KAPSUL TIAP 8 JAM' },
                    // { value: '1 KAPSUL TIAP 6 JAM', title: '1 KAPSUL TIAP 6 JAM' },
                    // { value: '1 Puff/ 24 JAM', title: '1 Puff/ 24 JAM' },
                    // { value: '1 Puff/ 12 JAM', title: '1 Puff/ 12 JAM' },
                    // { value: '1 Puff/ 8 JAM', title: '1 Puff/ 8 JAM' },
                    // { value: '1 OLES TIAP 24 JAM', title: '1 OLES TIAP 24 JAM' },
                    // { value: '1 OLES TIAP 12 JAM', title: '1 OLES TIAP 12 JAM' },
                    // { value: '1 OLES TIAP 8 JAM', title: '1 OLES TIAP 8 JAM' },
                    // { value: '1 OLES TIAP 6 JAM', title: '1 OLES TIAP 6 JAM' },
                    // { value: '1 BUNGKUS/ 24 JAM', title: '1 BUNGKUS/ 24 JAM' },
                    // { value: '1 BUNGKUS/ 12 JAM', title: '1 BUNGKUS/ 12 JAM' },
                    // { value: '1 BUNGKUS/ 8 JAM', title: '1 BUNGKUS/ 8 JAM' },
                    // { value: '1 BUNGKUS/ 6 JAM', title: '1 BUNGKUS/ 6 JAM' },
                    // { value: 'other', title: 'Lainnya' },
                ],

                consume_time: [
                    { value: 'Pagi', title: 'Pagi' },
                    { value: 'Siang', title: 'Siang' },
                    { value: 'Malam', title: 'Malam' },
                    { value: 'Pagi-Malam', title: 'Pagi-Malam' },
                    { value: 'Pagi-Siang-Malam', title: 'Pagi-Siang-Malam' },
                ],

                signatura: [
                    { value: '1x1/2', title: '1x1/2' },
                    { value: '1x1', title: '1x1' },
                    { value: '1x2', title: '1x2' },
                    { value: '1x3', title: '1x3' },
                    { value: '1x4', title: '1x4' },
                    { value: '1x5', title: '1x5' },
                    { value: '2x1/2', title: '2x1/2' },
                    { value: '2x1', title: '2x1' },
                    { value: '2x2', title: '2x2' },
                    { value: '3x1/2', title: '3x1/2' },
                    { value: '3x1', title: '3x1' },
                    { value: '3x2', title: '3x2' },
                    { value: '3x3', title: '3x3' },
                    { value: '4x1/2', title: '4x1/2' },
                    { value: '4x1', title: '4x1' },
                    { value: '4x2', title: '4x2' },
                    { value: '4x3', title: '4x3' },
                    { value: 'Pro injeksi', title: 'Pro injeksi' },
                    { value: 'other', title: 'Lainnya' },
                ],

                prescription_type:[
                    { value: 1, title: 'Resep Tambahan' },
                    { value: 2, title: 'Resep Pulang' },
                    { value: 4, title: 'DO' }
                ],

                depo:[
                    { value: '297c44f0-12ef-40e6-b9a7-c831065b3372', title: 'APOTIK CENTRAL' },
                    { value: '822d71e8-7a2b-4fc6-a372-40a6b458d8c3', title: 'FARMASI OK' },
                ],

                kardex_item_status: [
                    { value: 1, title: 'Regular' },
                    { value: 2, title: 'Resep tambahan' },
                    { value: 3, title: 'Extra' },
                    { value: 4, title: 'Stop' },
                ],

                prescription_item_status: [
                    { value: 1, title: 'Regular' },
                    { value: 3, title: 'Extra' },
                ],
            },
        },

        consultation: {
            consultation_type: [
                { value: 1, title: 'Perawatan Bersama' },
                { value: 2, title: 'Alih Rawat' },
                { value: 3, title: 'Konsultasi Sewaktu' },
            ],
            emergency_consultation_type: [
                { value: 1, title: 'Perawatan Bersama' },
                { value: 3, title: 'Konsultasi Sewaktu' },
            ],
        },

        sbar: {
            consultation_via: [
                { value: 1, title: 'WA (whatsapp)' },
                { value: 2, title: 'Telepon' },
                { value: 3, title: 'Lainnya' },
            ]
        },
    },

    general: {
        transfer: {
            mobilization: [
                { value: 1, title: 'Bed' },
                { value: 2, title: 'Kursi Roda' },
            ],
        },

        spri: {
            indication: [
                { value: 1, title: 'Perbaikan KU' },
                { value: 2, title: 'Pemberian antibiotik injeksi' },
                { value: 3, title: 'Terapi oksigen' },
                { value: 4, title: 'Terapi cairan' },
                { value: 5, title: 'Penanganan nyeri' },
                { value: 6, title: 'Gangguan hemodinamik' },
                { value: 7, title: 'Gangguan kesadaran' },
                { value: 8, title: 'Gangguan pernapasan' },
                { value: 9, title: 'Gangguan elektrolit' },
                { value: 10, title: 'Gangguan metabolik akut' },
                { value: 11, title: 'Gangguan jantung' },
                { value: 12, title: 'Gangguan neurologis' },
                { value: 13, title: 'Infeksi berat' },
                { value: 14, title: 'Post-operatif' },
                { value: 15, title: 'Cedera berat' },
                { value: 16, title: 'Perawatan paliatif' },
            ],

            condition: [
                { value: 1, title: 'Tampak sakit ringan' },
                { value: 2, title: 'Tampak sakit sedang' },
                { value: 3, title: 'Tampak sakit berat' },
            ],
        },

        checkout: {
            type_visit: [
                { value: 1, title: 'Kunjungan Awal' },
                { value: 2, title: 'Kontrol Lanjutan' },
                { value: 3, title: 'Observasi' },
                { value: 4, title: 'Post OP' },
                { value: 5, title: 'Post Rawat Inap' },
            ],

            exit: [
                { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', text: 'Atas Persetujuan Dokter' },
                { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', text: 'Meninggal' },
                { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', text: 'Dirujuk' },
                { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', text: 'Permintaan Sendiri' }
            ],
        },

        operatie: {
            operation_type: [
                { value: 1, title: 'CITO' },
                { value: 2, title: 'ELECTIVE' },
                { value: 3, title: 'One Day Care (ODC)' },
                { value: 4, title: 'ELECTIVE TAMBAHAN' },
            ],

            infectious: [
                { value: 1, title: 'Infectious' },
                { value: 2, title: 'Non-Infectious' },
            ],
        },

        vital_sign: {
            skin_color: [
                { value: 1, title: 'Pink' },
                { value: 2, title: 'Pucat/berbintik' },
            ],

            behavior: [
                { value: 1, title: 'Sesuai' },
                { value: 2, title: 'Cenderung murung/Diam' },
                { value: 3, title: 'Sensitif' },
                { value: 4, title: 'Letargik' },
                { value: 5, title: 'Bingung' },
                { value: 6, title: 'Penurunan Ransanganan Nyeri' },
                { value: 7, title: 'Unrespon' },
            ],
        },

        bhp: {
            depo: [
                { value: '297c44f0-12ef-40e6-b9a7-c831065b3372', title: 'APOTIK CENTRAL' },
                { value: '822d71e8-7a2b-4fc6-a372-40a6b458d8c3', title: 'FARMASI OK' },
                // { value: '4105d8bc-5769-463e-bc67-aae2fc5adf10', title: 'FARMASI IGD' },
            ],
        }
    },

    master: {
        room: {
            surgery: [
                { value: 0, title: 'Non-bedah' },
                { value: 1, title: 'Bedah' },
            ],

            isolation: [
                { value: 0, title: 'Non-isolasi' },
                { value: 1, title: 'Isolasi' },
            ],

            room_pressure: [
                { value: '', title: 'Normal' },
                { value: '+', title: 'Positif' },
                { value: '-', title: 'Negatif' },
            ],

            gender_availability: [
                { value: '', title: 'Genderless' },
                { value: 0, title: 'Perempuan' },
                { value: 1, title: 'Laki-laki' },
            ],

            internal_disease: [
                { value: 0, title: 'Tidak' },
                { value: 1, title: 'Ya' },
            ],
        },

        blue_print: {
            legends: {
                room_gender: [
                    { name: 'Genderless', color: 'bg-white' },
                    { name: 'Perempuan', color: 'bg-pink-200' },
                    { name: 'Laki-laki', color: 'bg-info-300' },
                ],

                room_status: [
                    { name: 'Tekanan positif', color: 'blue', icon: 'PlusCircleIcon' },
                    { name: 'Tekanan negatif', color: 'red', icon: 'MinusCircleIcon' },
                    { name: 'Isolasi', color: 'brown', icon: 'ShieldIcon' },
                    { name: 'Bedah', color: 'orange', icon: 'ZapIcon' },
                    { name: 'Penyakit dalam', color: 'red', icon: 'HeartIcon' },
                ],

                bed: [
                    { name: 'Available', color: 'bg-neutral-100' },
                    { name: 'Filled', color: 'bg-w3green' },
                    { name: 'Linen', color: 'bg-blinking' },
                ],
            }
        }
    }
})

// getters
const getters = {}

// actions
const actions = {}

// mutations
const mutations = {}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
