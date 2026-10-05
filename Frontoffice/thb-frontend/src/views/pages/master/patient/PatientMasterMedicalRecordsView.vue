<template>
  <div>
    <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Pasien' },
                { text: 'Riwayat Rekam Medis' },
                { text: `${patientData.alias_name}. ${patientData.name} (NO. RM: ${patientData.norm})` },
            ]"
        >
        <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
          <h5 class="mb-0">Riwayat Rekam Medis</h5>
          <b-button variant="primary" size="sm" @click="$bvModal.show('modal-add-record')">
            <feather-icon icon="PlusIcon" class="mr-50" />
            Tambah Rekam Medis
          </b-button>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <div class="row mx-0">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                  <span class="text-regular-14">Tampilkan</span>
                  <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                  <span class="text-regular-14">Entri</span>
                </b-form-group>
            </div>
          </div>
        </div>
          <b-table
          bordered
          responsive
          hover
          small
          show-empty
          head-variant="light"
          :fields="medicalRecordsTable"
          :items="medRecordList"
          thead-class="text-bold-14"
          tbody-tr-class="text-regular-12"
          class="mt-12 text-center"
        >
          <template #cell(no)="row">
            {{ row.index + 1 }}
          </template>

          <template #cell(rm_date)="row">
            {{ localDate(row.item.rm_date) }}
          </template>

          <template #cell(action)="row">
            <b-button
              size="sm"
              variant="info"
              class="mr-1"
              v-b-popover.hover.right="'Preview File'"
              @click="previewFile(row.item)"
            >
              <feather-icon icon="EyeIcon" />
            </b-button>
            <b-button
              size="sm"
              variant="danger"
              v-b-popover.hover.right="'Hapus'"
              @click="deleteRecord(row.item)"
            >
              <feather-icon icon="TrashIcon" />
            </b-button>
          </template>
          <template #empty>
            <div class="text-center fs-13 mt-1" style="font-style: italic;">
              Tidak ada riwayat rekam medis
            </div>
          </template>
        </b-table>
             <div class="col px-0 mt-20">
            <div class="d-flex">
                <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template
                        v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows
                        }}</template> dari {{ totalRows }} entri</span>
                <div class="ml-auto">
                    <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"
                        align="fill" size="sm" class="my-0"></b-pagination>
                </div>
            </div>
        </div>
      <b-modal
        id="modal-add-record"
        title="Tambah Rekam Medis"
        ok-title="Simpan"
        cancel-title="Batal"
        :ok-disabled="isUploading"
        @ok="handleSubmit"
        @hidden="resetForm"
      >
        <b-form>
          <b-form-group label="Tanggal Rekam Medis" label-for="input-date">
            <b-form-input id="input-date" v-model="form.rm_date" type="date" />
          </b-form-group>
          <b-form-group label="File Rekam Medis" label-for="input-file">
            <b-form-file
              id="input-file"
              v-model="input_file"
              accept="*"
              placeholder="Pilih File"
              @change="uploadFile($event)"
              autocomplete="off"
            />
          </b-form-group>

          <div v-if="isUploading" class="text-center mt-1">
            <b-spinner small variant="primary" />
            <span class="ml-1 text-muted" style="font-size: 12px;"> Loading.. </span>
          </div>
        </b-form>
      </b-modal>
      <b-modal
        id="modal-preview-file"
        title="Preview Rekam Medis"
        size="lg"
        hide-footer
        centered
      >
        <div class="text-center">
          <img
            v-if="previewIsImage"
            :src="previewUrl"
            style="max-width: 100%; max-height: 75vh;"
          />
          <iframe
            v-else
            :src="previewUrl"
            style="width: 100%; height: 75vh; border: none;"
          />
        </div>
      </b-modal>
        </Layout>
  </div>
</template>

<script>
  import moment from 'moment'
import Layout from '@/@core/light/Layout.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import { mapState } from 'vuex';

