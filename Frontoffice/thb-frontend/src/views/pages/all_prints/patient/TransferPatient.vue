<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" @click="handleBack" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header d-flex px-3 mt-16">
                        <img src="@/@core/assets/images/nurse/zeemar-logo.png" alt="rs-logo"> <br>
                        <div class="ml-3">
                            <p class="text-regular-12">Jl.Raya Jakarta 11520</p>
                            <p class="text-regular-12">Telp: (021) 12345678</p>
                            <p class="text-regular-12">Email: info@gmail.com</p>
                            <p class="text-regular-12">Website: www.website.com</p>
                        </div>
                    </div>
                    <div class="mt-40">
                        <div class="row mx-0">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.regdata.name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl. Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.regdata.birth_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">DPJP</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            <template v-if="this.transferdata.dpjp_doctor_in_charge_id === 1">
                                                {{ this.regdata.doctor_in_charge_name }}
                                            </template>
                                            <template else>
                                                {{ this.transferdata.doctor_in_charge_id_1.title }}
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Dokter Konsulen 1</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            <template v-if="this.transferdata.dpjp_doctor_in_charge_id === 1">
                                                {{ this.regdata.doctor_in_charge_name }}
                                            </template>
                                            <template else>
                                                {{ this.transferdata.doctor_in_charge_id_1.title }}
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Dokter Konsulen 2</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            <template v-if="this.transferdata.dpjp_doctor_in_charge_id === 1">
                                                {{ this.transferdata.doctor_in_charge_id_1.title }}
                                            </template>
                                            <template else>
                                                {{ this.regdata.doctor_in_charge_name }}
                                            </template>    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Diagnosis Masuk</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.regdata.icd10_code }} - {{ this.regdata.icd10_name }}</td>
                                    </tr>
                                    
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Jenis Kelamin</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.regdata.sex_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Masuk</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.regdata.register_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Ruang / Kamar</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.regdata.health_facility_item_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal / Jam Pindah</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.regdata.register_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Pindah Ke Ruang / Kamar</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.transferdata.health_facility_item_id.title }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Diagnosis Sekarang</td>
                                        <td class="px-3">:</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 mt-32">
                        <section class="card">
                            <div class="card-header text-center font-weight-bold">I. Ringkasan Riwayat Pasien</div>
                            <div class="card-body">
                                <small title="alarm" class="mt-2 text-break form-text text-muted" data-v-41be6633="">Anamnesis</small>
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                        <b-tr>
                                            <b-td class="border-0" style="width: 30%; height: 100px;"> Keluhan Utama </b-td>
                                            <b-td class="border-0" style="width: 30px;">:</b-td>
                                            <b-td class="border-0">{{ this.transferdata.subjective_main_complaint }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" style="width: 30%; height: 100px;">Riwayat Penyakit</b-td>
                                            <b-td class="border-0" style="width: 30px;">:</b-td>
                                            <b-td class="border-0">{{ this.transferdata.subjective_history_disease_past }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" style="width: 30%; height: 100px;">Pemeriksaan Fisik</b-td>
                                            <b-td class="border-0" style="width: 30px;">:</b-td>
                                            <b-td class="border-0">{{ this.transferdata.subjective_history_disease_now }}</b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                                <b-table-simple fixed small class="text-regular-14 mt-24">
                                
                                        <b-tr>
                                            <b-td class="border-0" style="width: 22%; height: 50px;" >Pemeriksaan Tanda Vital</b-td>
                                            <b-td class="border-0" style="width: 5%;">:</b-td>
                                        </b-tr>
                                    </b-table-simple>
                                <b-table-simple>
                                            <b-tr class="border-0">
                                            <b-td class="border-0"></b-td>
                                            <b-td class="border-0" style="width:20%"> Tensi</b-td>
                                            <b-td class="border-0" style="width:2%"> :</b-td>
                                            <b-td class="border-0" style="width:20%">{{ this.transferdata.systolic_blood_pressure }}</b-td>           
                                            <b-td class="border-0" style="width:20%"> mmHG</b-td>
                                            <b-td class="border-0" style="width:2%"> :</b-td>
                                            <b-td class="border-0" style="width:20%">{{ this.transferdata.diastolic_blood_pressure }} mmHG</b-td>
                                            </b-tr>
                                            <b-tr class="border-0">   
                                           <b-td class="border-0"></b-td>                                 
                                            <b-td class="border-0" style="width:20%"> Suhu</b-td>
                                            <b-td class="border-0" style="width:2%"> :</b-td>
                                            <b-td class="border-0" style="width:20%"> {{ this.transferdata.temperature }} ᵒC</b-td>                                           
                                            <b-td class="border-0" style="width:20%"> Nadi</b-td>
                                            <b-td class="border-0" style="width:2%"> :</b-td>
                                            <b-td class="border-0" style="width:20%"> {{ this.transferdata.pulse_frequency }} x/mnt</b-td>
                                            </b-tr>
                                    
                                    </b-table-simple>
                                        <b-table-simple fixed small class="text-regular-14 mt-12">
                                            <b-tr>
                                                <b-td class="border-0" style="width: 30%; height: 100px;">Keadaan Umum</b-td>
                                                <b-td class="border-0" style="width: 30px;">:</b-td>
                                                <b-td class="border-0">{{ this.transferdata.general_condition }}</b-td>
                                            </b-tr>
                                </b-table-simple>
                                <b-table-simple fixed small class="text-regular-14 mt-24">
                                            <b-tr>
                                                <b-td class="border-0" style="width: 30%; height: 100px;">Alasan Transfer</b-td>
                                                <b-td class="border-0" style="width: 30px;">:</b-td>
                                                <b-td class="border-0">{{ this.transferdata.transfer_reason }}</b-td>
                                            </b-tr>
                                </b-table-simple>
                                
                            </div>
                        </section>
                       
                        <section class="card mt-40">
                            <div class="card-header text-center font-weight-bold">II. PEMERIKSAAN YANG SUDAH DILAKUKAN</div>
                            <div class="card-body">
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                       <b-tr>
                                            <b-td>{{ this.transferdata.inspection_has }}</b-td>
                                       </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                               
                            </div>
                        </section>
                        <section class="card mt-40">
                            <div class="card-header text-center font-weight-bold">III. TINDAKAN MEDIS YANG SUDAH DILAKUKAN</div>
                            <div class="card-body">
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                       <b-tr>
                                            <b-td>{{ this.transferdata.medical_action_has }}</b-td>
                                       </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                               
                            </div>
                        </section>
                        <section class="card mt-40">
                            <div class="card-header text-center font-weight-bold">IV.  PEMBERIAN TERAPI YANG DI LAKUKAN</div>
                            <div class="card-body">
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                       <b-tr>
                                            <b-td style="width: 10%;">Infus :</b-td>    
                                            <b-td>{{ this.transferdata.Inspection }}</b-td>
                                       </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                        <b-tr>
                                            <b-td class="border-0" style="width: 15%;">Obat Injeksi :</b-td>    
                                            <b-td>{{ this.transferdata.injection_medication }}</b-td>
                                        </b-tr>
                                        <!-- <b-tr>
                                            <b-td class="border-0" ></b-td>
                                            <b-td class="border-0" >1.</b-td>
                                            <b-td class="border-0" >4.</b-td>                                                
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" ></b-td>
                                            <b-td class="border-0" >2.</b-td>
                                            <b-td class="border-0" >5.</b-td>                                                
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" ></b-td>
                                            <b-td class="border-0" >3.</b-td>
                                            <b-td class="border-0" >6.</b-td>                                                
                                        </b-tr> -->
                                    </b-tbody>
                                </b-table-simple>
                                <b-table-simple small class="text-regular-14">
                                    
                                </b-table-simple>
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                        <b-tr>
                                            <b-td class="border-0" style="width: 15%;">Obat Oral :</b-td>    
                                            <b-td>{{ this.transferdata.oral_medication }}</b-td>
                                        </b-tr>
                                        <!-- <b-tr>
                                            <b-td class="border-0" ></b-td>
                                            <b-td class="border-0" >1.</b-td>
                                            <b-td class="border-0" >5.</b-td>                                                
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" ></b-td>
                                            <b-td class="border-0" >2.</b-td>
                                            <b-td class="border-0" >6.</b-td>                                                
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" ></b-td>
                                            <b-td class="border-0" >3.</b-td>
                                            <b-td class="border-0" >7.</b-td>                                                
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" ></b-td>
                                            <b-td class="border-0" >4.</b-td>
                                            <b-td class="border-0" >8.</b-td>   -->
                                    </b-tbody>
                                </b-table-simple>
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                        <b-tr>
                                            <b-td class="border-0" style="width: 35%;"> Derajat Kebutuhan Perawatan Pasien:</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <td class="border-0"></td>
                                            <b-td class="border-0" style="width: 30%;">
                                                <b-form-checkbox-group size="sm">
                                                    <b-form-checkbox disabled>Derajat 0</b-form-checkbox>
                                                </b-form-checkbox-group>
                                                </b-td>
                                                <b-td class="border-0">
                                                        <b-form-checkbox-group size="sm">
                                                    <b-form-checkbox disabled>Derajat 1</b-form-checkbox>                                                    
                                                </b-form-checkbox-group>
                                            </b-td>
                                            </b-tr>
                                        <b-tr>
                                            <td class="border-0"></td>
                                            <b-td class="border-0" style="width: 30%;">
                                                <b-form-checkbox-group size="sm">
                                                    <b-form-checkbox disabled>Derajat 2</b-form-checkbox>
                                                </b-form-checkbox-group>
                                                </b-td>
                                                <b-td class="border-0">
                                                        <b-form-checkbox-group size="sm">
                                                    <b-form-checkbox disabled>Derajat 3</b-form-checkbox>                                                    
                                                </b-form-checkbox-group>
                                            </b-td>
                                            </b-tr>
                                        
                                    </b-tbody>
                                </b-table-simple>
                               
                            </div>
                        </section>
                        <section class="card mt-40">

                        <div class="card-body">
                            <b-table-simple small class="text-regular-14">
                                <b-tr>
                                    <b-td class="border-0">KATEGORI PASIEN TRANSFER</b-td>
                                    </b-tr>
                            </b-table-simple>
                            <b-table-simple small class="text-regular-14">
                                
                                <b-tbody>
                                    <b-tr>
                                        <b-td  style="width: 3%;" ></b-td>
                                        <b-td  >Level</b-td>
                                        <b-td  >Kategori</b-td>                                                
                                        <b-td  >Pendamping</b-td>                                                
                                        <b-td  >Peralatan</b-td>                                                
                                    </b-tr>
                                    <b-tr>
                                        <b-td  style="width: 3%;" ></b-td>                                                
                                        <b-td  style="width: 15%;">Derajat 0</b-td>
                                        <b-td  >Pasien Mebutuhkan Ruangan Perawatan Biasa</b-td>
                                        <b-td  >TPK/Petugas Keamanan</b-td>                                                
                                        <b-td  >Semua rekam medik,<br> hasil pemeriksaan penunjang,<br>format transfer internal</b-td>                                                
                                    </b-tr>
                                    <b-tr>
                                        <b-td  style="width: 3%;" ></b-td>                                                
                                        <b-td  style="width: 15%;">Derajat 1 </b-td>
                                        <b-td  >Pasien beresiko mengalami perburukan,<br>
                                        pasien baru pindah dari HCU/ICU,>
                                        pasien yang akan dirawat <br>
                                        diruang perawatan tim perawatan khusus.
                                        </b-td>
                                        <b-td  >Petugas PK I / <br> Petugas Keamanan</b-td>                                                
                                        <b-td  >Peralatan derajat 0+ tabung oksigen dan canul, <br> stand infus dan pulse oksimetri.</b-td>                                                
                                    </b-tr>
                                    <b-tr>
                                        <b-td  style="width: 3%;" ></b-td>                                                
                                        <b-td  style="width: 15%;">Derajat 2</b-td>
                                        <b-td  >Pasien memerlukan pengawasan <br> ketat atau intervensi khusus, mis: pada <br> pasien yang mengalami kegagalan satu <br>sistem organ.</b-td>
                                        <b-td  >Dokter/Perawat PKII</b-td>                                                
                                        <b-td  >Peralatan derajat 1, + bedside monitor, syringe pump.</b-td>                                                
                                    </b-tr>
                                    <b-tr>
                                        <b-td  style="width: 3%;" ></b-td>                                                
                                        <b-td  style="width: 15%;">Derajat 3</b-td>
                                        <b-td  >Pasien mengalami kegagalan multi organ  dan memerlukan bantuan hidup jangka <br>panjang ditambah dengan kebutuhan<br> akan alat bantu nafas.</b-td>
                                        <b-td  >Dokter/Perawat PKIII</b-td>                                                
                                        <b-td  >Peralatan derajat 2, + alat bantu nafas.</b-td>                                                
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                           
                        </div>
                    </section>
                     
                        <section class="card mt-40">
                            <div class="card-header text-center font-weight-bold">V. KONDISI PASIEN</div>
                            <div class="card-body">
                                <b-table-simple small class="text-regular-14">
                                    <tr>
                                        <b-td >Sebelum Teranfer</b-td>

                                    </tr>
                                    <b-thead>
                                        <tr>
                                            <b-td style="width: 50%;">Sebelum Teranfer</b-td>
                                            <b-td style="width: 50%;">Seudah Teranfer</b-td>
                                        </tr>
                                    </b-thead>
                                    <b-tbody>
                                        <b-tr>
                                            <b-td class="border-0" >Keadaan umum</b-td>
                                            <b-td class="border-0" >Keadaan umum</b-td>                                          
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" >Kesadaran</b-td>
                                            <b-td class="border-0" >Kesadaran</b-td>                                              
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" >Pemeriksaan Tanda Tanda Vital</b-td>
                                            <b-td class="border-0" >Pemeriksaan Tanda Tanda Vital</b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" >Tensi:             mmHg</b-td>
                                            <b-td class="border-0" >Tensi:             mmHg</b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" >Suhu        C</b-td>
                                            <b-td class="border-0" >Suhu        C</b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" >Nadi</b-td>
                                            <b-td class="border-0" >Nadi</b-td>
                                            </b-tr>
                                        <b-tr class="border-1">
                                            <b-td class="border-1" style="height:100px;" >Catatan Penting;</b-td>
                                            <b-td class="border-1" >Catatan Penting;</b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-1" >Petugas yang menyerahkan</b-td>
                                            <b-td class="border-1" >Petugas yang menerima</b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" style="text-align: center;" >Petugas Medis</b-td>
                                            <b-td class="border-0" style="text-align: center;" >Petugas Medis</b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0"style="height:60px;" ></b-td>
                                            <b-td class="border-0"style="height:60px;" ></b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" style="text-align: center;" >(..........................)</b-td>
                                            <b-td class="border-0" style="text-align: center;" >(..........................)</b-td>
                                            </b-tr>
                                    </b-tbody>
                                    
                                    
                                </b-table-simple>
                               
                            </div>
                        </section>
                
                       
                    </div>
              
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
export default {
    data() {
        return {
            regdata: JSON.parse(localStorage.getItem('regdata')),
            transferdata: JSON.parse(localStorage.getItem('transferdata')),
            nurseResume:{},
            nurseSubResume:{},
            regData:{}
        }
    },
    methods: {
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
        },
        handleBack() {
            this.$router.go(-1);
        },
        getData (id) {
			this.$store.dispatch('nurse/getByNurseId', id).then(data => {
                this.nurseResume = data[0]
		    	this.$store.dispatch('registration/getById', data[0].registration_destination_id).then(data => {
                    this.regData = data[0]
                })
                if (this.nurseResume.type_nurse_id === 1) {
                    this.$store.dispatch('nurse/getByNurseAdultId', id).then(data => {
                        this.nurseSubResume = data[0]
                        console.log(this.nurseSubResume)
                    })
                } else if (this.nurseResume.type_nurse_id === 2) {
                    this.$store.dispatch('nurse/getByNurseChildId', id).then(data => {
                        this.nurseSubResume = data[0]
                        console.log(this.nurseSubResume)
                    })
                }
            })
        }
    },
    mounted () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
};

</script>
<style>
@import './../../../../@core/css/main/main.min.css';

/* table,
th,
td {
    border: 1px solid black;
} */
td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
}

#canvas {
    /* width: 210mm; */
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 20px 0;
}

.canvas-header {
    width: 100%;
}

.canvas-header img:first-child {
    width: 80px;
}

.canvas-header img:last-child {
    width: 100%;
    height: 100%;
    display: block;
    margin-top: -30px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
