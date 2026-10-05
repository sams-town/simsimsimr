import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'
import prescription from '../pharmacy/prescription'

// initial state
const state = () => ({
  procedure: {
    items: [],
    totalRows: 1,
  },
  bhp: {
    items: [],
    totalRows: 1,
  },

  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  fields: [
    { key: 'norm', label: 'No RM', sortable: true },
    { key: 'name', label: 'Nama Pasien'},
    { key: 'sex', label: 'Jenis Kelamin'},
    { key: 'birth_place', label: 'Tempat Lahir'},
    { key: 'birth_date', label: 'Tanggal Lahir'},
    { key: 'address', label: 'Alamat'},
    { key: 'action', label: 'Aksi'}
  ],
  items: [],
  fieldstransaction: [
    { key: 'date_added', label: 'Tanggal & Jam', sortable: true },
    { key: 'nurse_id', label: 'Nama Pasien'},
    { key: 'costing_name', label: 'Nama Pasien'},
    { key: 'notes', label: 'Keterangan'},
    { key: 'var_1', label: 'Harga'},
    { key: 'action', label: 'Aksi'}
  ],
  itemstransaction: [],
  prescription: {
    fields: [
      { key: 'date_added', label: 'Tanggal & Jam', sortable: true },
      { key: 'nurse_id', label: 'Nama Pasien'},
      { key: 'costing_name', label: 'Nama Pasien'},
      { key: 'notes', label: 'Keterangan'},
      { key: 'var_1', label: 'Harga'},
      { key: 'action', label: 'Aksi'}
    ],
    items: []
  },
  options:{
    plandoctor: [],
    examination_category: [],
    plandoctorletter: []
  },
  // prescriptions: {
  //   OptionsUtilization: [
  //     { value: 'Sebelum makan', title: 'Sebelum makan' },
  //     { value: 'Setelah makan', title: 'Setelah makan' },
  //     { value: 'Saat makan', title: 'Saat makan' },
  //     { value: 'Pemakaian luar', title: 'Pemakaian luar' },
  //     { value: 'Pada area lesi', title: 'Pada area lesi' },
  //     { value: 'Intravena(IV)', title: 'Intravena(IV)' },
  //     { value: 'Intramuscular(IM)', title: 'Intramuscular(IM)' },
  //     { value: 'Subcutaneus', title: 'Subcutaneus' },
  //     { value: 'Intraouseus', title: 'Intraouseus' },
  //     { value: 'other', title: 'Lainnya' },
  //   ],
  //   OptionsConsumeTime: [
  //     { value: 'Pagi', title: 'Pagi' },
  //     { value: 'Siang', title: 'Siang' },
  //     { value: 'Malam', title: 'Malam' },
  //     { value: 'Pagi-Malam', title: 'Pagi-Malam' },
  //     { value: 'Pagi-Siang-Malam', title: 'Pagi-Siang-Malam' },
  //   ],
  //   OptionsSignatura: [
  //     { value: '1x1/2', title: '1x1/2' },
  //     { value: '1x1', title: '1x1' },
  //     { value: '1x2', title: '1x2' },
  //     { value: '1x3', title: '1x3' },
  //     { value: '1x4', title: '1x4' },
  //     { value: '1x5', title: '1x5' },
  //     { value: '2x1/2', title: '2x1/2' },
  //     { value: '2x1', title: '2x1' },
  //     { value: '2x2', title: '2x2' },
  //     { value: '3x1/2', title: '3x1/2' },
  //     { value: '3x1', title: '3x1' },
  //     { value: '3x2', title: '3x2' },
  //     { value: '3x3', title: '3x3' },
  //     { value: '4x1/2', title: '4x1/2' },
  //     { value: '4x1', title: '4x1' },
  //     { value: '4x2', title: '4x2' },
  //     { value: '4x3', title: '4x3' },
  //     { value: 'Pro injeksi', title: 'Pro injeksi' },
  //     { value: 'other', title: 'Lainnya' },
  //   ],
  //   OptionsTypePresc:[
  //     { value: 1, title: 'Resep Tambahan' },
  //     { value: 2, title: 'Resep Pulang' },
  //     { value: 4, title: 'DO' }
  //   ],
  //   OptionsDepo:[
  //     { value: '297c44f0-12ef-40e6-b9a7-c831065b3372', title: 'APOTIK CENTRAL' },
  //     { value: '822d71e8-7a2b-4fc6-a372-40a6b458d8c3', title: 'FARMASI OK' },
  //   ],
  // },
  // anamnesa: {
  //   OptionAnamnesa: [
  //     { value: 1, title: 'Auto Anamnesa' },
  //     { value: 2, title: 'Allo Anamnesa' },
  //   ],
  //   OptionFamily: [
  //     { value: 1, title: 'Ayah' },
  //     { value: 2, title: 'Ibu' },
  //     { value: 3, title: 'Kakak' },
  //     { value: 4, title: 'Adik' },
  //   ],
  // },
  vitals: {
    OptionsOxygen: [
      // { value: 1, title: 'Ya' },
      { value: 2, title: 'Room air' }, // ews normal 0
      { value: 3, title: 'Nasal kanul (NK)' }, // ews 2
      { value: 4, title: 'Simple mask (SM)' }, // ews 2
      { value: 5, title: 'Rebreathing mask (RM)' }, // ews 2
      { value: 6, title: 'Non-rebreathing mask (NRM)' }, // ews 2
      { value: 7, title: 'Laryngeal mask airways (LMA)' }, // ews 2
      { value: 8, title: 'Endotracheal Tube (ETT)' }, // ews 2
      { value: 9, title: 'Tracheostomy (TC)' }, // ews 2
      { value: 10, title: 'NIV' }, // ews 2
      { value: 11, title: 'HFNC' }, // ews 2
      { value: 12, title: 'CPAP' }, // ews 2
    ],
    OptionsOxygenPoint: [
      { value: 2, title: 'RA' },
      { value: 3, title: 'NK' },
      { value: 4, title: 'SM' },
      { value: 5, title: 'RM' },
      { value: 6, title: 'NRM' },
      { value: 7, title: 'LMA' },
      { value: 8, title: 'ETT' },
    ],
    OptionsAware: [
      { value: 0, title: 'Alert (A)' },
      { value: 1, title: 'Voice (V)' },
      { value: 2, title: 'Pain (P)' },
      { value: 3, title: 'Unresponsive (U)' },
    ],
    OptionsAwarePoint: [
      { value: 0, title: 'A' },
      { value: 1, title: 'V' },
      { value: 2, title: 'P' },
      { value: 3, title: 'U' },
    ],
    OptionGcsEye: [
      { value: 4, title: '(4) Dapat membuka mata secara mandiri' },
      { value: 3, title: '(3) Hanya membuka mata saat diminta, dan tetap tertutup jika tidak' },
      { value: 2, title: '(2) Mata hanya terbuka sebagai respons terhadap tekanan' },
      { value: 1, title: '(1) Mata tidak terbuka sama sekali' },
    ],
    OptionGcsVerbal: [
      { value: 5, title: '(5) Punya orientasi baik karena mampu menjawab siapa, di mana, serta tanggal atau tahun' },
      { value: 4, title: '(4) Mengalami kebingungan, namun masih bisa menjawab meski kurang paham situasi' },
      { value: 3, title: '(3) Bisa berbicara, tetapi respons tidak masuk akal' },
      { value: 2, title: '(2) Hanya bisa membuat suara, tetapi tidak berbicara' },
      { value: 1, title: '(1) Tidak bisa berbicara atau membuat suara sama sekali' },
    ],
    OptionGcsMotoric: [
      { value: 6, title: '(6) Mampu mengikuti instruksi kapan dan bagaimana harus bergerak' },
      { value: 5, title: '(5) Bergerak menjauh dari tekanan yang dirasakan secara sengaja' },
      { value: 4, title: '(4) Bergerak sebagai refleks terhadap tekanan' },
      { value: 3, title: '(3) Otot menegang (menarik ke dalam) saat merasakan tekanan' },
      { value: 2, title: '(2) Otot meregang (mengulur ke luar) sebagai respons terhadap tekanan' },
      { value: 1, title: '(1) Tidak merespons atau bergerak sama sekali' },
    ],
  },
  generalis: {
    OptionsHeadCircumference: [
        { value: 1, title: 'Normocefalus' },
        { value: 2, title: 'Microcefalus' },
        { value: 3, title: 'Hidrocefalus' },
    ],

    OptionsHeadHairColor: [
        { value: 1, title: 'Rambut hitam tebal' },
        { value: 2, title: 'Rambut pirang' },
        { value: 3, title: 'Rambut putih' },
    ],

    OptionsHeadHairCondition: [
        { value: 1, title: 'Tidak mudah dicabut' },
        { value: 2, title: 'Mudah dicabut' },
    ],

    OptionsEyeConjunctiva: [
        { value: 1, title: 'Conjunctiva anemis (-/-)' },
        { value: 2, title: 'Conjunctiva anemis (+/+)' },
    ],

    OptionsEyeSclera: [
        { value: 1, title: 'Sklera ikterik (-/-)' },
        { value: 2, title: 'Sklera ikterik (+/+)' },
    ],

    OptionsEyePupil: [
        { value: 1, title: 'Pupil isokor' },
        { value: 2, title: 'Pupil anisokor' },
    ],

    OptionsEyeReflect: [
        { value: 1, title: 'Reflek cahaya (+/+)' },
        { value: 2, title: 'Reflek cahaya (-/-)' },
    ],

    OptionsEyeBlindColor: [
        { value: 1, title: 'Tidak Buta Warna' },
        { value: 2, title: 'Buta Warna Parsial' },
        { value: 3, title: '⁠Buta Warna Total' },
    ],

    OptionsMouthMukosa: [
        { value: 1, title: 'Mukosa basah' },
        { value: 2, title: 'Mukosa kering' },
    ],

    OptionsMouthHiperemis: [
        { value: 1, title: 'Tidak hiperemis' },
        { value: 2, title: 'Hiperemis' },
    ],

    OptionsMouthTongue : [
        { value: 1, title: 'Mukosa & lidah bersih' },
        { value: 2, title: 'Mukosa & lidah kotor' },
        { value: 3, title: 'Terdapat leukoplakia' },
    ],

    OptionsMouthKaries : [
        { value: 1, title: 'Karies gigi (-)' },
        { value: 2, title: 'Karies gigi (+)' },
    ],

    OptionsNeckKGB: [
        { value: 1, title: 'Pembesaran KGB (-)' },
        { value: 2, title: 'Pembesaran KGB (+)' },
    ],

    OptionsNeckTrakea: [
        { value: 1, title: 'Trakea simetris' },
        { value: 2, title: 'Trakea asimetris' },
    ],

    OptionsNeckMassa: [
        { value: 1, title: 'Tidak teraba massa ' },
        { value: 2, title: 'Teraba massa simetris ' },
        { value: 3, title: 'Teraba massa asimetris ' },
    ],

    OptionsNeckJVP: [
      { value: 1, title: 'Peningkatan JVP (-)' },
      { value: 2, title: 'Peningkatan JVP (+)' },
    ],

    OptionsInspectionChest: [
        { value: 1, title: 'Dada simetris, statis & dinamis' },
        { value: 2, title: 'Dada asimetris' },
        { value: 3, title: 'Barrel Chest' },
    ],

    OptionsInspectionRetraction: [
        { value: 1, title: 'Tidak ada retraksi sela iga' },
        { value: 2, title: 'Terdapat retraksi sela iga' },
    ],

    OptionsPalpationChest: [
        { value: 1, title: 'Pengembangan dinding dada simetris' },
        { value: 2, title: 'Pengembangan dinding dada asimetris' },
    ],

    OptionsPalpationFremitus: [
        { value: 1, title: 'Fremitus normal' },
        { value: 2, title: 'Fremitus kanan lebih keras' },
        { value: 3, title: 'Fremitus kiri lebih keras' },
    ],

    OptionsPalpationKrepitasi: [
        { value: 1, title: 'Krepitasi (-)' },
        { value: 2, title: 'Krepitasi (+)' },
    ],

    OptionsAuskultationPulmoVasikuler: [
        { value: 1, title: 'Vesikular (+/+)' },
        { value: 3, title: 'Vesikular (-/-)' },
        { value: 4, title: 'Vesikular (+/-)' },
        { value: 5, title: 'Vesikular (-/+)' },
        { value: 2, title: 'Bronchovesikular (+/+)' },
    ],

    OptionsAuskultationPulmoRhonki: [
        { value: 1, title: 'Rhonki (-/-)' },
        { value: 2, title: 'Rhonki (+/+)' },
        { value: 3, title: 'Rhonki (-/+)' },
        { value: 4, title: 'Rhonki (+/-)' },
    ],

    OptionsAuskultationPulmoWheezing: [
        { value: 1, title: 'Wheezing (-/-)' },
        { value: 2, title: 'Wheezing (+/+)' },
        { value: 3, title: 'Wheezing (-/+)' },
        { value: 4, title: 'Wheezing (+/-)' },
    ],

    OptionsAuskultationCORBJ: [
        { value: 1, title: 'Bunyi jantung 1 & 2 reguler' },
        { value: 2, title: 'Bunyi jantung 1 & 2 irreguler' },
    ],

    OptionsAuskultationCORMurmur: [
        { value: 1, title: 'Murmur (-)' },
        { value: 2, title: 'Murmur (+)' },
    ],

    OptionsAuskultationCORGallop: [
        { value: 1, title: 'Gallop (-)' },
        { value: 2, title: 'Gallop (+)' },
    ],

    OptionsAbdomenSupel: [
        { value: 1, title: 'Supel' },
        { value: 2, title: 'Buncit' },
        { value: 3, title: 'Kempis' },
    ],

    OptionsAbdomenBising: [
        { value: 1, title: 'Bising usus (dbn)' },
        { value: 2, title: 'Bising usus menurun' },
        { value: 3, title: 'Bising usus meningkat' },
    ],

    OptionsAbdomenPress: [
        { value: 1, title: 'Nyeri Tekan (-)' },
        { value: 2, title: 'Nyeri Tekan ulu hati (+)' },
        { value: 3, title: 'Nyeri Tekan Kanan atas (+)' },
        { value: 4, title: 'Nyeri Tekan Kanan bawah (+)' },
        { value: 5, title: 'Nyeri Tekan perut bawah (+)' },
        { value: 6, title: 'Nyeri Tekan kiri bawah (+)' },
    ],

    OptionsAbdomenHepar: [
        { value: 1, title: 'Hepar & Lien tidak teraba' },
        { value: 2, title: 'Teraba pembesaran hepar' },
        { value: 3, title: 'Teraba pembesaran Lien' },
    ],

    OptionsEkstremitasAkral: [
        { value: 1, title: 'Akral hangat' },
        { value: 2, title: 'Akral dingin' },
    ],

    OptionsEkstremitasCRT: [
        { value: 1, title: 'CRT < 2 detik' },
        { value: 2, title: 'CRT ≥ 2 detik' },
    ],

    OptionsEkstremitasEdema: [
        { value: 1, title: 'Edema (-)' },
        { value: 2, title: 'Edema keempat eksteimitas (+)' },
        { value: 3, title: 'Edema ekstremitas atas (+/+)' },
        { value: 4, title: 'Edema ekstremitas atas (+/-)' },
        { value: 5, title: 'Edema ekstremitas atas (-/+)' },
        { value: 6, title: 'Edema ekstremitas bawah (+/+)' },
        { value: 7, title: 'Edema ekstremitas bawah (+/-)' },
        { value: 8, title: 'Edema ekstremitas bawah (-/+)' },
    ],

    OptionsOftalVisus: [
        { value: 1, title: '6/60' },
        { value: 2, title: '5/60' },
        { value: 3, title: '4/60' },
        { value: 4, title: '3/60' },
        { value: 5, title: '2/60' },
        { value: 6, title: '1/60' },
        { value: 7, title: '6/36' },
        { value: 8, title: '6/24' },
        { value: 9, title: '6/18' },
        { value: 10, title: '6/12' },
        { value: 11, title: '6/9' },
        { value: 12, title: '6/6' },
        { value: 13, title: '6/5' },
        { value: 14, title: '6/4' },
        { value: 15, title: '6/3' },
        { value: 16, title: '6/2' },
        { value: 17, title: '6/1' },
        { value: 18, title: '1/∞' },
        { value: 19, title: 'NLP' },
    ],

    OptionsOftalTIO: [
        { value: 1, title: 'Normal/palpasi' },
        { value: 2, title: 'Meningkat/palpasi' },
    ],

    OptionsOftalEyePosition: [
        { value: 1, title: 'Orthoporia' },
        { value: 2, title: 'Exothrophia' },
        { value: 3, title: 'Esothrophia' },
        { value: 4, title: 'Exophoria' },
        { value: 5, title: 'Esophoria' },
    ],

    OptionsOftalMovement: [
        { value: 1, title: 'Baik ke segala arah' },
        { value: 2, title: 'Paresis nervus 3' },
        { value: 3, title: 'paresis nervus 4' },
        { value: 4, title: 'paresis nervus 6' },
    ],

    OptionsOftalSuperSilia: [
        { value: 1, title: 'Normal' },
        { value: 2, title: 'Tidak normal' },
    ],

    OptionsOftalPalpebraEdema: [
        { value: 1, title: 'Edema (-)' },
        { value: 2, title: 'Edema (+)' },
    ],

    OptionsOftalPalpebraPtosis: [
        { value: 1, title: 'Ptosis (-)' },
        { value: 2, title: 'Ptosis (+)' },
    ],

    OptionsOftalPalpebraPtosisMCU: [
        { value: 1, title: '(-)' },
        { value: 2, title: '(+)' },
    ],

    OptionsOftalPalpebraLagophtalmus: [
        { value: 1, title: 'Lagophtalmus (-)' },
        { value: 2, title: 'Lagophtalmus (+)' },
    ],

    OptionsOftalPalpebraLagophtalmusMCU: [
        { value: 1, title: '(-)' },
        { value: 2, title: '(+)' },
    ],

    OptionsOftalPalpebraTumor: [
        { value: 1, title: 'Tumor (-)' },
        { value: 2, title: 'Tumor (+)' },
    ],

    OptionsOftalApparetus: [
        { value: 1, title: 'Laksrimasi (-)' },
        { value: 2, title: 'Laksrimasi (+)' },
    ],

    OptionsOftalSilia: [
        { value: 1, title: 'Sekret (-)' },
        { value: 2, title: 'Sekret (+)' },
    ],

    OptionsOftalConjHiperemis: [
        { value: 1, title: 'Hiperemis (-)' },
        { value: 2, title: 'Hiperemis (+)' },
    ],

    OptionsOftalConjBleed: [
        { value: 1, title: 'Pendarahan sub konjungtiva (-)' },
        { value: 2, title: 'Pendarahan sub konjungtiva (+)' },
    ],

    OptionsOftalKornea: [
        { value: 1, title: 'Jernih' },
        { value: 2, title: 'Keruh Sebagian' },
        { value: 3, title: 'Keruh' },
    ],

    OptionsOftalAnterior: [
        { value: 1, title: 'Dalam' },
        { value: 2, title: 'Dangkal' },
    ],

    OptionsOftalIrisColor: [
        { value: 1, title: 'Biru' },
        { value: 2, title: 'Coklat' },
        { value: 3, title: 'Hitam' },
    ],

    OptionsOftalIrisKripte: [
      { value: 1, title: 'Kripte (+/Jernih)' },
      { value: 2, title: 'Kripte (-/Keruh)' },
    ],

    OptionsOftalPupilForm: [
        { value: 1, title: 'Regular' },
        { value: 2, title: 'Irregular' },
    ],

    OptionsOftalPupilDiameter: [
        { value: 1, title: '1 mm' },
        { value: 2, title: '2 mm' },
        { value: 3, title: '3 mm' },
        { value: 4, title: '4 mm' },
        { value: 5, title: '5 mm' },
        { value: 6, title: '6 mm' },
        { value: 7, title: '7 mm' },
        { value: 8, title: '8 mm' },
        { value: 9, title: '9 mm' },
    ],

    OptionsOftalPupilDirectLight: [
        { value: 1, title: 'RCL (+)' },
        { value: 2, title: 'RCL (-)' },
    ],

    OptionsOftalPupilIndirectLight: [
        { value: 1, title: 'RCTL (+)' },
        { value: 2, title: 'RCTL (-)' },
    ],

    OptionsOftalLens: [
        { value: 1, title: 'Jernih' },
        { value: 2, title: 'Keruh Sebagian' },
        { value: 3, title: 'Keruh' },
    ],

    OptionsOftalGlandula: [
        { value: 1, title: 'Pembesaran (+)' },
        { value: 2, title: 'Penyempitan (-)' },
    ],

    OptionsOftalLapang: [
        { value: 1, title: 'Normal' },
        { value: 2, title: 'Menyempit' },
        { value: 3, title: 'Scotoma central' },
        { value: 4, title: 'Tertutup tirai' },
    ],

    OptionsOftalShadow: [
        { value: 1, title: 'Positif' },
        { value: 2, title: 'Negatif' },
    ],
  }
})