export default {
  name: 'PatientMasterMedicalRecordsView',
  components: {
    Layout,
    ToastificationContent
  },
  data() {
    return {
      urlbe:process.env.VUE_APP_IMAGE,
      medRecordList: [],
      previewUrl: '',
      previewIsImage: false,
      input_file: null,
      isUploading: false,
      isEditing: false,
      editId: null,
      patientData: {},
      form: {
        rm_date: '',
        file: null,
      },
      params: {
        norm: '',
        limit: 10,
        offset: 0,
      },
      currentPage:1,
      perPage: 20,
      firstRows: 1,
      lastRows: 20,
      poliOptions: [
        { value: null, text: '-- Pilih Poli --' },
        { value: 'umum', text: 'Poli Umum' },
        { value: 'gigi', text: 'Poli Gigi' },
        { value: 'anak', text: 'Poli Anak' },
      ],
      doctorOptions: [
        { value: null, text: '-- Pilih Dokter --' },
        { value: 'hendra', text: 'dr. Hendra Wijaya' },
        { value: 'amalia', text: 'dr. Amalia Putri' },
      ],
      medicalRecordsTable: [
        { key: 'no', label: 'No.', thStyle: { width: '100px' } },
        { key: 'rm_date', label: 'Tanggal RM' },
        { key: 'action', label: 'Aksi', thStyle: { width: '250px' } },
      ],
    }
  },
  computed: {
    ...mapState({
      items: state => state.registration.items,
      pageOptions: state => state.registration.pageOptions,
      start: state => state.registration.start,
      totalRows: state => state.registration.totalRows,
      }),
  },
  watch: {
    currentPage: {
        handler: function(value) {
            this.firstRows = this.perPage * (value - 1) + 1
            this.lastRows = this.perPage * (value)
            this.params.offset = this.perPage * (value - 1)
            this.params.limit = this.perPage
            this.$store.dispatch('patient/getListMedRecordByNorm',this.params)
        }
    },
    perPage: {
        handler: function(value) {
            this.params.limit = this.perPage
            this.$store.dispatch('patient/getListMedRecordByNorm',this.params)
        }
    },
  },
  mounted() {
        const getNorm = JSON.parse(localStorage.getItem('obj_norm'))
        console.log('norm from detail', getNorm.norm);

        if (getNorm?.norm) {
            this.params.norm = getNorm.norm
            this.getData()
        } else {
            console.warn('nothing norm here')
        }
  },
  methods: {
    notification (a,b,c,d) {
      this.$toast({
        component: ToastificationContent,
        employee: 'top-right',
        props: { title: a, icon: b, variant: c, text: d}
      })
    },
    localDate(input_date) {
      return moment(input_date).locale("id").format('DD MMMM YYYY')
    },
    async getData() {
      try {
        const payload = {
          norm: this.params.norm,
          offset: this.params.offset
        }

        const res = await this.$store.dispatch('patient/getListMedRecordByNorm', payload)
        const res_patient = await this.$store.dispatch('patient/getAll', payload).then(data => {
          this.patientData = data.data.rows[0]
       })

        this.medRecordList = res.data
        // this.pagination.total = res.data.count

      } catch (error) {
        console.error('ERR WHILE HIT ENDPOINT at: ', error)
      }
    },
    // onPageChange(newPage) {
    //   this.pagination.offset = (newPage - 1) * this.pagination.limit
    //   this.getData()
    // },
    uploadFile(e) {
      const file = e.target.files[0]
      if (!file) return
      this.form.file = file
      this.form.file_name = file.name
      this.form.file_type = file.type
    },
    async handleSubmit(bvModalEvent) {
      bvModalEvent.preventDefault()

      if (!this.form.rm_date || !this.form.file) {
        this.notification('Warning','CloseIcon','warning','Tanggal dan file wajib diisi.')
        return;
      }

      try {
        this.isUploading = true

        const formData = new FormData()
        formData.append('norm', this.params.norm)
        formData.append('rm_date', this.form.rm_date)
        formData.append('file', this.form.file)

        console.log('formData', formData)

        await this.$store.dispatch('patient/uploadMedRecord', formData)
        this.notification('Success','CheckIcon','success','Rekam medis berhasil diunggah.')

        this.$bvModal.hide('modal-add-record')
        this.resetForm()
        this.getData()

      } catch (error) {
        console.error('ERR upload med record: ', error)
        this.notification('Error','CloseIcon','danger','Gagal mengunggah rekam medis.')
      } finally {
        this.isUploading = false
      }
    },
    previewFile(item) {
      this.previewUrl = this.urlbe + item.path

      const ext = item.path.split('.').pop().toLowerCase()
      this.previewIsImage = ['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(ext)

      this.$bvModal.show('modal-preview-file')
    },
    resetForm() {
      this.form = { rm_date: '', file: null }
    },
    previewPdf(item) {
      console.log('Preview:', item)
    },
    editRecord(item) {
      this.isEditing = true
      this.editId = item.id
      this.form.rm_date = item.rm_date
      this.form.file_name = item.path.split('/').pop()
      this.$bvModal.show('modal-add-record')
    },
    resetForm() {
      this.form = { rm_date: '', file: null, file_name: null }
      this.input_file = null
      this.isEditing = false
      this.editId = null
    },
    deleteRecord(item) {
      this.$bvModal.msgBoxConfirm('Apakah anda yakin ingin menghapus rekam medis ini?', {
        title: 'Hapus Rekam Medis',
        okTitle: 'Hapus',
        okVariant: 'danger',
        cancelTitle: 'Batal',
        centered: true,
      }).then(async confirmed => {
        if (!confirmed) return

        try {
          await this.$store.dispatch('patient/deleteMedRecord', item.id)
          this.notification('Success', 'CheckIcon', 'success', 'Rekam medis berhasil dihapus.')
          this.getData()
        } catch (error) {
          console.error('ERR delete med record: ', error)
          this.notification('Error', 'CloseIcon', 'danger', 'Gagal menghapus rekam medis.')
        }
      })
    },
  },
}
</script>