// getters
const getters = {}

// actions
const actions = {
  getSoapList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.limit != undefined) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.offset != undefined) {
        queryParams.push(`offset=${params.offset}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.not_registration_id) {
        queryParams.push(`not_registration_id=${params.not_registration_id}`)
      }

      if (params.registration_id) {
        queryParams.push(`registration_id=${params.registration_id}`)
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.is_initial_inpatient === 1 || params.is_initial_inpatient === 0) {
        queryParams.push(`is_initial_inpatient=${params.is_initial_inpatient}`)
      }

      if (params.is_booking === 1 || params.is_booking === 0) {
        queryParams.push(`is_booking=${params.is_booking}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/soap/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getSoapDetail ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.not_registration_id) {
        queryParams.push(`not_registration_id=${params.not_registration_id}`)
      }

      if (params.registration_id) {
        queryParams.push(`registration_id=${params.registration_id}`)
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.is_initial_inpatient === 1 || params.is_initial_inpatient === 0) {
        queryParams.push(`is_initial_inpatient=${params.is_initial_inpatient}`)
      }

      if (params.is_booking === 1 || params.is_booking === 0) {
        queryParams.push(`is_booking=${params.is_booking}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/soap/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  putBpjsStatus ({ commit }, formData) {
		return new Promise((resolve, reject) => {

      const param = { rawData: formData, url: 'mr/soap/update_status_bpjs', id: formData.registration_destination_id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  putSections ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.data

      const fixed_url = {
          ANAMNESA: 'mr/soap/save_anamnesis',
          VITAL: 'mr/soap/save_ttv_antropometri',
          GENERALIS: 'mr/soap/save_st_generalist',
          DIAGNOSIS: 'mr/soap/save_diagnose',
          PROCEDURE: 'mr/soap/save_procedure',
          LABORATORY: 'mr/soap/save_laboratory_request',
          RADIOLOGY: 'mr/soap/save_radiology_request',
          PHYSIOTHERAPY: 'mr/soap/save_physiotherapy_request',
          PRESC: 'mr/soap/save_prescription',
          DISCHARGE: 'mr/soap/save_discharge_planning',
          HEMODIALYSIS: 'mr/soap/save_hemodialysis_initiation',
          // REHABILITATION: 'mr/soap/save_medical_rehabilitation',
          REHABILITATION: 'mr/soap/save_function_test',
          CHECKUP: 'mr/soap/save_medical_check_up',
      };

      let url= ''
      if (fixed_url[formData.exist]) {
          url = fixed_url[formData.exist]
      }

      const param = { rawData: rawData, url: url }
      useIPA.putonly(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  getHistorySOAP ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/soap/history?${paramsall}`}

      useIPA.get(param)
      .then(response => {
        resolve(response.data.data)
        return response;
      })
      .catch(({response}) => {
        reject({
          message: response.data.message,
          debug: response.data.debug,
          response: response.data,
        })
      })
    })
  },

  getSoapSList ({ commit}, id ) {
    return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap_s/list?offset=0&limit=25&registration_destination_id='  + id}
      useIPA.get(param).then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
  },
  getSoapAList ({ commit}, id ) {
    return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap_a/list?offset=0&limit=25&soap_id='  + id}
      useIPA.get(param).then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
  },
  getDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.is_booking === 1 || params.is_booking === 0) {
        queryParams.push(`is_booking=${params.is_booking}`)
      }

      if (params.is_initial_inpatient === 1 || params.is_initial_inpatient === 0) {
        queryParams.push(`is_initial_inpatient=${params.is_initial_inpatient}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/soap/detail?${paramsall}`}

      // useIPA.get(param).then(response => {
      //   resolve(response.data.data)
      // })
      useIPA.get(param)
          .then(response => {
              if (response.data.hasErrors) {
                  reject(response.data.message)
              } else {
                  resolve(response.data.data)
              }
              return response
          })
          .catch(({ response }) => {
              reject({ message: response.data.message, debug: response.data.debug, response: response.data, })
          })
    })
  },

  getAll ({ commit, id }) {
    let param = {status: 'mr/soap/detail?registration_destination_id='  + id}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getBySoapId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/detail/' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getDetailResumeById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/detail/' + id + '?is_resume_exit=1'}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByRegId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/detail?registration_destination_id='  + id+'&is_booking=0'}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getInpatientByRegId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/detail_inpatient?registration_destination_id='  + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  pdf_to_base64 ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/pdf_to_base64/'  + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  pdf_to_base64_lab ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/pdf_to_base64_lab/'  + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  ExaminationPdfToBase64 ({ commit }, url) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/soap_examination/pdf_to_base64/${url}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNorm ({ commit }, norm) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/detail?norm=' + norm}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByRegistrationId ({ commit }, registration_id) {
    // console.log('registration_id:',registration_id);
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/detail?registration_id=' + registration_id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByResumeExit ({ commit }, norm) {
    // console.log('registration_id:',registration_id);
    // console.log('hitttedd');
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap/detail?is_resume_exit=1&norm=' + norm}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getBySoapAdultId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/soap_adult/detail?soap_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getById ({ commit }, id) {
    let param = {status: 'mr/soap/detail?registration_destination_id='  + id}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getByRM ({ commit }, norm) {
    let param = {status: 'mr/soap/detail?norm='  + norm}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getByRMLimit ({ commit }, norm) {
    let param = {status: 'mr/soap/detail?limit=5&norm='  + norm}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getSoapDoctorById ({ commit }, id) {
    let param = {status: 'mr/soap/detail?registration_destination_id='  + id}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setDataSoapDoctor', {...patient})
    })
  },
  getSoapDoctorLetterById ({ commit }, id) {
    let param = {status: 'mr/soap/detail?registration_destination_id='  + id}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setDataSoapDoctorLetter', {...patient})
    })
  },
  getSoapPrescriptionById ({ commit }, id) {
    let param = {status: 'pharmacy/prescription/detail?registration_destination_id='  + id}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setDataSoapPrescription', {...patient})
    })
  },
  getByRegistrationIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_doctor/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  getByIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_doctor/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  getTransactionBySoapId ({ commit }, soap_id ) {
		return new Promise((resolve, reject) => {
      let param = {status: 'tr/transaction_doctor/detail/?soap_id=' + soap_id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getTransaction ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];
      if (params.soap_id) {
        queryParams.push(`soap_id=${params.soap_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `tr/transaction_doctor/detail/?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  soapResumePut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      // const rawData = formData
      const rawData = {
        registration_destination_id: formData.registration_destination_id,
        doctor_in_charge_id: formData.doctor_in_charge_id,
        significant_medications: formData.significant_medications,
        take_home_medications: formData.take_home_medications,
        exit_id: formData.exit_id,
        exit_anythingelse: formData.exit_anythingelse,
        exit_condition_type_id: formData.exit_condition_type_id,
        exit_condition_anythingelse: formData.exit_condition_anythingelse,
        care_instruction_control: formData.care_instruction_control,
        care_instruction_control_date: formData.care_instruction_control_date,
        care_instruction_anythingelse: formData.care_instruction_anythingelse,
        is_resume_exit: 1,
        summary_medical_history: formData.summary_medical_history,
        physical_check: formData.physical_check,
        home_education: formData.home_education

        // transfer_reason: formData.transfer_reason,
      }
      // console.log(rawData)
      const param = { rawData: rawData, url: 'mr/soap/resume_update', id: formData.soap_id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  putOphtal ({ commit }, formData) {
		return new Promise((resolve, reject) => {

      const param = { rawData: formData.ophtal, url: 'mr/soap/update_ophthal/', id: formData.soap_id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},
  formPostFull ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // console.log(rawData)
      const param = { rawData: rawData, url: 'mr/soap/create_full', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  getSoapP ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];
      if (params.soap_id) {
        queryParams.push(`soap_id=${params.soap_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/soap_p/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostPutSoapP ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/soap_p/update' : url = 'mr/soap_p/create'

      const param = { rawData: rawData, url: url, id: formData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},
  formPostPutDoang ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/soap/update' : url = 'mr/soap/create_soap'

      const param = { rawData: rawData, url: url, id: formData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},
  formPostInPatient ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/soap/update_inpatient' : url = 'mr/soap/create_inpatient'

      const param = { rawData: rawData, url: url, id: formData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},
  postPutFullBook ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url= 'mr/soap/update_book' : url= 'mr/soap/create_full_book'

      const param = { rawData: rawData, url: url, id: formData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'mr/soap/update' : 'mr/soap/create_full'
      const param = { rawData: rawData, url: url, id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},
  formUpdate ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // console.log(rawData)
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/soap/update', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'RegisMasterView' })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},
  canvas ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // console.log(rawData)
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData.form, url: 'mr/soap/canvas', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'RegisMasterView' })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  formPostPutPrescription ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'pharmacy/prescription/create'
      if (rawData.view === 'edit') {
        url = 'pharmacy/prescription/update'
      }
      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
			.then(response => {
        router.push({ name: 'PatientAlkesView' })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},
  formPostPutPrescriptionModule ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'pharmacy/prescription/create'
      if (rawData.view === 'edit') {
        url = 'pharmacy/prescription/update'
      }
      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
			.then(response => {
        router.push({ name: 'PatientPrescriptionView' })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  getExaminationCategory ({ commit }, params) {
    let queryParams = [];

    if (params?.health_facility_item_id) {
      queryParams.push(`health_facility_item_id=${params?.health_facility_item_id}`);
    }

    let paramsall = queryParams.join('&')
    let param = {status: `mr/category_examination/list?${paramsall}`}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setExaminationCategory', {...patient})
    })
  },
  postPutExamination ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = new FormData()
      rawData.set('file', formData.file)
      rawData.set('examination_date', formData.examination_date)
      rawData.set('category_examination_id', formData.category_examination_id)
      rawData.set('soap_id', formData.soap_id)
      rawData.set('expertise', formData.expertise)
      rawData.set('id', formData.id)

      let url = formData.id ? 'mr/soap_examination/update' : 'mr/soap_examination/upload'

      const param = { rawData: rawData, url: url, id: formData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  updateExaminationFile({ commit }, { id, params }) {
    // console.log('updateExaminationFile', id, params);
    return new Promise((resolve, reject) => {
      let url = `mr/soap_examination/update_file`;
      const param = {
        rawData: params,
        url: url,
        id: id
      };
      useIPA.postput(param)
        .then(response => {
          if (response.data.hasErrors) {
            reject(response.data.message);
          } else {
            resolve(response.data);
          }
        })
        .catch(({ response }) => {
          reject({ message: response.data.message, debug: response.data.debug, response: response.data });
        });
    });
  },

    updateExaminationImage({ commit }, { id, params }) {
      return new Promise((resolve, reject) => {
          let url = `mr/soap_examination/update_image`;
          const formData = new FormData();
          formData.append('file', params.file);
          if (params.file_code) {
              formData.append('file_code', params.file_code);
          }
          // Add other fields if needed
          // console.log('updateExaminationImage id and params', id);
          // console.log('updateExaminationImage formData', formData);
          // console.log('updateExaminationImage url', url);
          useIPA.postid({
              rawData: formData,
              url: url,
              id: id
          })
          .then(response => {
              if (response.data.hasErrors) {
                  reject(response.data.message);
              } else {
                  resolve(response.data);
              }
          })
          .catch(({ response }) => {
              reject({ message: response.data.message, debug: response.data.debug, response: response.data });
          });
      });
  },

  getExamination ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.soap_id) {
        queryParams.push(`soap_id=${params.soap_id}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.registration_id) {
        queryParams.push(`registration_id=${params.registration_id}`)
      }

      let paramsall = queryParams.join('&')

      let param = {status: `mr/soap_examination/detail/?${paramsall}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getExaminationList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      let paramsall = queryParams.join('&')

      let param = {status: `mr/soap_examination/list/?${paramsall}`}
      useIPA.get(param)
      .then(response => {
        resolve(response.data.data)
        return response;
      })
      .catch(({response}) => {
        reject({
          message: response.data.message,
          debug: response.data.debug,
          response: response.data,
        })
      })
    })
  },

  removeExamination ({ commit }, obj) {
    // console.log(obj);
		return new Promise((resolve, reject) => {
      const rawData = {
        file_code: obj.file_code,
			}
      const param = { rawData: rawData, url: 'mr/soap_examination/delete_file', id: obj.examination_id }
      useIPA.delete(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        if (error.response.data.hasOwnProperty('hasErrors')) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  removeImage ({ commit }, { file_code, examination_id }) {
		return new Promise((resolve, reject) => {
      const rawData = { file_code };
      const param = { rawData: rawData, url: 'mr/soap_examination/delete_image', id: examination_id };
      useIPA.delete(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
      .catch(error => {
          if (error.response && error.response.data && error.response.data.hasErrors) {
              reject(error.response.data.message);
          } else {
              reject(error.message);
          }
      });
		})
	},

  getDoctorProcedureStateList ({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
      queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`);
    }

    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    } else if (params.limit === 0) {
      queryParams.push(`limit=${params.limit}`);
    }

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`);
    }

    if (params.registration_id) {
      queryParams.push(`registration_id=${params.registration_id}`);
    }

    let paramsall = queryParams.join('&')
    let param = {status: `tr/transaction_doctor/list?${paramsall}`}

    useIPA.get(param).then(response => {
      const data = response.data.data
      commit('setDoctorProcedure', {...data})
    })
  },

  postPutTransaction ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'tr/transaction_doctor/update' : 'tr/transaction_doctor/create'

      const param = { rawData: rawData, url: url, id: formData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  deleteTransaction ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'tr/transaction_doctor/delete', id: id }
      useIPA.delete(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},

  // formPostPutTransaction ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = formData

  //     let url = 'tr/transaction_doctor/create'
  //     if (rawData.view === 'edit') {
  //       url = 'tr/transaction_doctor/update'
  //     }
  //     const param = { rawData: rawData, url: url, id: rawData.id }
  //     useIPA.postput(param)
  //     .then(response => {
  //       if (response.data.hasErrors) {
  //         reject(response.data.message)
  //       } else {
  //         resolve(response.data)
  //       }
  //       return response;
  //     })
  //     .catch(({response}) => {
  //       reject({message: response.data.message, debug: response.data.debug, response})
  //     })
	// 	})
	// },

  getBhpStateList ({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
      queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`);
    }

    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    } else if (params.limit === 0) {
      queryParams.push(`limit=${params.limit}`);
    }

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`);
    }

    if (params.registration_id) {
      queryParams.push(`registration_id=${params.registration_id}`);
    }

    let paramsall = queryParams.join('&')
    let param = {status: `pharmacy/prescription/list_bhp?${paramsall}`}
    // let param = {status: `pharmacy/prescription/list?${paramsall}`}

    useIPA.get(param).then(response => {
      const data = response.data.data
      commit('setBhpList', {...data})
    })
  },

  updateExaminationFile({ commit }, { id, params }) {
    // console.log('updateExaminationFile', id, params);
    return new Promise((resolve, reject) => {
      let url = `mr/soap_examination/update_file`;
      const param = {
        rawData: params,
        url: url,
        id: id
      };
      useIPA.postput(param)
        .then(response => {
          if (response.data.hasErrors) {
            reject(response.data.message);
          } else {
            resolve(response.data);
          }
        })
        .catch(({ response }) => {
          reject({ message: response.data.message, debug: response.data.debug, response: response.data });
        });
    });
  },

  postPutPrescription ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      let url = formData.id ? 'pharmacy/prescription/update' : 'pharmacy/prescription/create'

      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},
}

// mutations
const mutations = {
  setData (state, patient) {
    state.items = patient.rows
    state.totalRows = patient.rows.length
  },
  setDataSoapPrescription (state, prescription) {
    // console.log(prescription)
    state.prescription.items = prescription.rows
    state.prescription.totalRows = prescription.rows.length
  },
  setDataSoapDoctor (state, data) {
    state.options.plandoctor = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i]['soap_p_id'] } else if (key === 'employee_doctor_name') { option['title'] = data.rows[i][key]+'    '+(data.rows[i]['icd10'] ? data.rows[i]['icd10'][0]['code']+'-'+data.rows[i]['icd10'][0]['name'] : '') } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.plandoctor.push(Object.assign({}, option))
    }
  },
  setExaminationCategory (state, data) {
    state.options.examination_category = []
    const option = []
    const fixdata = data.rows.rows

    for (let i = 0; i < fixdata.length; i++) {
      let temp_obj = { value: fixdata[i].id , title: fixdata[i].name}
      state.options.examination_category.push(Object.assign({}, temp_obj))
    }
  },
  setDataSoapDoctorLetter (state, data) {
    state.options.plandoctor = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'employee_doctor_name') { option['title'] = data.rows[i][key]+'    '+(data.rows[i]['icd10'] ? data.rows[i]['icd10'][0]['code']+'-'+data.rows[i]['icd10'][0]['name'] : '') } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.plandoctorletter.push(Object.assign({}, option))
    }
  },
  setDataTransaction (state, transaction) {
    state.itemstransaction = []
    state.itemstransaction = transaction.rows.sort((a, b) => new Date(a.date_added) - new Date(b.date_added))
  },

  setDoctorProcedure (state, res) {
    state.procedure.items = res.rows
    state.procedure.totalRows = res.iTotal
  },

  setBhpList (state, res) {
    state.bhp.items = res.rows
    state.bhp.totalRows = res.iTotal
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
