<template>
    <div>
        <!-- <patientMenu selectedMenu="menu22" /> -->
        <section class="card mt-32">
            <div class="card-header bg-indigo-200">
                <span class="text-bold-18">Data Dasar</span>
            </div>
            <div class="card-body">
                <div class="row" style="font-size: 14px">
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Nama</div>
                        <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        <div>{{ regData.room_name }} ({{ regData.room_name }})</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Tanggal Lahir</div>
                        <div>{{ localDate(regData.birth_date) }}</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">No RM</div>
                        <div>{{ regData.norm }}</div>
                    </div>
                </div>
            </div>
        </section>
        <section class ="card mt-32">
            <div class="container-fluid p-4">
                <div class="row">
                    <div class="col-md-4 mt-8">
                        <label class="fs-13 mb-0">Tanggal & Jam Mulai</label>
                        <date-picker
                            v-model="partograph.datetime_start"
                            type="datetime"
                            format="YYYY-MM-DD HH:mm"
                            valueType="format"
                            placeholder="Pilih tanggal & jam mulai"
                            style="width: 100%;"
                        />
                    </div>
                    <div class="col-md-4 mt-8">
                        <label class="fs-13 mb-0">Dokter</label>
                            <v-select
                                v-model="partograph.doctor_id"
                                :options="doctor_list"
                                :reduce="doctor => doctor.value"
                                label="title"
                                placeholder="-- Pilih Dokter --"
                            />
                        <!-- <b-form-input v-model="partograph.doctor_id" /> -->
                    </div>
                    <div class="col-md-4 mt-8">
                        <label class="fs-13 mb-0">Bidan</label>
                        <v-select
                            v-model="partograph.midwife_id"
                            :options="midwife_list"
                            :reduce="midwife => midwife.value"
                            label="title"
                            placeholder="-- Pilih Bidan --"
                        />
                        <!-- <b-form-input v-model="partograph.midwife_id" /> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mt-8">
                        <label class="fs-13 mb-0">Pecah Ketuban Sejak</label>
                        <date-picker
                            v-model="partograph.water_breaks_since"
                            type="datetime"
                            format="YYYY-MM-DD HH:mm"
                            valueType="format"
                            placeholder="Pilih tanggal & jam pecah ketuban"
                            style="width: 100%;"
                        />
                        <!-- <b-form-input v-model="partograph.water_breaks_since" /> -->
                    </div>
                    <div class="col-md-4 mt-8">
                        <label class="fs-13 mb-0">Mulas Sejak</label>
                        <date-picker
                            v-model="partograph.stomachache_since"
                            type="datetime"
                            format="YYYY-MM-DD HH:mm"
                            valueType="format"
                            placeholder="Pilih tanggal & jam mules"
                            style="width: 100%;"
                        />
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="w-100 d-flex">
                            <div class="mr-1 flex-fill">
                                <label class="fs-13 mb-0">Gravida</label>
                                <b-form-input v-model="partograph.gravida" />
                            </div>
                            <div class="mr-1 flex-fill">
                                <label class="fs-13 mb-0">Para</label>
                                <b-form-input v-model="partograph.para" />
                            </div>
                            <div class="flex-fill">
                                <label class="fs-13 mb-0">Abortus</label>
                                <b-form-input v-model="partograph.abortus" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4"  v-if="!partograph.id">
                    <div class="col-12 text-right">
                        <b-button variant="primary" @click="handleSubmit">Submit</b-button>
                    </div>
                </div>
            </div>
            <div class="text-center mb-2"  v-if="partograph.id">
                <b-button variant="primary" @click="handleSaveAll"> <b-icon icon="save" class="mr-1" />Simpan</b-button>
            </div>
        </section>

        <template  v-if="partograph.id">

            <b-button
                :to="`/patient/inpatient/partograph/print/${this.reg_des_id}`"
                router
                target="_blank"
                variant="success"
                class="mb-4 mt-4"
                >
                <b-icon icon="printer" class="mr-1" /> Cetak Partograf
            </b-button>


            <ul class="nav nav-tabs mt-2">
                <li class="nav-item">
                    <a
                        class="nav-link"
                        :class="{ active: activeTab === 'partograph' }"
                        href="#"
                        @click.prevent="handleTabChange('partograph')"
                    >Partograf</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        :class="{ active: activeTab === 'birth_notes' }"
                        href="#"
                        @click.prevent="handleTabChange('birth_notes')"
                    >Catatan Persalinan</a>
                </li>
            </ul>
        </template>

        <div v-if="partograph.id" class="card mt-2">
            <div class="card-body">
                <div v-if="activeTab === 'partograph'">
                    <div>
                        <div
                        v-b-toggle.partograph-1
                        class="expandable-header d-flex align-items-center justify-content-between mb-2"
                        style="cursor: pointer; width: 100%; background: #f5f6fa; border-radius: 6px; padding: 12px 20px; font-weight: 600; font-size: 16px;"
                        >
                        <span>Denyut Jantung Janin, Ketuban & Penyusupan</span>
                        <span>
                            <b-icon :icon="isPartograph1Open ? 'chevron-up' : 'chevron-down'" />
                        </span>
                        </div>
                        <b-collapse id="partograph-1" v-model="isPartograph1Open">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <!-- Denyut Jantung Janin -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Denyut Jantung Janin</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select
                                                        v-model="observationHourFetalHeartRate"
                                                        :options="labels"
                                                        placeholder="Pilih jam observasi"
                                                        class="w-100"
                                                        :class="{ 'border-warning': editType === 'FetalHeartRates' && editIndex !== null }"
                                                    />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Denyut Jantung Janin</label>
                                                    <b-input-group append="x/mnt">
                                                        <b-form-input
                                                            type="number"
                                                            autocomplete="off"
                                                            v-model="fetalHeartRate"
                                                            :class="{ 'border-warning': editType === 'FetalHeartRates' && editIndex !== null }"
                                                        ></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'FetalHeartRates' && editIndex !== null)"
                                                @click="addFetalHeartRate"
                                            >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('FetalHeartRates')"
                                            >
                                                Perbarui
                                            </b-button>
                                        </div>
                                        <!-- Air Ketuban -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Air Ketuban</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select
                                                    v-model="observationHourAmnioticFluid"
                                                    :options="labels"
                                                    placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'AmnioticFluids' && editIndex !== null }"
                                                    />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Air Ketuban</label>
                                                    <b-form-select
                                                        v-model="amnioticFluid"
                                                        :options="amnioticFluidOptions"
                                                        placeholder="Pilih air ketuban"
                                                        :class="{ 'border-warning': editType === 'AmnioticFluids' && editIndex !== null }"
                                                    />
                                                </div>
                                            </div>
                                             <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'AmnioticFluids' && editIndex !== null)"
                                                @click="addAmnioticFluid"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('AmnioticFluids')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                        <!-- Penyusupan -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Penyusupan</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourInfiltration" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'Infiltration' && editIndex !== null }"/>
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Penyusupan</label>
                                                    <b-form-select
                                                        v-model="infiltration"
                                                        :options="infiltrationOptions"
                                                        placeholder="Pilih penyusupan"
                                                        :class="{ 'border-warning': editType === 'Infiltration' && editIndex !== null }"
                                                    />
                                                </div>
                                            </div>
                                             <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'Infiltration' && editIndex !== null)"
                                                @click="addInfiltration"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('Infiltration')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <b-table
                                        :items="tempFetalHeartRates"
                                        :fields="[
                                            { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%',  fontSize: '14px' } },
                                            { key: 'fetal_heart_rate', label: 'Denyut Jantung Janin (x/mnt)', thStyle: { width: '50%',  fontSize: '14px' } },
                                            { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                        ]"
                                        small
                                        class="mt-3"
                                    >

                                        <template #cell(actions)="data">
                                            <b-button size="sm" variant="warning" @click="editItem('FetalHeartRates', data.index)">
                                                <b-icon-pencil />
                                            </b-button>
                                            <b-button size="sm" variant="danger" @click="deleteItem('FetalHeartRates', data.index)">
                                                <b-icon-trash />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </div>
                                <div class="col-md-4">
                                    <b-table
                                        :items="tempAmnioticFluids"
                                        :fields="[
                                            { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%', fontSize: '14px' } },
                                            { key: 'amniotic_fluid', label: 'Air Ketuban', thStyle: { width: '50%', fontSize: '14px' } },
                                            { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                        ]"
                                        small
                                        class="mt-3"
                                    >
                                        <template #cell(actions)="data">
                                            <b-button size="sm" variant="warning" @click="editItem('AmnioticFluids', data.index)">
                                                <b-icon-pencil />
                                            </b-button>
                                            <b-button size="sm" variant="danger" @click="deleteItem('AmnioticFluids', data.index)">
                                                <b-icon-trash />
                                            </b-button>
                                        </template>

                                        <template #cell(observation_hours)="data">
                                            <span>{{ data.item.observation_hours }}</span>
                                        </template>
                                        <template #cell(amniotic_fluid)="data">
                                            <span>
                                                <!-- [{{ typeof data.item.amniotic_fluid }}:{{ data.item.amniotic_fluid }}] -->
                                                {{ amnioticFluidMap[Number(data.item.amniotic_fluid)] || '-' }}
                                            </span>
                                        </template>
                                    </b-table>
                                </div>
                                <div class="col-md-4">
                                    <b-table
                                        :items="tempInfiltration"
                                        :fields="[
                                            { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%', fontSize: '14px' } },
                                            { key: 'infiltration', label: 'Penyusupan', thStyle: { width: '50%', fontSize: '14px' } },
                                            { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                        ]"
                                        small
                                        class="mt-3"
                                    >
                                        <template #cell(actions)="data">
                                            <b-button size="sm" variant="warning" @click="editItem('Infiltration', data.index)">
                                                <b-icon-pencil />
                                            </b-button>
                                            <b-button size="sm" variant="danger" @click="deleteItem('Infiltration', data.index)">
                                                <b-icon-trash />
                                            </b-button>
                                        </template>
                                        <template #cell(infiltration)="data">
                                            <span>{{ infiltrationMap[Number(data.item.infiltration)] || '-' }}</span>
                                        </template>
                                    </b-table>
                                </div>
                            </div>
                            <div class="text-center mb-2">
                                <b-button variant="primary" @click="handleSaveAll">
                                    <b-icon icon="save" class="mr-1" />Simpan
                                </b-button>
                                <b-button variant="outline-secondary" class="ml-2" @click="handleCancel">
                                    <b-icon icon="x-circle" class="mr-1" />Batal
                                </b-button>
                            </div>
                        </b-collapse>
                        <div style="width: 100%; margin-top: 24px; ">
                            <label class="fs-15 mb-0 font-weight-bold">Denyut Jantung Janin</label>
                            <div v-if="showCharts">
                                <div class="d-flex align-items-center mb-2">
                                    <b-button size="sm" variant="warning" @click="toggleEditFetalHeartRate">
                                        {{ isEditingFetalHeartRate ? 'Batal Edit' : 'Edit Data' }}
                                    </b-button>
                                    <span v-if="isEditingFetalHeartRate" class="ml-2 text-danger">
                                        Klik titik pada grafik yang ingin diedit.
                                    </span>
                                </div>
                                <div style="width: 100%; height: 300px; margin-bottom: 24px; overflow: hidden;">
                                    <LineChart
                                        :chartData="getFetalHeartRateChartData()"
                                        @point-clicked="onFetalHeartRatePointClicked"
                                    />
                                </div>
                                <div v-if="selectedFetalHeartRatePoint && isEditingFetalHeartRate" class="mt-3">
                                  <div class="d-flex flex-column align-items-center" style="width: 300px; min-height: 80px;">
                                    <div class="w-100 mb-3">
                                      <strong>Waktu:</strong> {{ selectedFetalHeartRatePoint.label }}
                                    </div>
                                    <div class="d-flex w-100" style="gap: 8px;">
                                      <!-- Trash button column -->
                                      <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                        <b-button
                                          size="sm"
                                          variant="danger"
                                          @click="confirmDeleteFetalHeartRatePoint"
                                          style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                        >
                                          <b-icon icon="trash" />
                                        </b-button>
                                      </div>
                                      <!-- Input field with label -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Denyut Jantung Janin</label>
                                        <b-form-input
                                          v-model="selectedFetalHeartRatePoint.value"
                                          type="number"
                                          min="80"
                                          max="200"
                                          class="w-100"
                                        />
                                      </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                      <b-button
                                        size="sm"
                                        variant="success"
                                        @click="saveFetalHeartRateEdit"
                                      >
                                        <b-icon icon="check" /> Simpan
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                                <label class="fs-15 mb-0 font-weight-bold">Air Ketuban & Penyusupan</label>
                                <div class="d-flex align-items-center mb-2">
                                    <b-button size="sm" variant="warning" @click="toggleEditFluidInfiltration">
                                        {{ isEditingFluidInfiltration ? 'Batal Edit' : 'Edit Data' }}
                                    </b-button>
                                    <span v-if="isEditingFluidInfiltration" class="ml-2 text-danger">
                                        Klik sel pada tabel yang ingin diedit.
                                    </span>
                                </div>
                                <div style="width: 100%; overflow: hidden;">
                                    <TableFluidInfiltration
                                    :labels="labels"
                                    :partographId="partograph.id"
                                    :isEditing="isEditingFluidInfiltration"
                                    @cell-clicked="onFluidInfiltrationCellClicked"
                                    />
                                </div>
                                <div v-if="selectedFluidInfiltrationPoint && isEditingFluidInfiltration" class="mt-3">
                                    <div class="d-flex flex-column align-items-center" style="width: 300px; min-height: 80px;">
                                        <div class="w-100 mb-3">
                                            <strong>Waktu:</strong> {{ selectedFluidInfiltrationPoint.label }}
                                        </div>
                                        <div class="d-flex w-100" style="gap: 8px;">
                                        <!-- Trash button column -->
                                        <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                            <b-button
                                            size="sm"
                                            variant="danger"
                                            @click="confirmDeleteFluidInfiltrationPoint"
                                            style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                            >
                                            <b-icon icon="trash" />
                                            </b-button>
                                        </div>
                                        <!-- Input field with label -->
                                        <div class="flex-grow-1 d-flex flex-column">
                                            <label class="mb-1 font-weight-bold">
                                            {{ selectedFluidInfiltrationPoint.type === 'amniotic_fluid' ? 'Air Ketuban' : 'Penyusupan' }}
                                            </label>
                                            <b-form-select
                                            v-if="selectedFluidInfiltrationPoint.type === 'amniotic_fluid'"
                                            v-model="selectedFluidInfiltrationPoint.value"
                                            :options="amnioticFluidOptions"
                                            class="w-100"
                                            />
                                            <b-form-select
                                            v-else
                                            v-model="selectedFluidInfiltrationPoint.value"
                                            :options="infiltrationOptions"
                                            class="w-100"
                                            />
                                        </div>
                                        </div>
                                        <div class="d-flex justify-content-end align-items-end mt-4 mb-5 w-100">
                                            <b-button
                                                size="sm"
                                                variant="success"
                                                @click="selectedFluidInfiltrationPoint.type === 'amniotic_fluid' ? saveAmnioticFluidEdit() : saveInfiltrationEdit()"
                                            >
                                            <b-icon icon="check" /> Simpan
                                        </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div
                            v-b-toggle.partograph-2
                            class="expandable-header d-flex align-items-center justify-content-between mb-2"
                            style="cursor: pointer; width: 100%; background: #f5f6fa; border-radius: 6px; padding: 12px 20px; font-weight: 600; font-size: 16px; margin-top: 24px;"
                            >
                            <span>Pembukaan Serviks & Kontraksi</span>
                            <span>
                                <b-icon :icon="isPartograph2Open ? 'chevron-up' : 'chevron-down'" />
                            </span>
                        </div>
                        <b-collapse id="partograph-2" v-model="isPartograph2Open">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <!-- Pembukaan Serviks -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Pembukaan Serviks</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourCervicalDilation" :options="labels" placeholder="Pilih jam observasi" class="w-100"
                                                    :class="{ 'border-warning': editType === 'CervicalDilation' && editIndex !== null }"
                                                    />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Pembukaan Serviks </label>
                                                    <b-form-select
                                                        v-model="cervicalDilation"
                                                        :options="cervicalDilationOptions"
                                                        placeholder="Pilih pembukaan serviks"
                                                        :class="{ 'border-warning': editType === 'CervicalDilation' && editIndex !== null }"
                                                    />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Penurunan Kepala</label>
                                                    <b-form-select
                                                        v-model="headDescent"
                                                        :options="headDescentOptions"
                                                        placeholder="Pilih penurunan kepala"
                                                        :class="{ 'border-warning': editType === 'CervicalDilation' && editIndex !== null }"
                                                    />
                                                </div>
                                            </div>
                                             <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'CervicalDilation' && editIndex !== null)"
                                                @click="addCervicalDilation"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('CervicalDilation')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                        <!-- Kontraksi -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Kontraksi</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourContraction" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'Contraction' && editIndex !== null }" />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Kontraksi</label>
                                                    <b-form-select
                                                        v-model="contraction"
                                                        :options="[
                                                            { value: '1', text: '1' },
                                                            { value: '2', text: '2' },
                                                            { value: '3', text: '3' },
                                                            { value: '4', text: '4' },
                                                            { value: '5', text: '5' }
                                                        ]"
                                                        placeholder="Pilih kontraksi"
                                                        :class="{ 'border-warning': editType === 'Contraction' && editIndex !== null }"
                                                    />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Lama Kontraksi</label>
                                                    <b-input-group append="detik">
                                                        <b-form-input type="number" autocomplete="off" v-model="contractionLength"
                                                        :class="{ 'border-warning': editType === 'Contraction' && editIndex !== null }" ></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                             <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'Contraction' && editIndex !== null)"
                                                @click="addContraction"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('Contraction')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <b-table
                                        :items="tempCervicalDilation"
                                        :fields="[
                                            { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%',  fontSize: '14px' } },
                                            { key: 'cervical_dilation', label: 'Pembukaan Serviks', thStyle: { width: '50%',  fontSize: '14px' } },
                                            { key: 'head_descent', label: 'Penurunan Kepala', thStyle: { width: '35%',  fontSize: '14px' } },
                                            { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                        ]"
                                        small
                                        class="mt-3"
                                    >
                                        <template #cell(cervical_dilation)="data">
                                            <span>{{ data.item.cervical_dilation }} cm</span>
                                        </template>
                                        <template #cell(head_descent)="data">
                                            <span>{{ data.item.head_descent }} cm</span>
                                        </template>

                                        <template #cell(actions)="data">
                                            <b-button size="sm" variant="warning" @click="editItem('CervicalDilation', data.index)">
                                                <b-icon-pencil />
                                            </b-button>
                                            <b-button size="sm" variant="danger" @click="deleteItem('CervicalDilation', data.index)">
                                                <b-icon-trash />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </div>
                                <div class="col-md-4">
                                    <b-table
                                        :items="tempContraction"
                                        :fields="[
                                            { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%', fontSize: '14px' } },
                                            { key: 'contraction', label: 'Kontraksi', thStyle: { width: '50%', fontSize: '14px' } },
                                            { key: 'duration', label: 'Lama Kontraksi (detik)', thStyle: { width: '35%', fontSize: '14px' } },
                                            { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                        ]"
                                        small
                                        class="mt-3"
                                    >
                                        <template #cell(actions)="data">
                                            <b-button size="sm" variant="warning" @click="editItem('Contraction', data.index)">
                                                <b-icon-pencil />
                                            </b-button>
                                            <b-button size="sm" variant="danger" @click="deleteItem('Contraction', data.index)">
                                                <b-icon-trash />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </div>
                            </div>
                            <div class="text-center mb-2">
                                <b-button variant="primary" @click="handleSaveAll">
                                    <b-icon icon="save" class="mr-1" />Simpan
                                </b-button>
                                <b-button variant="outline-secondary" class="ml-2" @click="handleCancel">
                                    <b-icon icon="x-circle" class="mr-1" />Batal
                                </b-button>
                            </div>
                            <!-- <b-button size="sm" variant="primary" @click="handleSaveAll">Simpan Semua</b-button> -->
                        </b-collapse>
                        <div style="width: 100%; margin-top: 24px; ">
                            <label class="font-weight-bold fs-15 mb-0">Pembukaan Serviks</label>
                            <div class="d-flex align-items-center mb-2">
                                <b-button size="sm" variant="warning" @click="toggleEditCervicalDilation">
                                    {{ isEditingCervicalDilation ? 'Batal Edit' : 'Edit Data' }}
                                </b-button>
                                <span v-if="isEditingCervicalDilation" class="ml-2 text-danger">
                                    Klik titik pada grafik yang ingin diedit.
                                </span>
                            </div>
                            <div v-if="showCharts">
                                <div style="width: 100%; height: 300px; margin-bottom: 24px; overflow: hidden;">
                                    <LineChartServiks
                                      :chartData="getCervicalDilationChartData()"
                                      @point-clicked="onCervicalDilationPointClicked"
                                    /><br>
                                </div>
                                <div v-if="selectedCervicalDilationPoint && isEditingCervicalDilation" class="mt-3">
                                  <div class="d-flex flex-column align-items-center" style="width: 400px; min-height: 120px;">
                                    <div class="w-100 mb-3">
                                      <strong>Waktu: </strong> {{ selectedCervicalDilationPoint.label }}
                                    </div>
                                    <div class="d-flex w-100" style="gap: 8px;">
                                      <!-- Trash button column -->
                                      <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                        <b-button
                                          size="sm"
                                          variant="danger"
                                          @click="confirmDeleteCervicalDilationPoint"
                                          style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                        >
                                          <b-icon icon="trash" />
                                        </b-button>
                                      </div>
                                      <!-- Pembukaan Serviks -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Pembukaan Serviks</label>
                                        <b-form-select
                                          v-model="selectedCervicalDilationPoint.cervicalDilation"
                                          :options="cervicalDilationOptions"
                                          class="w-100"
                                        />
                                      </div>
                                      <!-- Penurunan Kepala -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Penurunan Kepala</label>
                                        <b-form-select
                                          v-model="selectedCervicalDilationPoint.headDescent"
                                          :options="headDescentOptions"
                                          class="w-100"
                                        />
                                      </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                      <b-button
                                        size="sm"
                                        variant="success"
                                        @click="saveCervicalDilationEdit"
                                      >
                                        <b-icon icon="check" /> Simpan
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                                <label class="font-weight-bold fs-15 mb-0">Kontraksi</label>
                                <div class="d-flex align-items-center mb-2">
                                    <b-button size="sm" variant="warning" @click="toggleEditContraction">
                                        {{ isEditingContraction ? 'Batal Edit' : 'Edit Data' }}
                                    </b-button>
                                    <span v-if="isEditingContraction" class="ml-2 text-danger">
                                        Klik bar pada grafik yang ingin diedit.
                                    </span>
                                </div>
                                <BarChartKontraksi class="mt-24 w-100"
                                    :partographId="partograph.id"
                                    :datetime-start="partograph.datetime_start"
                                    @labels-updated="labels = $event"
                                    @bar-clicked="onContractionBarClicked"
                                /><br>
                                <div v-if="selectedContractionPoint && isEditingContraction" class="mt-3">
                                  <div class="d-flex flex-column align-items-center" style="width: 400px; min-height: 120px;">
                                    <div class="w-100 mb-3">
                                      <strong>Waktu: </strong> {{ selectedContractionPoint.label }}
                                    </div>
                                    <div class="d-flex w-100" style="gap: 8px;">
                                      <!-- Trash button column -->
                                      <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                        <b-button
                                          size="sm"
                                          variant="danger"
                                          @click="confirmDeleteContractionPoint"
                                          style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                        >
                                          <b-icon icon="trash" />
                                        </b-button>
                                      </div>
                                      <!-- Kontraksi -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Kontraksi</label>
                                        <b-form-select
                                            v-model="selectedContractionPoint.contraction"
                                            :options="[
                                            { value: 1, text: '1' },
                                            { value: 2, text: '2' },
                                            { value: 3, text: '3' },
                                            { value: 4, text: '4' },
                                            { value: 5, text: '5' }
                                            ]"
                                            class="w-100"
                                        />
                                      </div>
                                      <!-- Lama Kontraksi (detik) -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Lama Kontraksi (detik)</label>
                                        <b-form-input
                                            v-model="selectedContractionPoint.duration"
                                            type="number"
                                            min="0"
                                            class="w-100"
                                            placeholder="Masukkan lama kontraksi"
                                        />
                                      </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                      <b-button
                                        size="sm"
                                        variant="success"
                                        @click="saveContractionEdit"
                                      >
                                        <b-icon icon="check" /> Simpan
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div
                        v-b-toggle.partograph-3
                        class="expandable-header d-flex align-items-center justify-content-between mb-2"
                        style="cursor: pointer; width: 100%; background: #f5f6fa; border-radius: 6px; padding: 12px 20px; font-weight: 600; font-size: 16px; margin-top: 24px;"
                        >
                        <span>Oksitosin, Obat & Cairan</span>
                        <span>
                            <b-icon :icon="isPartograph3Open ? 'chevron-up' : 'chevron-down'" />
                        </span>
                        </div>
                        <b-collapse id="partograph-3" v-model="isPartograph3Open">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <!-- Obat & Cairan IV -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Obat & Cairan IV</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourIVFluidsMeds" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'IVFluidsMeds' && editIndex !== null }" />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Obat & Cairan IV </label>
                                                    <v-select
                                                        v-model="IVFluidsMeds"
                                                        :options="IVFluidsMedsOptions"
                                                        label="label"
                                                        :reduce="option => option.value"
                                                        placeholder="Pilih obat & cairan IV"
                                                        :searchable="true"
                                                        :clearable="true"
                                                        :class="{ 'border-warning': editType === 'IVFluidsMeds' && editIndex !== null }"
                                                        style="width: 100%; min-width: 300px;"
                                                    />
                                                </div>
                                            </div>
                                             <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'IVFluidsMeds' && editIndex !== null)"
                                                @click="addIVFluidsMeds"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('IVFluidsMeds')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                        <!-- Oksitosin -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Oksitosin</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourOxytocin" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'Oxytocin' && editIndex !== null }" />
                                                </div>
                                            <div class="flex-fill">
                                                <label class="fs-13 mb-0">Oksitosin</label>
                                                <b-input-group append="IU">
                                                    <b-form-input
                                                        type="number"
                                                        autocomplete="off"
                                                        v-model="oxytocin"
                                                        placeholder=""
                                                        min="0"
                                                        step="any"
                                                        :class="{ 'border-warning': editType === 'Oxytocin' && editIndex !== null }"
                                                    ></b-form-input>
                                                </b-input-group>
                                            </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Tetes/Menit</label>
                                                    <b-input-group append="tetes/menit">
                                                        <b-form-input type="number" autocomplete="off" v-model="oxytocinDripRate"
                                                        :class="{ 'border-warning': editType === 'Oxytocin' && editIndex !== null }" ></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'Oxytocin' && editIndex !== null)"
                                                @click="addOxytocin"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('Oxytocin')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <b-table
                                        :items="tempIVFluidsMeds"
                                        :fields="[
                                            { key: 'observation_hours', label: 'Jam Observasi' },
                                            { key: 'iv_fluids_meds', label: 'Obat & Cairan IV' },
                                            { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                        ]"
                                        small
                                        class="mt-3"
                                    >
                                    <template #cell(iv_fluids_meds)="data">
                                        <span>
                                            {{ IVFluidsMedsOptions.find(opt => opt.value === data.item.iv_fluids_meds)?.label || '-' }}
                                        </span>
                                    </template>
                                    <template #cell(actions)="data">
                                        <b-button size="sm" variant="warning" @click="editItem('IVFluidsMeds', data.index)">
                                            <b-icon-pencil />
                                        </b-button>
                                        <b-button size="sm" variant="danger" @click="deleteItem('IVFluidsMeds', data.index)">
                                            <b-icon-trash />
                                        </b-button>
                                    </template>
                                    </b-table>

                                </div>
                                <div class="col-md-4">
                                    <b-table
                                        :items="tempOxytocin"
                                        :fields="[
                                            { key: 'observation_hours', label: 'Jam Observasi' },
                                            { key: 'oxytocin', label: 'Oksitosin' },
                                            { key: 'oxytocin_drip_rate', label: 'Tetes/Menit' },
                                            { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                        ]"
                                        small
                                        class="mt-3"
                                    >
                                        <template #cell(actions)="data">
                                            <b-button size="sm" variant="warning" @click="editItem('Oxytocin', data.index)">
                                                <b-icon-pencil />
                                            </b-button>
                                            <b-button size="sm" variant="danger" @click="deleteItem('Oxytocin', data.index)">
                                                <b-icon-trash />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </div>
                            </div>
                            <div class="text-center mb-2">
                                <b-button variant="primary" @click="handleSaveAll">
                                    <b-icon icon="save" class="mr-1" />Simpan
                                </b-button>
                                <b-button variant="outline-secondary" class="ml-2" @click="handleCancel">
                                    <b-icon icon="x-circle" class="mr-1" />Batal
                                </b-button>
                            </div>
                        </b-collapse>
                        <div style="width: 100%; margin-top: 24px; ">
                            <div v-if="showCharts">
                                <label class="font-weight-bold fs-15 mb-0">Oksitosin</label>
                                <div class="d-flex align-items-center mb-2">
                                    <b-button size="sm" variant="warning" @click="toggleEditOxytocin">
                                        {{ isEditingOxytocin ? 'Batal Edit' : 'Edit Data' }}
                                    </b-button>
                                    <span v-if="isEditingOxytocin" class="ml-2 text-danger">
                                        Klik sel pada tabel yang ingin diedit.
                                    </span>
                                </div>
                                <div style="width: 100%;overflow: hidden;">
                                    <TableOxytocin
                                        :labels="labels"
                                        :oxytocin="partograph.oxytocin"
                                        :oxytocinDripRate="partograph.oxytocin_drip_rate"
                                        :isEditing="isEditingOxytocin"
                                        @cell-clicked="onOxytocinCellClicked"
                                    />
                                </div>
                                <div v-if="selectedOxytocinPoint && isEditingOxytocin" class="mt-3">
                                    <div class="d-flex flex-column align-items-center" style="width: 300px; min-height: 80px;">
                                        <div class="w-100 mb-3">
                                            <strong>Waktu:</strong> {{ selectedOxytocinPoint.label }}
                                        </div>
                                        <div class="d-flex w-100" style="gap: 8px;">
                                            <!-- Trash button column -->
                                            <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                                <b-button
                                                size="sm"
                                                variant="danger"
                                                @click="confirmDeleteOxytocinPoint"
                                                style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                                >
                                                <b-icon icon="trash" />
                                                </b-button>
                                            </div>
                                            <!-- Oksitosin -->
                                            <div class="flex-grow-1 d-flex flex-column">
                                                <label class="mb-1 font-weight-bold">Oksitosin (U/L)</label>
                                                <b-form-input
                                                v-model="selectedOxytocinPoint.value"
                                                type="number"
                                                min="0"
                                                class="w-100"
                                                placeholder="Masukkan Oksitosin"
                                                />
                                            </div>
                                            <!-- Tetes / menit -->
                                            <div class="flex-grow-1 d-flex flex-column">
                                                <label class="mb-1 font-weight-bold">Tetes / menit</label>
                                                <b-form-input
                                                v-model="selectedOxytocinPoint.dripRate"
                                                type="number"
                                                min="0"
                                                class="w-100"
                                                placeholder="Masukkan tetes/menit"
                                                />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                        <b-button
                                            size="sm"
                                            variant="success"
                                            @click="saveOxytocinEdit"
                                        >
                                            <b-icon icon="check" /> Simpan
                                        </b-button>
                                        </div>
                                    </div>
                                </div>
                                <label class="font-weight-bold fs-15 mb-0">Obat dan Cairan IV</label>
                                <div class="d-flex align-items-center mb-2">
                                    <b-button size="sm" variant="warning" @click="toggleEditIVFluidsMeds">
                                        {{ isEditingIVFluidsMeds ? 'Batal Edit' : 'Edit Data' }}
                                    </b-button>
                                    <span v-if="isEditingIVFluidsMeds" class="ml-2 text-danger">
                                        Klik sel pada tabel yang ingin diedit.
                                    </span>
                                </div>
                                <div style="width: 100%; overflow: hidden;">
                                    <TableIVFluidsMeds
                                    :labels="labels"
                                    :IVFluidsMeds="partograph.iv_fluids_meds"
                                    :isEditing="isEditingIVFluidsMeds"
                                    @cell-clicked="onIVFluidsMedsCellClicked"
                                    />
                                </div>
                                <div v-if="selectedIVFluidsMedsPoint && isEditingIVFluidsMeds" class="mt-3">
                                  <div class="d-flex flex-column align-items-center" style="width: 300px; min-height: 80px;">
                                    <div class="w-100 mb-3">
                                      <strong>Waktu:</strong> {{ selectedIVFluidsMedsPoint.label }}
                                    </div>
                                    <div class="d-flex w-100" style="gap: 8px;">
                                      <!-- Trash button column -->
                                      <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                        <b-button
                                          size="sm"
                                          variant="danger"
                                          @click="confirmDeleteIVFluidsMedsPoint"
                                          style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                        >
                                          <b-icon icon="trash" />
                                        </b-button>
                                      </div>
                                      <!-- Obat & Cairan IV -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Obat & Cairan IV</label>
                                        <v-select
                                          v-model="selectedIVFluidsMedsPoint.value"
                                          :options="IVFluidsMedsOptions"
                                          label="label"
                                          :reduce="option => option.value"
                                          placeholder="Pilih obat & cairan IV"
                                          :searchable="true"
                                          :clearable="true"
                                          class="w-100"
                                        />
                                      </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                      <b-button
                                        size="sm"
                                        variant="success"
                                        @click="saveIVFluidsMedsEdit"
                                      >
                                        <b-icon icon="check" /> Simpan
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div
                        v-b-toggle.partograph-4
                        class="expandable-header d-flex align-items-center justify-content-between mb-2"
                        style="cursor: pointer; width: 100%; background: #f5f6fa; border-radius: 6px; padding: 12px 20px; font-weight: 600; font-size: 16px; margin-top: 24px;"
                        >
                        <span>Tensi, Nadi, Suhu Badan & Urin</span>
                        <span>
                            <b-icon :icon="isPartograph4Open ? 'chevron-up' : 'chevron-down'" />
                        </span>
                        </div>
                        <b-collapse id="partograph-4" v-model="isPartograph4Open">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <!-- Tekanan Darah -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Pemeriksaan Tekanan Darah</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourBloodPressure" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'BloodPressure' && editIndex !== null }"/>
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Tekanan Darah</label>
                                                    <b-input-group append="mmHg">
                                                        <b-form-input
                                                            type="number"
                                                            autocomplete="off"
                                                            v-model="bloodPressureSystolic"
                                                            placeholder="Sistolik"
                                                            min="0"
                                                            step="any"
                                                            style="min-width: 100px;"
                                                            :class="{ 'border-warning': editType === 'BloodPressure' && editIndex !== null }"
                                                        ></b-form-input>
                                                        <span class="mx-1" style="font-weight: bold;">/</span>
                                                        <b-form-input
                                                            type="number"
                                                            autocomplete="off"
                                                            v-model="bloodPressureDiastolic"
                                                            placeholder="Diastolik"
                                                            min="0"
                                                            step="any"
                                                            style="min-width: 100px;"
                                                            :class="{ 'border-warning': editType === 'BloodPressure' && editIndex !== null }"
                                                        ></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'BloodPressure' && editIndex !== null)"
                                                @click="addBloodPressure"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('BloodPressure')"
                                                >
                                                Perbarui
                                            </b-button>
                                            <!-- <b-button size="sm" variant="success" class="mt-2" @click="addBloodPressure">Tambah</b-button> -->
                                        </div>
                                        <!-- Nadi -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Pemeriksaan Nadi</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourPulse" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'Pulse' && editIndex !== null }" />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Nadi</label>
                                                    <b-input-group append="bpm">
                                                        <b-form-input
                                                            type="number"
                                                            autocomplete="off"
                                                            v-model="pulse"
                                                            placeholder="Nadi"
                                                            min="0"
                                                            step="any"
                                                            style="min-width: 100px;"
                                                            :class="{ 'border-warning': editType === 'Pulse' && editIndex !== null }"
                                                        ></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'Pulse' && editIndex !== null)"
                                                @click="addPulse"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('Pulse')"
                                                >
                                                Perbarui
                                            </b-button>
                                            <!-- <b-button size="sm" variant="success" class="mt-2" @click="addPulse">Tambah</b-button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                    <div class="col-md-4">
                                        <b-table
                                            :items="tempBloodPressure"
                                            :fields="[
                                                { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%', fontSize: '14px' } },
                                                { key: 'blood_pressure', label: 'Tekanan Darah', thStyle: { width: '50%', fontSize: '14px' } },
                                                { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                            ]"
                                            small
                                            class="mt-3"
                                        >
                                            <template #cell(observation_hours)="data">
                                                <span>{{ data.item.observation_hours }}</span>
                                            </template>
                                            <template #cell(blood_pressure)="data">
                                                <span>{{ data.item.blood_pressure_systolic }}/{{ data.item.blood_pressure_diastolic }} mmHg</span>
                                            </template>
                                            <template #cell(actions)="data">
                                                <b-button size="sm" variant="warning" @click="editItem('BloodPressure', data.index)">
                                                    <b-icon-pencil />
                                                </b-button>
                                                <b-button size="sm" variant="danger" @click="deleteItem('BloodPressure', data.index)">
                                                    <b-icon-trash />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </div>
                                    <div class="col-md-4">
                                        <b-table
                                            :items="tempPulse"
                                            :fields="[
                                                { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%', fontSize: '14px' } },
                                                { key: 'pulse', label: 'Nadi', thStyle: { width: '50%', fontSize: '14px' } },
                                                { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                            ]"
                                            small
                                            class="mt-3"
                                        >
                                            <template #cell(observation_hours)="data">
                                                <span>{{ data.item.observation_hours }}</span>
                                            </template>
                                            <template #cell(pulse)="data">
                                                <span>{{ data.item.pulse }} bpm</span>
                                            </template>
                                            <template #cell(actions)="data">
                                                <b-button size="sm" variant="warning" @click="editItem('Pulse', data.index)">
                                                    <b-icon-pencil />
                                                </b-button>
                                                <b-button size="sm" variant="danger" @click="deleteItem('Pulse', data.index)">
                                                    <b-icon-trash />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </div>
                            </div>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <!-- Suhu Badan -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Pemeriksaan Suhu Badan</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourTemperature" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'Temperature' && editIndex !== null }"/>
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Suhu</label>
                                                    <b-input-group append="°C">
                                                        <b-form-input
                                                            type="number"
                                                            autocomplete="off"
                                                            v-model="temperature"
                                                            placeholder="Suhu"
                                                            min="0"
                                                            step="any"
                                                            style="min-width: 100px;"
                                                            :class="{ 'border-warning': editType === 'Temperature' && editIndex !== null }"
                                                        ></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <!-- <b-button size="sm" variant="success" class="mt-2" @click="addTemperature">Tambah</b-button> -->
                                             <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'Temperature' && editIndex !== null)"
                                                @click="addTemperature"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('Temperature')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                        <!-- Urin -->
                                        <div class="col-md-4">
                                            <div class="mb-1 font-weight-bold">Pemeriksaan Urin</div>
                                            <div class="d-flex align-items-end">
                                                <div class="mr-1 flex-fill">
                                                    <label class="fs-13 mb-0">Jam Observasi</label>
                                                    <b-form-select v-model="observationHourUrine" :options="labels" placeholder="Pilih jam observasi"
                                                    :class="{ 'border-warning': editType === 'Urine' && editIndex !== null }" />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Protein</label>
                                                    <b-form-select
                                                        v-model="urineProtein"
                                                        :options="urineProteinOptions"
                                                        placeholder="Pilih protein"
                                                        :class="{ 'border-warning': editType === 'Urine' && editIndex !== null }"
                                                    />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Aseton</label>
                                                    <b-form-select
                                                        v-model="urineAcetone"
                                                        :options="urineAcetoneOptions"
                                                        placeholder="Pilih aseton"
                                                        :class="{ 'border-warning': editType === 'Urine' && editIndex !== null }"
                                                    />
                                                </div>
                                                <div class="flex-fill">
                                                    <label class="fs-13 mb-0">Volume (cc)</label>
                                                    <b-input-group append="cc">
                                                        <b-form-input
                                                            type="number"
                                                            autocomplete="off"
                                                            v-model="urineVolume"
                                                            placeholder="Volume"
                                                            min="0"
                                                            step="any"
                                                            style="min-width: 80px;"
                                                        ></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <!-- <b-button size="sm" variant="success" class="mt-2" @click="addUrine">Tambah</b-button> -->
                                             <b-button
                                                size="sm"
                                                variant="success"
                                                class="mt-2"
                                                v-if="!(editType === 'Urine' && editIndex !== null)"
                                                @click="addUrine"
                                                >
                                                Tambah
                                            </b-button>
                                            <b-button
                                                size="sm"
                                                variant="primary"
                                                class="mt-2"
                                                v-else
                                                @click="updateItem('Urine')"
                                                >
                                                Perbarui
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <b-table
                                            :items="tempTemperature"
                                            :fields="[
                                                { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '25%', fontSize: '14px' } },
                                                { key: 'temperature', label: 'Suhu', thStyle: { width: '50%', fontSize: '14px' } },
                                                { key: 'actions', label: '', thStyle: { width: '25%', fontSize: '14px' } }
                                            ]"
                                            small
                                            class="mt-3"
                                        >
                                            <template #cell(observation_hours)="data">
                                                <span>{{ data.item.observation_hours }}</span>
                                            </template>
                                            <template #cell(temperature)="data">
                                                <span>{{ data.item.temperature }} °C</span>
                                            </template>
                                            <template #cell(actions)="data">
                                                <b-button size="sm" variant="warning" @click="editItem('Temperature', data.index)">
                                                    <b-icon-pencil />
                                                </b-button>
                                                <b-button size="sm" variant="danger" @click="deleteItem('Temperature', data.index)">
                                                    <b-icon-trash />
                                                </b-button>
                                            </template>
                                    </b-table>
                                </div>
                                <div class="col-md-4">
                                    <b-table
                                            :items="tempUrine"
                                            :fields="[
                                                { key: 'observation_hours', label: 'Jam Observasi', thStyle: { width: '20%', fontSize: '14px' } },
                                                { key: 'protein', label: 'Protein', thStyle: { width: '20%', fontSize: '14px' } },
                                                { key: 'acetone', label: 'Aseton', thStyle: { width: '20%', fontSize: '14px' } },
                                                { key: 'volume', label: 'Volume (cc)', thStyle: { width: '20%', fontSize: '14px' } },
                                                { key: 'actions', label: '', thStyle: { width: '20%', fontSize: '14px' } }
                                            ]"
                                            small
                                            class="mt-3"
                                        >
                                            <template #cell(observation_hours)="data">
                                                <span>{{ data.item.observation_hours }}</span>
                                            </template>
                                            <template #cell(protein)="data">
                                                <span>{{ urineProteinMap[Number(data.item.protein)] }}</span>
                                            </template>
                                            <template #cell(acetone)="data">
                                                <span>{{ urineAcetoneMap[Number(data.item.acetone)] }}</span>
                                            </template>
                                            <template #cell(volume)="data">
                                                <span>{{ data.item.volume }}</span>
                                            </template>
                                            <template #cell(actions)="data">
                                                <b-button size="sm" variant="warning" @click="editItem('Urine', data.index)">
                                                    <b-icon-pencil />
                                                </b-button>
                                                <b-button size="sm" variant="danger" @click="deleteItem('Urine', data.index)">
                                                    <b-icon-trash />
                                                </b-button>
                                            </template>
                                    </b-table>
                                </div>
                            </div>
                            <div class="text-center mb-2">
                                <b-button variant="primary" @click="handleSaveAll">
                                    <b-icon icon="save" class="mr-1" />Simpan
                                </b-button>
                                <b-button variant="outline-secondary" class="ml-2" @click="handleCancel">
                                    <b-icon icon="x-circle" class="mr-1" />Batal
                                </b-button>
                            </div>
                            <!-- <b-button size="sm" variant="primary" @click="handleSaveAll">Simpan Semua</b-button> -->
                        </b-collapse>
                        <div style="width: 100%; margin-top: 24px; ">
                            <label class="font-weight-bold fs-15 mb-0">Tekanan Darah & Nadi</label>
                            <div class="d-flex align-items-center mb-2">
                                <b-button size="sm" variant="warning" @click="toggleEditBloodPressurePulse">
                                    {{ isEditingBloodPressurePulse ? 'Batal Edit' : 'Edit Data' }}
                                </b-button>
                                <span v-if="isEditingBloodPressurePulse" class="ml-2 text-danger">
                                    Klik titik pada grafik yang ingin diedit.
                                </span>
                            </div>
                            <div v-if="showCharts">
                                <div style="width: 100%; height: 300px; margin-bottom: 24px; overflow: hidden;">
                                    <LineChartTensiNadi
                                      :chartData="getTensiNadiChartData()"
                                      @point-clicked="onBloodPressurePulsePointClicked"
                                    />
                                </div>
                                <div v-if="selectedBloodPressurePulsePoint && isEditingBloodPressurePulse" class="mt-3">
                                  <div class="d-flex flex-column align-items-center" style="width: 350px; min-height: 80px;">
                                    <div class="w-100 mb-3">
                                      <strong>Waktu:</strong> {{ selectedBloodPressurePulsePoint.label }}
                                    </div>
                                    <div class="d-flex w-100" style="gap: 8px;">
                                      <!-- Trash button column -->
                                      <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                        <b-button
                                          size="sm"
                                          variant="danger"
                                          @click="confirmDeleteBloodPressurePulsePoint"
                                          style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                        >
                                          <b-icon icon="trash" />
                                        </b-button>
                                      </div>
                                      <!-- Tekanan Darah -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Tekanan Darah (mmHg)</label>
                                        <div class="d-flex align-items-center" style="gap: 4px;">
                                          <b-form-input
                                            v-model="selectedBloodPressurePulsePoint.systolic"
                                            type="number"
                                            min="0"
                                            class="w-50"
                                            placeholder="Sistolik"
                                          />
                                          <span>/</span>
                                          <b-form-input
                                            v-model="selectedBloodPressurePulsePoint.diastolic"
                                            type="number"
                                            min="0"
                                            class="w-50"
                                            placeholder="Diastolik"
                                          />
                                        </div>
                                      </div>
                                      <!-- Nadi -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Nadi (bpm)</label>
                                        <b-form-input
                                          v-model="selectedBloodPressurePulsePoint.pulse"
                                          type="number"
                                          min="0"
                                          class="w-100"
                                          placeholder="Masukkan nadi"
                                        />
                                      </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                      <b-button
                                        size="sm"
                                        variant="success"
                                        @click="saveBloodPressurePulseEdit"
                                      >
                                        <b-icon icon="check" /> Simpan
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                                <label class="font-weight-bold fs-15 mb-0 mt-8">Suhu Badan</label>
                                <div class="d-flex align-items-center mb-2">
                                    <b-button size="sm" variant="warning" @click="toggleEditTemperature">
                                        {{ isEditingTemperature ? 'Batal Edit' : 'Edit Data' }}
                                    </b-button>
                                    <span v-if="isEditingTemperature" class="ml-2 text-danger">
                                        Klik sel pada tabel yang ingin diedit.
                                    </span>
                                </div>
                                <div style="width: 100%; margin-bottom: 24px; overflow: hidden;">
                                    <TableTemperature
                                      :labels="labels"
                                      :temperature="partograph.temperature"
                                      :isEditing="isEditingTemperature"
                                      @cell-clicked="onTemperatureCellClicked"
                                    />
                                </div>
                                <div v-if="selectedTemperaturePoint && isEditingTemperature" class="mt-3">
                                  <div class="d-flex flex-column align-items-center" style="width: 300px; min-height: 80px;">
                                    <div class="w-100 mb-3">
                                      <strong>Waktu:</strong> {{ selectedTemperaturePoint.label }}
                                    </div>
                                    <div class="d-flex w-100" style="gap: 8px;">
                                      <!-- Trash button column -->
                                      <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                        <b-button
                                          size="sm"
                                          variant="danger"
                                          @click="confirmDeleteTemperaturePoint"
                                          style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                        >
                                          <b-icon icon="trash" />
                                        </b-button>
                                      </div>
                                      <!-- Suhu -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Suhu (°C)</label>
                                        <b-form-input
                                          v-model="selectedTemperaturePoint.value"
                                          type="number"
                                          min="30"
                                          max="45"
                                          class="w-100"
                                          placeholder="Masukkan suhu"
                                        />
                                      </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                      <b-button
                                        size="sm"
                                        variant="success"
                                        @click="saveTemperatureEdit"
                                      >
                                        <b-icon icon="check" /> Simpan
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                                <label class="font-weight-bold fs-15 mb-0 mt-8">Urin</label>
                                <div class="d-flex align-items-center mb-2">
                                    <b-button size="sm" variant="warning" @click="toggleEditUrine">
                                        {{ isEditingUrine ? 'Batal Edit' : 'Edit Data' }}
                                    </b-button>
                                    <span v-if="isEditingUrine" class="ml-2 text-danger">
                                        Klik sel pada tabel yang ingin diedit.
                                    </span>
                                </div>
                                <TableUrine
                                  :labels="labels"
                                  :urine="partograph.urine"
                                  :isEditing="isEditingUrine"
                                  @cell-clicked="onUrineCellClicked"
                                />
                                <div v-if="selectedUrinePoint && isEditingUrine" class="mt-3">
                                  <div class="d-flex flex-column align-items-center" style="width: 350px; min-height: 80px;">
                                    <div class="w-100 mb-3">
                                      <strong>Waktu:</strong> {{ selectedUrinePoint.label }}
                                    </div>
                                    <div class="d-flex w-100" style="gap: 8px;">
                                      <!-- Trash button column -->
                                      <div class="d-flex flex-column justify-content-end" style="width: 38px;">
                                        <b-button
                                          size="sm"
                                          variant="danger"
                                          @click="confirmDeleteUrinePoint"
                                          style="aspect-ratio: 1 / 1; min-width: 2.5rem;"
                                        >
                                          <b-icon icon="trash" />
                                        </b-button>
                                      </div>
                                      <!-- Protein -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Protein</label>
                                        <b-form-select
                                          v-model="selectedUrinePoint.protein"
                                          :options="urineProteinOptions"
                                          class="w-100"
                                        />
                                      </div>
                                      <!-- Aseton -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Aseton</label>
                                        <b-form-select
                                          v-model="selectedUrinePoint.acetone"
                                          :options="urineAcetoneOptions"
                                          class="w-100"
                                        />
                                      </div>
                                      <!-- Volume -->
                                      <div class="flex-grow-1 d-flex flex-column">
                                        <label class="mb-1 font-weight-bold">Volume (cc)</label>
                                        <b-form-input
                                          v-model="selectedUrinePoint.volume"
                                          type="number"
                                          min="0"
                                          class="w-100"
                                          placeholder="Masukkan volume"
                                        />
                                      </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4 mb-5 w-100">
                                      <b-button
                                        size="sm"
                                        variant="success"
                                        @click="saveUrineEdit"
                                      >
                                        <b-icon icon="check" /> Simpan
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-2">
                        <b-button variant="primary" @click="handleSaveAll"> <b-icon icon="save" class="mt-8"/> Simpan</b-button>
                    </div>

                </div>
                <div v-else-if="activeTab === 'birth_notes'">
                    <div class="card shadow-sm mb-4 p-4" style="background: #fff; border-radius: 8px;" >
                        <h5 v-if="!birthNotesUpdate" class="font-weight-bold mb-3" style="color: #222;">Buat Catatan Persalinan</h5>
                        <h5 v-if="birthNotesUpdate" class="font-weight-bold mb-3" style="color: #222;">Catatan Persalinan</h5>
                        <div class="form-group row gx-5">
                            <div class="col-md-3 mr-8">
                                <label>Tanggal</label>
                                <date-picker
                                v-model="birthNotes.date_birth"
                                type="date"
                                format="YYYY-MM-DD"
                                valueType="format"
                                placeholder="Pilih tanggal"
                                style="width: 100%;"
                                />
                            </div>
                            <div class="col-md-3 ml-md-4">
                                <label>Tempat Persalinan</label>
                                <b-form-select
                                    v-model="birthNotes.birthing_place_type_id"
                                    :options="[
                                        { value: 1, text: 'Rumah Ibu' },
                                        { value: 2, text: 'Polindes' },
                                        { value: 3, text: 'Klinik Swasta' },
                                        { value: 4, text: 'Puskesmas' },
                                        { value: 5, text: 'Rumah Sakit' },
                                        { value: 6, text: 'Lainnya' }
                                    ]"
                                    placeholder="Pilih Tempat Persalinan"
                                />
                                <b-form-input
                                    v-if="birthNotes.birthing_place_type_id == 6"
                                    v-model="birthNotes.birthing_place_other"
                                    placeholder="Sebutkan tempat persalinan lainnya"
                                    class="mt-2"
                                />
                            </div>
                            <div class="col-md-3">
                                <label>Alamat Tempat Persalinan</label>
                                <b-form-input v-model="birthNotes.birthing_place_address" />
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Rujuk</label>
                                    <b-form-radio-group
                                    v-model="birthNotes.referral_type_id"
                                    :options="[
                                        { value: 1, text: 'Ya' },
                                        { value: 2, text: 'Tidak' }
                                    ]"
                                    name="referral_type_id"
                                    class="mb-2"
                                    />
                                    <div v-if="birthNotes.referral_type_id == 1" class="pl-2 mt-2">
                                        <div class="form-group row mb-2">
                                            <div class="col-md-6">
                                                <label>Kala</label>
                                                <b-form-select
                                                    v-model="birthNotes.referral_kala"
                                                    :options="[
                                                    { value: 1, text: 'I' },
                                                    { value: 2, text: 'II' },
                                                    { value: 3, text: 'III' },
                                                    { value: 4, text: 'IV' }
                                                    ]"
                                                    placeholder="Pilih Kala"
                                                />
                                            </div>
                                            <div class="col-md-6">
                                                <label>Alasan merujuk</label>
                                                <b-form-input v-model="birthNotes.referral_reason" placeholder="Alasan merujuk" />
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <div class="col-md-6">
                                                <label>Tempat rujukan</label>
                                                <b-form-input v-model="birthNotes.referral_place" placeholder="Tempat rujukan" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Pendamping pada saat merujuk</label>
                                                <b-form-radio-group
                                                    v-model="birthNotes.referral_companion_type_id"
                                                    :options="[
                                                        { value: 2, text: 'Ada' },
                                                        { value: 1, text: 'Tidak Ada' }
                                                    ]"
                                                    name="hasReferralCompanion"
                                                    class="mb-2"
                                                />
                                                <b-form-checkbox-group
                                                    v-if="birthNotes.referral_companion_type_id == 2"
                                                    v-model="birthNotes.referral_companion_arry"
                                                    :options="companionOptions"
                                                    name="referralCompanion"
                                                    class="mt-2"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Masalah dalam kehamilan/persalinan ini</label>
                                        <b-form-checkbox-group
                                            v-model="birthNotes.problem_arry"
                                            :options="[
                                                { value: 1, text: 'Gawat darurat' },
                                                { value: 2, text: 'Perdarahan' },
                                                { value: 3, text: 'HDK' },
                                                { value: 4, text: 'Infeksi' },
                                                { value: 5, text: 'PMTCT' }
                                            ]"
                                            name="problem_arry"
                                            class="d-flex flex-column"
                                        />
                                </div>

                        </div>

                        <div class="text-right mt-3" v-if="!birthNotesUpdate">
                            <b-button variant="primary" @click="handleSubmitBirthNotes">Buat Catatan Persalinan</b-button>
                        </div>
                        <div class="text-right mt-3" v-if="birthNotesUpdate">
                            <b-button variant="primary" @click="handleSaveAllBirthNotes">Simpan</b-button>
                        </div>
                    </div>
                    <div v-if="birthNotesUpdate" >
                        <!-- Top Navigation -->
                        <div class="card shadow-sm w-100 h-100" style="background: #f7f7fa; border-radius: 8px;">
                            <ul class="nav nav-pills flex-row py-2 px-2" style="overflow-x: auto;">
                                <li
                                    v-for="(page) in birthNotePages"
                                    :key="page.key"
                                    class="nav-item"
                                    style="margin-right: 8px;"
                                >
                                    <a
                                        href="#"
                                        class="nav-link birth-notes-nav-link"
                                        :class="{ 'active-birth-notes': birthNotesActivePage === page.key }"
                                        @click.prevent="birthNotesActivePage = page.key"
                                    >
                                        {{ page.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Content Area -->
                        <div class="card shadow-sm w-100 h-100" style="background: #fff; border-radius: 8px; min-height: 400px;">
                            <div class="p-4">
                                <component :is="birthNotePages.find(p => p.key === birthNotesActivePage)?.component || 'div'" />
                                <!-- Kala I Form -->
                                <div v-if="birthNotesActivePage === 'kala1'">
                                    <h5 class="font-weight-bold mb-3" style="color: #222;">Kala I</h5>
                                    <div class="form-group row align-items-center mb-3">
                                        <div class="col-md-6">
                                            <label class="col-form-label font-weight-bold">Temuan pada fase laten</label>
                                            <b-form-input
                                                v-model="birthRecordKala.kala_one_latent_phase"
                                                placeholder=""
                                                class="mt-1"
                                            />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label font-weight-bold">Perlu Intervensi:</label>
                                            <b-form-radio-group
                                                v-model="birthRecordKala.kala_one_intervention_type_id"
                                                :options="[
                                                    { value: 2, text: 'Ya' },
                                                    { value: 1, text: 'Tidak' }
                                                ]"
                                                class="ml-2"
                                            />
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-3">
                                        <label class="col-md-4 col-form-label font-weight-bold">Grafis dilatasi melewati garis waspada</label>
                                        <div class="col-md-8">
                                            <b-form-radio-group
                                                v-model="birthRecordKala.kala_one_alert_line_type_id"
                                                :options="[
                                                    { value: 2, text: 'Ya' },
                                                    { value: 1, text: 'Tidak' }
                                                ]"
                                            />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Masalah lain yang ditemukan</label>
                                            <b-form-textarea
                                                v-model="birthRecordKala.kala_one_problem"
                                                rows="3"
                                                placeholder=""
                                            />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Penatalaksanaan masalah</label>
                                            <b-form-textarea
                                                v-model="birthRecordKala.kala_one_problem_management"
                                                rows="3"
                                                placeholder=""
                                            />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Hasil penatalaksanaan masalah</label>
                                            <b-form-textarea
                                                v-model="birthRecordKala.kala_one_result"
                                                rows="3"
                                                placeholder=""
                                            />
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <b-button variant="primary" @click="handleSubmitKalaOne" :disabled="active.disabled.kalaOne">Simpan</b-button>
                                    </div>
                                </div>
                                <!-- Kala II Form -->
                                <div v-else-if="birthNotesActivePage === 'kala2'">
                                    <h5 class="font-weight-bold mb-3" style="color: #222;">Kala II</h5>
                                    <div class="form-group row align-items-center mb-3">
                                        <!-- Episiotomi -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Episiotomi</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_two_episiotomy_type_id" name="kala_two_episiotomy_type_id" class="d-flex" :options="[]">
                                                <b-form-radio :value=2 class="mr-3">
                                                    Ya
                                                    <div v-if="birthRecordKala.kala_two_episiotomy_type_id === 2" class="mt-2">
                                                        <label class="font-weight-bold mb-1" style="font-size: 13px;">Indikasi</label>
                                                        <b-form-input
                                                            v-model="birthRecordKala.kala_two_episiotomy"
                                                            placeholder=""
                                                        />
                                                    </div>
                                                </b-form-radio>
                                                <b-form-radio :value=1>Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <!-- Gawat Janin -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Gawat Janin</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_two_fetal_distress_type_id" name="kala_two_fetal_distress_type_id" class="d-flex" :options="[]">
                                                <b-form-radio :value=2 class="mr-3">
                                                    Ya
                                                    <div v-if="birthRecordKala.kala_two_fetal_distress_type_id === 2" class="mt-2">
                                                        <label class="font-weight-bold mb-1" style="font-size: 13px;">Tindakan</label>
                                                        <b-form-input
                                                            v-model="birthRecordKala.kala_two_fetal_distress"
                                                            placeholder="Tindakan yang dilakukan"
                                                        />
                                                    </div>
                                                </b-form-radio>
                                                <b-form-radio :value=1>Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-3">
                                        <!-- Pendamping pada saat persalinan -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Pendamping pada saat persalinan</label>
                                            <b-form-radio-group
                                                v-model="birthRecordKala.kala_two_companion_type_id"
                                                :options="[
                                                    { value: 2, text: 'Ada' },
                                                    { value: 1, text: 'Tidak Ada' }
                                                ]"
                                                name="kala_two_companion_type_id"
                                                class="mb-2"
                                            />
                                            <b-form-checkbox-group
                                                v-if="birthRecordKala.kala_two_companion_type_id == 2"
                                                v-model="birthRecordKala.kala_two_companion_arry"
                                                :options="companionOptions"
                                                name="kala_two_companion_arry"
                                                class="mt-2"
                                            />
                                        </div>
                                        <!-- Distosia Bahu -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Distosia Bahu</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_two_shoulder_dystocia_type_id" name="kala_two_shoulder_dystocia_type_id" class="d-flex" :options="[]">
                                                <b-form-radio :value=2 class="mr-3">
                                                    Ya
                                                    <div v-if="birthRecordKala.kala_two_shoulder_dystocia_type_id === 2" class="mt-2">
                                                        <label class="font-weight-bold mb-1" style="font-size: 13px;">Tindakan</label>
                                                        <b-form-input
                                                            v-model="birthRecordKala.kala_two_shoulder_dystocia"
                                                            placeholder="Tindakan yang dilakukan"
                                                        />
                                                    </div>
                                                </b-form-radio>
                                                <b-form-radio :value=1>Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Masalah lain yang ditemukan</label>
                                            <b-form-textarea
                                                v-model="birthRecordKala.kala_two_problem"
                                                rows="3"
                                                placeholder=""
                                            />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Penatalaksanaan masalah</label>
                                            <b-form-textarea
                                                v-model="birthRecordKala.kala_two_problem_management"
                                                rows="3"
                                                placeholder=""
                                            />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Hasil penatalaksanaan masalah</label>
                                            <b-form-textarea
                                                v-model="birthRecordKala.kala_two_result"
                                                rows="3"
                                                placeholder=""
                                            />
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <b-button variant="primary" @click="handleSubmitKalaTwo" :disabled="active.disabled.kalaTwo">Simpan</b-button>
                                    </div>
                                </div>
                                <!-- Kala III Form -->
                                <div v-else-if="birthNotesActivePage === 'kala3'">
                                    <h5 class="font-weight-bold mb-3" style="color: #222;">Kala III</h5>
                                    <!-- Row 1: Inisiasi Menyusu Dini -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Inisiasi Menyusu Dini</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_initiation_type_id" name="kala_three_initiation_type_id" class="d-flex flex-column">
                                                <b-form-radio :value=2 class="mb-1">Ya</b-form-radio>
                                                <b-form-radio :value=1 class="mb-1">
                                                    Tidak
                                                    <div v-if="birthRecordKala.kala_three_initiation_type_id === 1" class="mt-2 d-block w-100">
                                                        <label class="font-weight-bold mb-1" style="font-size: 13px;">Alasan</label>
                                                        <b-form-input v-model="birthRecordKala.kala_three_initiation" placeholder="" />
                                                    </div>
                                                </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <!-- Row 2: Lama Kala III -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Lama Kala III</label>
                                            <b-input-group append="menit">
                                                <b-form-input v-model="birthRecordKala.kala_three_long" type="number" min="0" placeholder="" />
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <!-- Row 3: Oksitosin & Oksitosan Ulang -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <!-- Pemberian Oksitosin 10 U im -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Pemberian Oksitosin 10 U im</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_oxytocin_type_id" name="kala_three_oxytocin_type_id" class="d-flex flex-column">
                                                <b-form-radio :value=2 class="mb-1">
                                                    Ya
                                                    <div v-if="birthRecordKala.kala_three_oxytocin_type_id === 2" class="mt-2 d-block w-100">
                                                        <label class="font-weight-bold mb-1" style="font-size: 13px;">Waktu</label>
                                                        <b-input-group append="menit sesudah persalinan">
                                                            <b-form-input v-model="birthRecordKala.kala_three_oxytocin" type="number" min="0" placeholder="" />
                                                        </b-input-group>
                                                    </div>
                                                </b-form-radio>
                                                <b-form-radio :value=1 class="mb-1">
                                                    Tidak
                                                    <div v-if="birthRecordKala.kala_three_oxytocin_type_id === 1" class="mt-2 d-block w-100">
                                                        <label class="font-weight-bold mb-1" style="font-size: 13px;">Alasan</label>
                                                        <b-form-input v-model="birthRecordKala.kala_three_oxytocin_reason" placeholder="" />
                                                    </div>
                                                </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <!-- Pemberian ulang Oksitosan (2x) -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Pemberian ulang Oksitosan (2x)</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_oxytocin_second_type_id" name="kala_three_oxytocin_second_type_id" class="d-flex flex-column">
                                            <b-form-radio :value=2 class="mb-1">
                                                Ya
                                                <div v-if="birthRecordKala.kala_three_oxytocin_second_type_id === 2" class="mt-2 d-block w-100">
                                                    <label class="font-weight-bold mb-1" style="font-size: 13px;">Alasan</label>
                                                    <b-form-input v-model="birthRecordKala.kala_three_oxytocin_second" placeholder="" />
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio :value=1 class="mb-1">Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <!-- Row 4: Penegangan tali pusat & Massase fundus uteri -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <!-- Penegangan tali pusat terkendali -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Penegangan tali pusat terkendali</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_umbilical_cord_type_id" name="kala_three_umbilical_cord_type_id" class="d-flex flex-column">
                                            <b-form-radio :value=2 class="mb-1">Ya</b-form-radio>
                                            <b-form-radio :value=1 class="mb-1">
                                                Tidak
                                                <div v-if="birthRecordKala.kala_three_umbilical_cord_type_id === 1" class="mt-2 d-block w-100">
                                                    <label class="font-weight-bold mb-1" style="font-size: 13px;">Alasan</label>
                                                    <b-form-input v-model="birthRecordKala.kala_three_umbilical_cord" placeholder="" />
                                                </div>
                                            </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <!-- Massase fundus uteri -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Massase fundus uteri</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_uterine_fundus_type_id" name="kala_three_uterine_fundus_type_id" class="d-flex flex-column">
                                            <b-form-radio :value=2 class="mb-1">Ya</b-form-radio>
                                            <b-form-radio :value=1 class="mb-1">
                                                Tidak
                                                <div v-if="birthRecordKala.kala_three_uterine_fundus_type_id === 1" class="mt-2 d-block w-100">
                                                    <label class="font-weight-bold mb-1" style="font-size: 13px;">Alasan</label>
                                                    <b-form-input v-model="birthRecordKala.kala_three_uterine_fundus" placeholder="" />
                                                </div>
                                            </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <!-- Row 5: Plasenta -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <!-- Plasenta dahi lengkap (intact) -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Plasenta lahir lengkap (intact)</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_placenta_type_id" name="kala_three_placenta_type_id" class="d-flex flex-column">
                                                <b-form-radio :value=2 class="mb-1">Ya</b-form-radio>
                                                <b-form-radio :value=1 class="mb-1">
                                                    Tidak
                                                    <div v-if="birthRecordKala.kala_three_placenta_type_id === 1" class="mt-2 d-block w-100">
                                                        <label class="font-weight-bold mb-1" style="font-size: 13px;">Tindakan</label>
                                                        <b-form-textarea v-model="birthRecordKala.kala_three_placenta" placeholder="Tindakan yang dilakukan" rows="2" />
                                                    </div>
                                                </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <!-- Plasenta tidak lahir >30 menit -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Plasenta tidak lahir &gt;30 menit</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_placenta_not_type_id" name="kala_three_placenta_not_type_id" class="d-flex flex-column">
                                            <b-form-radio :value=2 class="mb-1">
                                                Ya
                                                <div v-if="birthRecordKala.kala_three_placenta_not_type_id === 2" class="mt-2 d-block w-100">
                                                    <label class="font-weight-bold mb-1" style="font-size: 13px;">Tindakan</label>
                                                    <b-form-textarea v-model="birthRecordKala.kala_three_placenta_not" placeholder="Tindakan yang dilakukan" rows="2" />
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio :value=1 class="mb-1">Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <!-- Row 6: Atoni uteri & Jumlah darah -->
                                    <div class="form-group row align-items-center mb-3">
                                        <!-- Atoni uteri -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Atoni uteri</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_uterine_atony_type_id" name="kala_three_uterine_atony_type_id" class="d-flex flex-column">
                                            <b-form-radio :value=2 class="mb-1">
                                                Ya
                                                <div v-if="birthRecordKala.kala_three_uterine_atony_type_id === 2" class="mt-2 d-block w-100">
                                                    <label class="font-weight-bold mb-1" style="font-size: 13px;">Tindakan</label>
                                                    <b-form-textarea v-model="birthRecordKala.kala_three_uterine_atony" placeholder="Tindakan yang dilakukan" rows="2" />
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio :value=1 class="mb-1">Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <!-- Jumlah darah yang keluar/perdarahan -->
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Jumlah darah yang keluar/perdarahan</label>
                                            <b-input-group append="ml">
                                                <b-form-input v-model="birthRecordKala.kala_three_amount_blood" type="number" min="0" placeholder="" />
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <!-- Row 7: Laserasi -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <div class="col-md-12">
                                            <label class="font-weight-bold">Laserasi</label>
                                            <b-form-radio-group v-model="birthRecordKala.kala_three_laceration_type_id" name="kala_three_laceration_type_id" class="d-flex flex-column">
                                                <b-form-radio :value=2 class="mb-1">
                                                    Ya
                                                    <div v-if="birthRecordKala.kala_three_laceration_type_id === 2" class="mt-2 d-block w-100">
                                                    <label class="font-weight-bold mb-1" style="font-size: 13px;">Dimana</label>
                                                    <b-form-textarea v-model="birthRecordKala.kala_three_laceration" placeholder="" rows="2" class="mb-2" />
                                                    <label class="font-weight-bold">Jika laserasi parineum, derajat:</label>
                                                    <b-form-radio-group v-model="birthRecordKala.kala_three_laceration_degree" name="kala_three_laceration_degree" class="d-flex flex-row mb-2">
                                                        <b-form-radio :value=1 class="mr-2">1</b-form-radio>
                                                        <b-form-radio :value=2 class="mr-2">2</b-form-radio>
                                                        <b-form-radio :value=3 class="mr-2">3</b-form-radio>
                                                        <b-form-radio :value=4>4</b-form-radio>
                                                    </b-form-radio-group>
                                                    <label class="font-weight-bold">Tindakan</label>
                                                    <b-form-radio-group v-model="birthRecordKala.kala_three_laceration_suture" name="kala_three_laceration_suture" class="d-flex flex-row mb-2">
                                                        <b-form-radio :value=2 class="mr-2">
                                                        Penjahitan
                                                        <div v-if="birthRecordKala.kala_three_laceration_suture === 2" class="mt-2 d-block w-100">
                                                            <label class="font-weight-bold">Anestesi</label>
                                                            <b-form-radio-group v-model="birthRecordKala.kala_three_laceration_anesthesia" name="kala_three_laceration_anesthesia" class="d-flex flex-row">
                                                            <b-form-radio :value=2>Dengan anestesi</b-form-radio>
                                                            <b-form-radio :value=1>Tanpa anestesi</b-form-radio>
                                                            </b-form-radio-group>
                                                        </div>
                                                        </b-form-radio>
                                                        <b-form-radio :value=1>
                                                        Tidak dijahit
                                                        <div v-if="birthRecordKala.kala_three_laceration_suture === 1" class="mt-2 d-block w-100">
                                                            <label class="font-weight-bold mb-1" style="font-size: 13px;">Alasan</label>
                                                            <b-form-input v-model="birthRecordKala.kala_three_laceration_not_suture_reason" placeholder="" />
                                                        </div>
                                                        </b-form-radio>
                                                    </b-form-radio-group>
                                                    </div>
                                                </b-form-radio>
                                                <b-form-radio :value=1 class="mb-1">Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <!-- Row 8: Masalah lain -->
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Masalah lain yang ditemukan</label>
                                            <b-form-textarea v-model="birthRecordKala.kala_three_problem" rows="3" placeholder="" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Penatalaksanaan masalah</label>
                                            <b-form-textarea v-model="birthRecordKala.kala_three_problem_management" rows="3" placeholder="" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Hasil penatalaksanaan masalah</label>
                                            <b-form-textarea v-model="birthRecordKala.kala_three_result" rows="3" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <b-button variant="primary" @click="handleSubmitKalaThree" :disabled="active.disabled.kalaThree">Simpan</b-button>
                                    </div>
                                </div>
                                <!-- Kala IV Form -->
                                <div v-else-if="birthNotesActivePage === 'kala4'">
                                    <h5 class="font-weight-bold mb-3" style="color: #222;">Kala IV</h5>
                                    <!-- Row 1: Kondisi Umum & Tekanan Darah -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Kondisi Umum</label>
                                            <b-form-select
                                            v-model="birthRecordKala.kala_four_mother_ku"
                                            :options="[
                                                { value: 1, text: 'Baik' },
                                                { value: 2, text: 'Sedang' },
                                                { value: 3, text: 'Lemah' }
                                            ]"
                                            placeholder="Pilih kondisi umum"
                                            />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Tekanan Darah</label>
                                            <b-input-group append="mmHg">
                                                <b-form-input
                                                    v-model="birthRecordKala.kala_four_systolic"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Sistolik"
                                                    style="max-width: 100px;"
                                                />
                                                <span class="mx-1" style="font-weight: bold;">/</span>
                                                <b-form-input
                                                    v-model="birthRecordKala.kala_four_diastolic"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Diastolik"
                                                    style="max-width: 100px;"
                                                />
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <!-- Row 2: Nadi & Nafas -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Nadi</label>
                                            <b-input-group append="bpm">
                                                <b-form-input
                                                    v-model="birthRecordKala.kala_four_pulse"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Nadi"
                                                />
                                            </b-input-group>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="font-weight-bold">Nafas</label>
                                            <b-input-group append="x/menit">
                                                <b-form-input
                                                    v-model="birthRecordKala.kala_four_breath"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Nafas"
                                                />
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <!-- Row 3: Masalah, Penatalaksanaan, Hasil -->
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Masalah lain yang ditemukan</label>
                                            <b-form-textarea v-model="birthRecordKala.kala_four_problem" rows="3" placeholder="Masukkan masalah lain..." />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Penatalaksanaan masalah</label>
                                            <b-form-textarea v-model="birthRecordKala.kala_four_problem_management" rows="3" placeholder="Masukkan penatalaksanaan..." />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Hasil penatalaksanaan masalah</label>
                                            <b-form-textarea v-model="birthRecordKala.kala_four_result" rows="3" placeholder="Masukkan hasil penatalaksanaan..." />
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <b-button variant="primary" @click="handleSubmitKalaFour" :disabled="active.disabled.kalaFour">Simpan</b-button>
                                    </div>
                                </div>
                                <!-- Bayi Baru Lahir Form -->
                                <div v-else-if="birthNotesActivePage === 'bayi_lahir'">
                                    <h5 class="font-weight-bold mb-3" style="color: #222;">Bayi Baru Lahir</h5>
                                    <!-- Row 1 -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Berat badan</label>
                                            <b-input-group append="gram">
                                                <b-form-input v-model="newbornBaby.newborn_baby_weight" type="number" min="0" />
                                            </b-input-group>
                                            </div>
                                            <div class="col-md-3">
                                            <label class="font-weight-bold">Panjang badan</label>
                                            <b-input-group append="cm">
                                                <b-form-input v-model="newbornBaby.newborn_baby_height" type="number" min="0" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Jenis kelamin</label>
                                            <b-form-radio-group v-model="newbornBaby.newborn_baby_sex_type_id" :options="[
                                                { value: 1, text: 'Laki-laki' },
                                                { value: 2, text: 'Perempuan' }
                                            ]" name="bayiJenisKelamin" class="d-flex flex-row" />
                                        </div>
                                    </div>
                                    <!-- Row 2 -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                    <div class="col-md-12">
                                    <label class="font-weight-bold">Penilaian bayi baru lahir </label>
                                      <b-form-radio-group
                                        v-model="newbornBaby.newborn_baby_assessment_type_id"
                                        :options="[
                                        { value: 1, text: 'Normal (Baik)' },
                                        { value: 2, text: 'Ada penyulit' }
                                        ]"
                                        name="newborn_baby_assessment_type_id"
                                        class="d-flex flex-row mb-3"
                                        />
                                    <div v-if="newbornBaby.newborn_baby_assessment_type_id == 1" class="mt-2">
                                        <label class="font-weight-bold" style="font-size: 13px;">Tindakan</label>
                                        <b-form-checkbox-group v-model="newbornBaby.newborn_baby_normal_arry">
                                            <div class="row">
                                            <div class="col-md-6" v-for="(option, idx) in bayiLahirTindakanNormalOptions" :key="option.value">
                                                <b-form-checkbox :value="option.value" class="mb-2">
                                                {{ option.text }}
                                                </b-form-checkbox>
                                            </div>
                                            </div>
                                        </b-form-checkbox-group>
                                    </div>
                                     <!-- If Ada penyulit -->
                                    <div v-if="newbornBaby.newborn_baby_assessment_type_id == 2" class="mt-2">
                                      <label class="font-weight-bold" style="font-size: 13px;">Pilih penyulit</label>
                                      <div class="d-flex flex-column">
                                        <!-- Asfiksia -->
                                        <b-form-checkbox
                                          v-model="newbornBaby.newborn_baby_abnormal_arry"
                                          :value="1"
                                          class="mb-2"
                                        >
                                          Asfiksia
                                        <div v-if="newbornBaby.newborn_baby_abnormal_arry.includes(1)" class="mt-2 ml-4">
                                            <label class="font-weight-bold" style="font-size: 13px;">Tindakan Asfiksia</label>
                                            <b-form-checkbox-group v-model="newbornBaby.newborn_baby_asphyxia_arry">
                                                <div class="row">
                                                <div class="col-md-6" v-for="(option, idx) in bayiLahirTindakanAsfiksiaOptions" :key="option.value">
                                                    <b-form-checkbox :value="option.value" class="mb-2">
                                                    {{ option.text }}
                                                    </b-form-checkbox>
                                                </div>
                                                </div>
                                            </b-form-checkbox-group>
                                            <div v-if="newbornBaby.newborn_baby_asphyxia_arry.includes(7)" class="mt-2">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px;">Sebutkan</label>
                                                <b-form-input v-model="newbornBaby.newborn_baby_asphyxia_others" />
                                            </div>
                                        </div>
                                        </b-form-checkbox>

                                        <!-- Cacat Bawaan -->
                                        <b-form-checkbox
                                          v-model="newbornBaby.newborn_baby_abnormal_arry"
                                          :value="2"
                                          class="mb-2"
                                        >
                                          Cacat Bawaan
                                          <div v-if="newbornBaby.newborn_baby_abnormal_arry.includes(2)" class="mt-2 ml-4">
                                            <label class="font-weight-bold mb-1" style="font-size: 13px;">Sebutkan Cacat Bawaan</label>
                                            <b-form-input v-model="newbornBaby.newborn_baby_disabled" />
                                          </div>
                                        </b-form-checkbox>

                                        <!-- Hipotermi -->
                                        <b-form-checkbox
                                          v-model="newbornBaby.newborn_baby_abnormal_arry"
                                          :value="3"
                                          class="mb-2"
                                        >
                                          Hipotermi
                                          <div v-if="newbornBaby.newborn_baby_abnormal_arry.includes(3)" class="mt-2 ml-4">
                                            <label class="font-weight-bold mb-1" style="font-size: 13px;">Tindakan Hipotermi</label>
                                            <b-form-textarea v-model="newbornBaby.newborn_baby_hypothermia" rows="2" />
                                          </div>
                                        </b-form-checkbox>
                                      </div>
                                    </div>
                                </div>
                                    </div>
                                    <!-- Row 3: Pemberian ASI -->
                                    <div class="form-group row align-items-center mb-3 g-2">
                                        <div class="col-md-12">
                                        <label class="font-weight-bold">Pemberian ASI setelah jam pertama bayi lahir</label>
                                        <b-form-radio-group v-model="newbornBaby.newborn_baby_breast_milk_type_id" name="newborn_baby_breast_milk_type_id" class="d-flex flex-row">
                                            <b-form-radio :value=2 class="mr-3">
                                            Ya
                                            <div v-if="newbornBaby.newborn_baby_breast_milk_type_id === 2" class="mt-2 d-block w-100">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px;">Waktu</label>
                                                <b-input-group append="jam setelah bayi lahir">
                                                <b-form-input v-model="newbornBaby.newborn_baby_breast_milk_hours" type="number" min="0" />
                                                </b-input-group>
                                            </div>
                                            </b-form-radio>
                                            <b-form-radio :value=1>
                                            Tidak
                                            <div v-if="newbornBaby.newborn_baby_breast_milk_type_id === 1" class="mt-2 d-block w-100">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px;">Alasan</label>
                                                <b-form-input v-model="newbornBaby.newborn_baby_breast_milk_not" />
                                            </div>
                                            </b-form-radio>
                                        </b-form-radio-group>
                                        </div>
                                    </div>
                                    <!-- Row 4: Masalah, Penatalaksanaan, Hasil -->
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                        <label class="font-weight-bold">Masalah lain yang ditemukan</label>
                                        <b-form-textarea v-model="newbornBaby.newborn_baby_problem" rows="3" />
                                        </div>
                                        <div class="col-md-4">
                                        <label class="font-weight-bold">Penatalaksanaan masalah</label>
                                        <b-form-textarea v-model="newbornBaby.newborn_baby_problem_management" rows="3" />
                                        </div>
                                        <div class="col-md-4">
                                        <label class="font-weight-bold">Hasil penatalaksanaan masalah</label>
                                        <b-form-textarea v-model="newbornBaby.newborn_baby_result" rows="3" />
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <b-button variant="primary" @click="handleSubmitNewborn" :disabled="active.disabled.newborn">Simpan</b-button>
                                    </div>
                                </div>
                                <!-- Pemantauan Persalinan Table -->
                                <div v-else-if="birthNotesActivePage === 'pemantauan_persalinan'">
                                <h5 class="font-weight-bold mb-3" style="color: #222;">Pemantauan Persalinan</h5>
                                <div v-if="hasUnsavedChanges" class="alert alert-warning unsaved-alert">
                                    Ada perubahan yang belum disimpan. Klik tombol simpan di kanan bawah sebelum beralih halaman.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center align-middle" style="background: #fff;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width:60px;">Jam Ke</th>
                                                <th style="width:90px;">Waktu</th>
                                                <th style="width:200px;">Tekanan Darah</th>
                                                <th style="width:120px;">Nadi</th>
                                                <th style="width:120px;">Suhu</th>
                                                <th style="width:170px;">Tinggi Fundus Uteri</th>
                                                <th style="width:90px;">Kontraksi Uterus</th>
                                                <th style="width:120px;">Kandung Kemih</th>
                                                <th style="width:120px;">Perdarahan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, rowIdx) in pemantauanPersalinanRows" :key="rowIdx">
                                                <!-- Jam Ke -->
                                                <td
                                                v-if="rowIdx === 0"
                                                :rowspan="4"
                                                style="background:#fcf8f5; vertical-align:middle; user-select:none; text-align:center;"
                                                >
                                                1
                                                </td>
                                                <td
                                                v-if="rowIdx === 4"
                                                :rowspan="pemantauanPersalinanRows.length - 4"
                                                style="background:#fcf8f5; vertical-align:middle; user-select:none; text-align:center;"
                                                >
                                                2
                                                </td>
                                                <!-- Waktu -->
                                                <td
                                                :style="isSavedCell(rowIdx, 'time') ? 'background:#fcf8f5;' : ''"
                                                @dblclick="handleCellDblClick(rowIdx, 'time')"
                                                class="text-center align-middle position-relative"
                                                >
                                                    <template v-if="!isSavedCell(rowIdx, 'time') || isEditingCell(rowIdx, 'time')">
                                                        <b-form-input
                                                        :class="`cell-input-${rowIdx}-time`"
                                                        v-model="row.time"
                                                        type="time"
                                                        @keydown="handleCellKeydown($event, rowIdx, 'time')"
                                                        @blur="saveCell(rowIdx, 'time')"
                                                        />
                                                    </template>
                                                    <template v-else>
                                                        <span
                                                        @click="enterEditCell(rowIdx, 'time')"
                                                        style="cursor:pointer; display: block; width: 100%;"
                                                        >{{ formatTime(row.time) }}</span>
                                                        <b-button
                                                        variant="link"
                                                        size="sm"
                                                        class="cell-edit-btn"
                                                        @click.stop="enterEditCell(rowIdx, 'time')"
                                                        aria-label="Edit waktu"
                                                        title="Edit"
                                                        >
                                                        <b-icon icon="pencil" />
                                                        </b-button>
                                                    </template>
                                                </td>
                                                <!-- Tekanan Darah -->
                                                <td
                                                  :style="(isSavedCell(rowIdx, 'systolic') && isSavedCell(rowIdx, 'diastolic')) ? 'background:#fcf8f5;' : ''"
                                                  @dblclick="handleCellDblClick(rowIdx, 'systolic')"
                                                  class="text-center align-middle position-relative"
                                                >
                                                  <template v-if="!isSavedCell(rowIdx, 'systolic') || isEditingCell(rowIdx, 'systolic')">
                                                    <div style="display: flex; align-items: center;"
                                                        @focusout="handleBloodPressureBlur(rowIdx)"
                                                        tabindex="0">
                                                      <b-form-input
                                                        :class="`cell-input-${rowIdx}-systolic`"
                                                        v-model.number="row.systolic"
                                                        type="number"
                                                        min="0"
                                                        @keydown="handleBloodPressureKeydown($event, rowIdx)"
                                                      />
                                                      <span class="mx-1" style="font-weight:bold;">/</span>
                                                      <b-form-input
                                                        :class="`cell-input-${rowIdx}-diastolic`"
                                                        v-model.number="row.diastolic"
                                                        type="number"
                                                        min="0"
                                                        @keydown="handleBloodPressureKeydown($event, rowIdx)"
                                                      />
                                                      <span class="ml-1">mmHg</span>
                                                    </div>
                                                  </template>
                                                <template v-else>
                                                    <span @click="enterEditCell(rowIdx, 'systolic')" style="cursor:pointer; display: block; width: 100%;">
                                                    {{ row.systolic }}/{{ row.diastolic }} mmHg
                                                    </span>
                                                    <b-button
                                                    variant="link"
                                                    size="sm"
                                                    class="cell-edit-btn"
                                                    @click.stop="enterEditCell(rowIdx, 'systolic')"
                                                    aria-label="Edit Tekanan Darah"
                                                    title="Edit"
                                                    >
                                                    <b-icon icon="pencil" />
                                                    </b-button>
                                                </template>
                                                </td>
                                                <!-- Nadi -->
                                                <td
                                                :style="isSavedCell(rowIdx, 'pulse') ? 'background:#fcf8f5;' : ''"
                                                @dblclick="handleCellDblClick(rowIdx, 'pulse')"
                                                class="text-center align-middle position-relative"
                                                >
                                                    <template v-if="!isSavedCell(rowIdx, 'pulse') || isEditingCell(rowIdx, 'pulse')">
                                                        <div style="display: flex; align-items: center;">
                                                            <b-form-input
                                                            :class="`cell-input-${rowIdx}-pulse`"
                                                            v-model.number="row.pulse"
                                                            type="number"
                                                            min="0"
                                                            @keydown="handleCellKeydown($event, rowIdx, 'pulse')"
                                                            @blur="saveCell(rowIdx, 'pulse')"
                                                            style="width: 60%;"
                                                            />
                                                            <span class="ml-1" style="width: 40%;">bpm</span>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <span
                                                        @click="enterEditCell(rowIdx, 'pulse')"
                                                        style="cursor:pointer; display: block; width: 100%;"
                                                        >{{ row.pulse }} bpm</span>
                                                        <b-button
                                                        variant="link"
                                                        size="sm"
                                                        class="cell-edit-btn"
                                                        @click.stop="enterEditCell(rowIdx, 'pulse')"
                                                        aria-label="Edit Nadi"
                                                        title="Edit"
                                                        >
                                                        <b-icon icon="pencil" />
                                                        </b-button>
                                                    </template>
                                                </td>
                                                <!-- Suhu -->
                                                <td
                                                v-if="[1,2,3,5].includes(rowIdx)"
                                                :style="'background:#4b4b4b;'"
                                                class="text-center align-middle"
                                                >
                                                <!-- Blocked cell: no input, just black -->
                                                </td>
                                                <td
                                                v-else
                                                :style="isSavedCell(rowIdx, 'temperature') ? 'background:#fcf8f5;' : ''"
                                                @dblclick="handleCellDblClick(rowIdx, 'temperature')"
                                                class="text-center align-middle position-relative"
                                                >
                                                    <template v-if="!isSavedCell(rowIdx, 'temperature') || isEditingCell(rowIdx, 'temperature')">
                                                        <div style="display: flex; align-items: center;">
                                                            <b-form-input
                                                            :class="`cell-input-${rowIdx}-temperature`"
                                                            v-model.number="row.temperature"
                                                            type="number"
                                                            style="width: 75%;"
                                                            min="0"
                                                            @keydown="handleCellKeydown($event, rowIdx, 'temperature')"
                                                            @blur="saveCell(rowIdx, 'temperature')"
                                                            />
                                                            <span class="ml-1" style="width: 25%;">°C</span>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <span
                                                        @click="enterEditCell(rowIdx, 'temperature')"
                                                        style="cursor:pointer; display: block; width: 100%;"
                                                        >{{ row.temperature }} °C</span>
                                                        <b-button
                                                        variant="link"
                                                        size="sm"
                                                        class="cell-edit-btn"
                                                        @click.stop="enterEditCell(rowIdx, 'temperature')"
                                                        aria-label="Edit suhu"
                                                        title="Edit"
                                                        >
                                                        <b-icon icon="pencil" />
                                                        </b-button>
                                                    </template>
                                                </td>
                                                <!-- Tinggi Fundus Uteri -->
                                                <td
                                                :style="isSavedCell(rowIdx, 'fundal_height') ? 'background:#fcf8f5;' : ''"
                                                @dblclick="handleCellDblClick(rowIdx, 'fundal_height')"
                                                class="text-center align-middle position-relative"
                                                >
                                                    <template v-if="!isSavedCell(rowIdx, 'fundal_height') || isEditingCell(rowIdx, 'fundal_height')">
                                                        <b-form-select
                                                        :class="`cell-input-${rowIdx}-fundal_height`"
                                                        v-model="row.fundal_height"
                                                        :options="[
                                                            { value: null, text: '-' },
                                                            { value: 1, text: '1' },
                                                            { value: 2, text: '2' },
                                                            { value: 3, text: '3' }
                                                        ]"
                                                        @change="saveCell(rowIdx, 'fundal_height')"
                                                        @keydown="handleCellKeydown($event, rowIdx, 'fundal_height')"
                                                        placeholder="Pilih tinggi fundus"
                                                        />
                                                    </template>
                                                    <template v-else>
                                                        <span
                                                        @click="enterEditCell(rowIdx, 'fundal_height')"
                                                        style="cursor:pointer; display: block; width: 100%; "
                                                        >{{ row.fundal_height }} jari di bawah pusar</span>
                                                        <b-button
                                                        variant="link"
                                                        size="sm"
                                                        class="cell-edit-btn"
                                                        @click.stop="enterEditCell(rowIdx, 'fundal_height')"
                                                        aria-label="Edit Tinggi Fundus Uteri"
                                                        title="Edit"
                                                        >
                                                        <b-icon icon="pencil" />
                                                        </b-button>
                                                    </template>
                                                </td>
                                                <!-- Kontraksi Uterus -->
                                                <td
                                                :style="isSavedCell(rowIdx, 'uterus_contraction') ? 'background:#fcf8f5;' : ''"
                                                @dblclick="handleCellDblClick(rowIdx, 'uterus_contraction')"
                                                class="text-center align-middle position-relative"
                                                >
                                                <template v-if="!isSavedCell(rowIdx, 'uterus_contraction') || isEditingCell(rowIdx, 'uterus_contraction')">
                                                    <b-form-select
                                                    :class="`cell-input-${rowIdx}-uterus_contraction`"
                                                    v-model="row.uterus_contraction"
                                                    :options="kontraksiOptions"
                                                    @change="saveCell(rowIdx, 'uterus_contraction')"
                                                    @keydown="handleCellKeydown($event, rowIdx, 'uterus_contraction')"
                                                    @blur="saveCell(rowIdx, 'uterus_contraction')"
                                                    placeholder="Pilih kontraksi uterus"
                                                    />
                                                </template>
                                                <template v-else>
                                                    <span
                                                    @click="enterEditCell(rowIdx, 'uterus_contraction')"
                                                    style="cursor:pointer; display: block; width: 100%;"
                                                    >{{ kontraksiMap[row.uterus_contraction] || '' }}</span>
                                                    <b-button
                                                    variant="link"
                                                    size="sm"
                                                    class="cell-edit-btn"
                                                    @click.stop="enterEditCell(rowIdx, 'uterus_contraction')"
                                                    aria-label="Edit Kontraksi Uterus"
                                                    title="Edit"
                                                    >
                                                    <b-icon icon="pencil" />
                                                    </b-button>
                                                </template>
                                                </td>
                                                <!-- Kandung Kemih -->
                                               <td
                                                :style="isSavedCell(rowIdx, 'bladder_volume') ? 'background:#fcf8f5;' : ''"
                                                @dblclick="handleCellDblClick(rowIdx, 'bladder_volume')"
                                                class="text-center align-middle position-relative"
                                                >
                                                <template v-if="!isSavedCell(rowIdx, 'bladder_volume') || isEditingCell(rowIdx, 'bladder_volume')">
                                                    <div style="display: flex; align-items: center;">
                                                        <b-form-input
                                                        :class="`cell-input-${rowIdx}-bladder_volume`"
                                                        v-model.number="row.bladder_volume"
                                                        type="number"
                                                        min="0"
                                                        style="width: 75%;"
                                                        @keydown="handleCellKeydown($event, rowIdx, 'bladder_volume')"
                                                        @blur="saveCell(rowIdx, 'bladder_volume')"
                                                        />
                                                        <span class="ml-1" style="width: 25%;">cc</span>
                                                    </div>

                                                </template>
                                                <template v-else>
                                                    <span
                                                    @click="enterEditCell(rowIdx, 'bladder_volume')"
                                                    style="cursor:pointer; display: block;"
                                                    >{{ row.bladder_volume }} cc</span>
                                                    <b-button
                                                    variant="link"
                                                    size="sm"
                                                    class="cell-edit-btn"
                                                    @click.stop="enterEditCell(rowIdx, 'bladder_volume')"
                                                    aria-label="Edit Kandung Kemih"
                                                    title="Edit"
                                                    >
                                                    <b-icon icon="pencil" />
                                                    </b-button>
                                                </template>
                                                </td>

                                                <!-- Perdarahan -->
                                                <td
                                                :style="isSavedCell(rowIdx, 'bleeding_volume') ? 'background:#fcf8f5;' : ''"
                                                @dblclick="handleCellDblClick(rowIdx, 'bleeding_volume')"
                                                class="text-center align-middle position-relative"
                                                >
                                                <template v-if="!isSavedCell(rowIdx, 'bleeding_volume') || isEditingCell(rowIdx, 'bleeding_volume')">
                                                    <div style="display: flex; align-items: center;">
                                                        <b-form-input
                                                        :class="`cell-input-${rowIdx}-bleeding_volume`"
                                                        v-model.number="row.bleeding_volume"
                                                        type="number"
                                                        min="0"
                                                        style="width: 75%;"
                                                        @keydown="handleCellKeydown($event, rowIdx, 'bleeding_volume')"
                                                        @blur="saveCell(rowIdx, 'bleeding_volume')"
                                                        />
                                                        <span class="ml-1" style="width: 25%;">cc</span>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <span
                                                    @click="enterEditCell(rowIdx, 'bleeding_volume')"
                                                    style="cursor:pointer; display: block; width: 100%;"
                                                    >{{ row.bleeding_volume }} cc</span>
                                                    <b-button
                                                    variant="link"
                                                    size="sm"
                                                    class="cell-edit-btn"
                                                    @click.stop="enterEditCell(rowIdx, 'bleeding_volume')"
                                                    aria-label="Edit Perdarahan"
                                                    title="Edit"
                                                    >
                                                    <b-icon icon="pencil" />
                                                    </b-button>
                                                </template>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Row 4: Masalah, Penatalaksanaan, Hasil -->
                                <!-- <div class="form-group row">
                                    <div class="col-md-4">
                                    <label class="font-weight-bold">Masalah lain yang ditemukan</label>
                                    <b-form-textarea v-model="pemantauanMasalah" rows="3" />
                                    </div>
                                    <div class="col-md-4">
                                    <label class="font-weight-bold">Penatalaksanaan masalah</label>
                                    <b-form-textarea v-model="pemantauanPenatalaksanaan" rows="3" />
                                    </div>
                                    <div class="col-md-4">
                                    <label class="font-weight-bold">Hasil penatalaksanaan masalah</label>
                                    <b-form-textarea v-model="pemantauanHasil" rows="3" />
                                    </div>
                                </div> -->
                                <div class="text-right mt-3">
                                    <b-button variant="primary" @click="handleSubmitPemantauanPersalinan" :disabled="active.disabled.kalaOne">Simpan</b-button>
                                </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <b-modal
        id="delete-modal"
        ref="deleteModal"
        title="Konfirmasi Hapus Data"
        @ok="handleDeleteConfirmed"
        ok-title="Hapus"
        ok-variant="danger"
        cancel-title="Batal"
        centered
        >
                <div>
                    Apakah anda yakin ingin menghapus data ini?
                    <br><br>
                    <strong>Waktu:</strong> {{ deleteContext?.label || '-' }}<br>
                    <template v-if="deleteContext?.type === 'fetal_heart_rate'">
                        <strong>Denyut Jantung Janin:</strong> {{ deleteContext?.value || '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'amniotic_fluid'">
                        <strong>Air Ketuban:</strong> {{ amnioticFluidMap[Number(deleteContext?.value)] || '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'infiltration'">
                        <strong>Penyusupan:</strong> {{ infiltrationMap[Number(deleteContext?.value)] || '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'cervical_dilation'">
                        <strong>Pembukaan Serviks & Penurunan Kepala:</strong>
                        <br>
                        Pembukaan Serviks: {{ deleteContext?.value !== null && deleteContext?.value !== undefined ? deleteContext.value + ' cm' : '-' }}<br>
                        Penurunan Kepala: {{ selectedCervicalDilationPoint?.headDescent !== null && selectedCervicalDilationPoint?.headDescent !== undefined ? selectedCervicalDilationPoint.headDescent + ' cm' : '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'contraction'">
                        <strong>Kontraksi & Lama Kontraksi:</strong>
                        <br>
                        Kontraksi: {{ deleteContext?.value !== null && deleteContext?.value !== undefined ? deleteContext.value : '-' }}<br>
                        Lama Kontraksi: {{ deleteContext?.value2 !== null && deleteContext?.value2 !== undefined ? deleteContext.value2 + ' detik' : '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'oxytocin'">
                        <strong>Oksitosin & Tetes/menit:</strong>
                        <br>
                        Oksitosin: {{ deleteContext?.value !== null && deleteContext?.value !== undefined ? deleteContext.value : '-' }}<br>
                        Tetes/menit: {{ deleteContext?.value2 !== null && deleteContext?.value2 !== undefined ? deleteContext.value2 : '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'iv_fluids_meds'">
                        <strong>Obat & Cairan IV:</strong>
                        <br>
                        {{ IVFluidsMedsOptions.find(opt => opt.value === deleteContext?.value)?.label || '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'blood_pressure_pulse'">
                      <strong>Tekanan Darah & Nadi:</strong>
                      <br>
                      Tekanan Darah:
                        {{ deleteContext?.systolic !== null && deleteContext?.systolic !== undefined ? deleteContext.systolic : '-' }}
                        /
                        {{ deleteContext?.diastolic !== null && deleteContext?.diastolic !== undefined ? deleteContext.diastolic : '-' }} mmHg<br>
                      Nadi: {{ deleteContext?.pulse !== null && deleteContext?.pulse !== undefined ? deleteContext.pulse + ' bpm' : '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'temperature'">
                        <strong>Suhu:</strong> {{ deleteContext?.value !== null && deleteContext?.value !== undefined ? deleteContext.value + ' °C' : '-' }}
                    </template>
                    <template v-else-if="deleteContext?.type === 'urine'">
                      <strong>Urin:</strong>
                      <br>
                      Protein: {{ urineProteinMap[Number(deleteContext?.protein)] || '-' }}<br>
                      Aseton: {{ urineAcetoneMap[Number(deleteContext?.acetone)] || '-' }}<br>
                      Volume: {{ deleteContext?.volume !== null && deleteContext?.volume !== undefined ? deleteContext.volume + ' cc' : '-' }}
                    </template>
                    <template v-else>
                        <strong>Nilai:</strong> {{ deleteContext?.value || '-' }}
                    </template>
                </div>
        </b-modal>
    </div>
</template>
<script>
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue'
import vSelect from 'vue-select'
import moment from 'moment'
import { BFormDatetimepicker } from 'bootstrap-vue'
import LineChart from '../../doctor/LineChart.vue';
import TableFluidInfiltration from '../../doctor/TableFluidInfiltration.vue';
import LineChartServiks from '../../doctor/LineChartServiks.vue';
import BarChartKontraksi from '../../doctor/BarChartKontraksi.vue';
import LineChartKetuban from '../../doctor/LineChartKetuban.vue';
import LineChartKontraksi from '../../doctor/LineChartKontraksi.vue';
// import LineChartOksitosin from '../../doctor/LineChartOksitosin.vue';
import TableOxytocin from '../../doctor/TableOxytocin.vue'
import LineChartObatCairan from '../../doctor/LineChartObatCairan.vue';
import LineChartTensiNadi from '../../doctor/LineChartTensiNadi.vue';
import TableTemperature from '../../doctor/TableTemperature.vue';
import TableUrine from '../../doctor/TableUrine.vue'
// import LineChartSuhuBadan from '../../doctor/LineChartSuhuBadan.vue';
import LineChartUrin from '../../doctor/LineChartUrin.vue';
import { BIcon } from 'bootstrap-vue'
import { BModal } from 'bootstrap-vue'
// import ConfirmTransferView from '@/views/pages/patient/detail/ConfirmTransferView.vue'
// import blood from '@/store/mr/blood'
import TableIVFluidsMeds from '../../doctor/TableIVFluidsMeds.vue'
// import id from '@/@core/assets/plugins/fullcalendar/locale/id'
// import TableOxytocin from '../../doctor/TableOxytocin.vue'

export default {
    name: 'Partograph',
    components: {
        patientMenu, vSelect,
        BFormDatetimepicker,
        BModal,
        LineChart,
        TableFluidInfiltration,
        LineChartKetuban,
        LineChartServiks,
        BarChartKontraksi,
        LineChartKontraksi,
        // LineChartOksitosin,
        TableOxytocin,
        TableIVFluidsMeds,
        LineChartObatCairan,
        LineChartTensiNadi,
        TableTemperature,
        TableUrine,
        LineChartUrin,
        BIcon,
        DatePicker
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            // showTabs: false,
            showCharts: true,
            activeTab: 'partograph',
            active: {
                disabled: {
                    kalaOne: false,
                    kalaTwo: false,
                    kalaThree: false,
                    kalaFour: false,
                    newborn: false,
                    laborMonitoring: false

                }
            },
            deleteContext: null,
            labels: [],
            doctor_list: [],
            midwife_list: [],
            isEditingFetalHeartRate: false,
            isEditingFluidInfiltration: false,
            isEditingCervicalDilation: false,
            isEditingContraction: false,
            isEditingOxytocin: false,
            isEditingIVFluidsMeds: false,
            isEditingBloodPressurePulse: false,
            isEditingTemperature: false,
            isEditingUrine: false,
            selectedFetalHeartRatePoint: null,
            selectedFluidInfiltrationPoint: null,
            selectedCervicalDilationPoint: null,
            selectedContractionPoint: null,
            selectedOxytocinPoint: null,
            selectedIVFluidsMedsPoint: null,
            selectedBloodPressurePulsePoint: null,
            selectedTemperaturePoint: null,
            selectedUrinePoint: null,
            fetalHeartRate: '',
            amnioticFluid: null,
            amnioticFluidOptions: [
                { value: 1, text: '[U] Utuh' },
                { value: 2, text: '[J] Jernih' },
                { value: 3, text: '[M] Mekonium' },
                { value: 4, text: '[D] Darah' },
                { value: 5, text: '[K] Kering' }
            ],
            amnioticFluidMap: {
                1: '[U] Utuh',
                2: '[J] Jernih',
                3: '[M] Mekonium',
                4: '[D] Darah',
                5: '[K] Kering'
            },
            infiltration: null,
            infiltrationOptions: [
                { value: 0, text: '[0] Tulang kepala janin terpisah' },
                { value: 1, text: '[1] Tulang kepala janin saling bersentuhan' },
                { value: 2, text: '[2] Tulang kepala janin saling tumpang tindih, tapi masih dapat dipisahkan' },
                { value: 3, text: '[3] Tulang kepala janin saling tumpang tindih, tapi TIDAK dapat dipisahkan' }
            ],
            infiltrationMap: {
                0: '[0] Tulang kepala janin terpisah',
                1: '[1] Tulang kepala janin saling bersentuhan',
                2: '[2] Tulang kepala janin saling tumpang tindih, tapi masih dapat dipisahkan',
                3: '[3] Tulang kepala janin saling tumpang tindih, tapi TIDAK dapat dipisahkan'
            },
            cervicalDilation: null,
            cervicalDilationOptions: [
                { value: 0, text: '0 cm' },
                { value: 1, text: '1 cm' },
                { value: 2, text: '2 cm' },
                { value: 3, text: '3 cm' },
                { value: 4, text: '4 cm' },
                { value: 5, text: '5 cm' },
                { value: 6, text: '6 cm' },
                { value: 7, text: '7 cm' },
                { value: 8, text: '8 cm' },
                { value: 9, text: '9 cm' },
                { value: 10, text: '10 cm' }
            ],
            headDescentOptions: [
                { value: 0, text: '0 cm' },
                { value: 1, text: '1 cm' },
                { value: 2, text: '2 cm' },
                { value: 3, text: '3 cm' },
                { value: 4, text: '4 cm' },
                { value: 5, text: '5 cm' },
                { value: 6, text: '6 cm' },
                { value: 7, text: '7 cm' },
                { value: 8, text: '8 cm' },
                { value: 9, text: '9 cm' },
                { value: 10, text: '10 cm' }
            ],
            headDescent: '',
            contraction: '',
            contractionLength: '',
            IVFluidsMeds: null,
            IVFluidsMedsOptions: [
                { value: 1, label: 'Oxytocin (OXY)' },
                { value: 2, label: 'Ringer Lactate (RL)' },
                { value: 3, label: 'NaCl 0.9% (NS)' },
                { value: 4, label: 'Methergine (MTG)' },
                { value: 5, label: 'Ampicillin (AMP)' },
                { value: 6, label: 'Paracetamol (PCM)' },
                { value: 7, label: 'Spasminal (SPM)' },
                { value: 8, label: 'Dexamethasone (DEX)' },
                { value: 9, label: 'Magnesium Sulfate (MgSO₄)' },
                { value: 10, label: 'Metoclopramide (METO)' },
                { value: 11, label: 'Ondansentron 4mg (ONDAN-4mg)' },
                { value: 12, label: 'Ondansentron 8mg (ONDAN-8mg)' },
                { value: 13, label: 'Ketoprofen supp (KETO-8mg)' },
                { value: 14, label: 'Dexketoprofen injection (DEXKETOPROFEN)' },
                { value: 15, label: 'Duvadilan (DUVADILAN)' }
            ],
            oxytocin: '',
            oxytocinDripRate: '',
            bloodPressureSystolic: '',
            bloodPressureDiastolic: '',
            pulse: '',
            temperature: '',
            urineProtein: null,
            urineProteinOptions: [
                { value: 0, text: 'Negatif' },
                { value: 1, text: '(+) Ringan' },
                { value: 2, text: '(++) Sedang' },
                { value: 3, text: '(+++) Berat' }
            ],
            urineProteinMap: {
                0: 'Negatif',
                1: '(+) Ringan',
                2: '(++) Sedang',
                3: '(+++) Berat'
            },
            urineAcetone: null,
            urineAcetoneOptions: [
                { value: 0, text: 'Negatif' },
                { value: 1, text: '(+) Ringan' },
                { value: 2, text: '(++) Sedang' },
                { value: 3, text: '(+++) Berat' }
            ],
            urineAcetoneMap: {
                0: 'Negatif',
                1: '(+) Ringan',
                2: '(++) Sedang',
                3: '(+++) Berat'
            },
            urineVolume: '',
            observationHourFetalHeartRate: '',
            observationHourAmnioticFluid: '',
            observationHourInfiltration: '',
            observationHourCervicalDilation: '',
            observationHourContraction: '',
            observationHourIVFluidsMeds: '',
            observationHourOxytocin: '',
            observationHourBloodPressure: '',
            observationHourPulse: '',
            observationHourTemperature: '',
            observationHourUrine: '',
            tempFetalHeartRates: [],
            tempAmnioticFluids: [],
            tempInfiltration: [],
            tempCervicalDilation: [],
            tempContraction: [],
            tempIVFluidsMeds: [],
            tempOxytocin: [],
            tempBloodPressure: [],
            tempPulse: [],
            tempTemperature: [],
            tempUrine: [],
            partograph: {
                id: "",
                datetime_start: "",
                doctor_id: "",
                midwife_id: "",
                water_breaks_since: "",
                stomachache_since: "",
                gravida: "",
                para: "",
                abortus: "",
                fetal_heart_rate: [],
                amniotic_fluid: [],
                infiltration: [],
                cervical_dilation: [],
                head_descent: [],
                contraction: [],
                duration: [],
                iv_fluids_meds: [],
                oxytocin: [],
                blood_pressure: [],
                pulse: [],
                temperature: [],
                urine: []
            },
            partographId: null,
            chartRefresh: 0,
            isPartograph1Open: false,
            isPartograph2Open: false,
            isPartograph3Open: false,
            isPartograph4Open: false,
            editIndex: null,
            editType: null,
            birthNotesActivePage: 'kala1',
            birthNotePages: [
                { key: 'kala1', label: 'Kala I' },
                { key: 'kala2', label: 'Kala II' },
                { key: 'kala3', label: 'Kala III' },
                { key: 'kala4', label: 'Kala IV' },
                { key: 'bayi_lahir', label: 'Bayi Lahir' },
                { key: 'pemantauan_persalinan', label: 'Pemantauan Persalinan' },
            ],
            birthNotesUpdate: false,
            birthNotes: {
                id: '',
                partograph_id: '',
                date_birth: '',
                birthing_place_type_id: 5,
                birthing_place_other: '',
                birthing_place_address: 'RS Taman Harapan Baru, Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi',
                referral_type_id: null,
                referral_kala: null,
                referral_reason: '',
                referral_place: '',
                referral_companion_type_id: null,
                referral_companion_arry: [],
                problem_arry: []
            },
            birthRecordKala: {
                id: '',
                birth_record_id: '',
                kala_one_latent_phase: "",
                kala_one_intervention_type_id: null,
                kala_one_alert_line_type_id: null,
                kala_one_problem: "",
                kala_one_problem_management: "",
                kala_one_result: "",
                kala_two_episiotomy_type_id: null,
                kala_two_episiotomy: "",
                kala_two_fetal_distress_type_id: null,
                kala_two_fetal_distress: "",
                kala_two_companion_type_id: null,
                kala_two_companion_arry: [],
                kala_two_shoulder_dystocia_type_id: null,
                kala_two_shoulder_dystocia: "",
                // kala_two_active_phase: "",
                kala_two_problem: "",
                kala_two_problem_management: "",
                kala_two_result: "",
                kala_three_initiation_type_id: null,
                kala_three_initiation: "",
                kala_three_long: null,
                kala_three_oxytocin_type_id: null,
                kala_three_oxytocin: null,
                kala_three_oxytocin_reason: "",
                kala_three_oxytocin_second_type_id: null,
                kala_three_oxytocin_second: "",
                kala_three_umbilical_cord_type_id: null,
                kala_three_umbilical_cord: "",
                kala_three_uterine_fundus_type_id: null,
                kala_three_uterine_fundus: "",
                kala_three_placenta_type_id: null,
                kala_three_placenta: "",
                kala_three_placenta_not_type_id: null,
                kala_three_placenta_not: "",
                kala_three_laceration_type_id: null,
                kala_three_laceration: "",
                kala_three_laceration_degree: null,
                kala_three_laceration_suture: null,
                kala_three_laceration_anesthesia: null,
                kala_three_laceration_not_suture_reason: "",
                kala_three_uterine_atony_type_id: null,
                kala_three_uterine_atony: "",
                kala_three_amount_blood: null,
                kala_three_problem: "",
                kala_three_problem_management: "",
                kala_three_result: "",
                kala_four_mother_ku: null,
                kala_four_systolic: null,
                kala_four_diastolic: null,
                kala_four_pulse: null,
                kala_four_breath: null,
                kala_four_problem: "",
                kala_four_problem_management: "",
                kala_four_result: ""
            },
            newbornBaby: {
                partograph_id: "",
                newborn_baby_weight: null,
                newborn_baby_height: null,
                newborn_baby_sex_type_id: null,           // 1 = Laki-laki, 2 = Perempuan
                newborn_baby_assessment_type_id: null,    // 1 = Baik, 2 = Ada penyulit
                newborn_baby_normal_arry: [],
                newborn_baby_abnormal_arry: [],
                newborn_baby_asphyxia_arry: [],
                newborn_baby_asphyxia_others: "",
                newborn_baby_disabled: "",
                newborn_baby_hypothermia: "",
                newborn_baby_breast_milk_type_id: null,
                newborn_baby_breast_milk_hours: "",
                newborn_baby_breast_milk_not: "",
                newborn_baby_problem: "",
                newborn_baby_problem_management: "",
                newborn_baby_result: ""
            },
            bayiLahirTindakanNormalOptions: [
            { value: 1, text: 'Menghangatkan' },
            { value: 2, text: 'Mengeringkan' },
            { value: 3, text: 'IMD atau naluri menyusu segera' },
            { value: 4, text: 'Rangsangan taktil' },
            { value: 5, text: 'Tetes mata profilaksis, vit. K, Imunisasi Hepatitis B' }
            ],
            // bayiLahirTindakanAsfiksia: [],
            bayiLahirTindakanAsfiksiaOptions: [
            { value: 1, text: 'Menghangatkan' },
            { value: 2, text: 'Mengeringkan' },
            { value: 3, text: 'Rangsangan taktil' },
            { value: 4, text: 'Asuhan pasca resusitasi' },
            { value: 5, text: 'Bebaskan jalan napas (posisi dan isap lendir)' },
            { value: 6, text: 'Ventilasi positif (jika perlu)' },
            { value: 7, text: 'Lainnya' }
            ],
            // bayiLahirTindakanAsfiksiaLainnya: '',
            // bayiLahirCacatBawaanSebutkan: '',
            // bayiLahirHipotermiTindakan: '',
            // bayiAsiSetelahJamPertama: null, // 1 = Ya, 0 = Tidak
            // bayiAsiSetelahJamPertamaWaktu: '',
            // bayiAsiSetelahJamPertamaAlasan: '',
            pemantauanMasalah: '',
            pemantauanPenatalaksanaan: '',
            pemantauanHasil: '',
            pemantauanPersalinanRows: [
            { time_id: 1, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
            { time_id: 2, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
            { time_id: 3, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
            { time_id: 4, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
            { time_id: 5, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
            { time_id: 6, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
            ],
            kontraksiOptions: [
            { value: 1, text: 'Keras' },
            { value: 2, text: 'Lemah' }
            ],
            kontraksiMap: {
            1: 'Keras',
            2: 'Lemah'
            },
            editingCell: { row: null, col: null }, // {row: index, col: fieldName}
            savedCells: {},
            companionOptions: [
            { value: 1, text: 'Suami' },
            { value: 2, text: 'Keluarga' },
            { value: 3, text: 'Teman' },
            { value: 4, text: 'Dukun' },
            { value: 5, text: 'Bidan' }
            ],
            companionMap: {
            1: 'Suami',
            2: 'Keluarga',
            3: 'Teman',
            4: 'Dukun',
            5: 'Bidan'
            },
        }
    },

    watch: {
        partograph: {
            deep: true,
            handler() {
            this.rerenderCharts();
            }
        },
        'partograph.id'(newVal) {
            this.partographId = newVal;
        },
        'birthNotes.referral_type_id'(newVal) {
            if (newVal == 2) {
            this.birthNotes.referral_kala = null;
            this.birthNotes.referral_reason = '';
            this.birthNotes.referral_place = '';
            this.birthNotes.referral_companion_type_id = null;
            this.birthNotes.referral_companion_arry = [];
            }
        },
        'birthNotes.referral_companion_type_id'(newVal) {
            if (newVal == 1) {
            this.birthNotes.referral_companion_arry = [];
            }
        },
        'birthRecordKala.kala_two_episiotomy_type_id'(val) {
            if (val !== 2) this.birthRecordKala.kala_two_episiotomy = '';
        },
        'birthRecordKala.kala_two_fetal_distress_type_id'(val) {
            if (val !== 2) this.birthRecordKala.kala_two_fetal_distress = '';
        },
        'birthRecordKala.kala_two_shoulder_dystocia_type_id'(val) {
            if (val !== 2) this.birthRecordKala.kala_two_shoulder_dystocia = '';
        },
        'birthRecordKala.kala_three_initiation_type_id'(val) {
            if (val !== 1) this.birthRecordKala.kala_three_initiation = '';
        },
        'birthRecordKala.kala_three_oxytocin_type_id'(val) {
            if (val !== 2) this.birthRecordKala.kala_three_oxytocin = null;
            if (val !== 1) this.birthRecordKala.kala_three_oxytocin_reason = '';
        },
        'birthRecordKala.kala_three_oxytocin_second_type_id'(val) {
            if (val !== 2) this.birthRecordKala.kala_three_oxytocin_second = '';
        },
        'birthRecordKala.kala_three_umbilical_cord_type_id'(val) {
            if (val !== 1) this.birthRecordKala.kala_three_umbilical_cord = '';
        },
        'birthRecordKala.kala_three_uterine_fundus_type_id'(val) {
            if (val !== 1) this.birthRecordKala.kala_three_uterine_fundus = '';
        },
        'birthRecordKala.kala_three_placenta_type_id'(val) {
            if (val !== 1) this.birthRecordKala.kala_three_placenta = '';
        },
        'birthRecordKala.kala_three_placenta_not_type_id'(val) {
            if (val !== 2) this.birthRecordKala.kala_three_placenta_not = '';
        },
        'birthRecordKala.kala_three_uterine_atony_type_id'(val) {
            if (val !== 2) this.birthRecordKala.kala_three_uterine_atony = '';
        },
        'birthRecordKala.kala_three_laceration_type_id'(val) {
            if (val !== 2) {
                this.birthRecordKala.kala_three_laceration = '';
                this.birthRecordKala.kala_three_laceration_degree = null;
                this.birthRecordKala.kala_three_laceration_suture = null;
                this.birthRecordKala.kala_three_laceration_anesthesia = null;
                this.birthRecordKala.kala_three_laceration_not_suture_reason = '';
            }
        },
        'birthRecordKala.kala_three_laceration_suture'(val) {
            if (val !== 2) this.birthRecordKala.kala_three_laceration_anesthesia = null;
            if (val !== 1) this.birthRecordKala.kala_three_laceration_not_suture_reason = '';
        },
        'newbornBaby.newborn_baby_assessment_type_id'(val) {
            if (val !== 1) {
                this.newbornBaby.newborn_baby_normal_arry = [];
            }
            if (val !== 2) {
                this.newbornBaby.newborn_baby_abnormal_arry = [];
                this.newbornBaby.newborn_baby_asphyxia_arry = [];
                this.newbornBaby.newborn_baby_asphyxia_others = '';
                this.newbornBaby.newborn_baby_disabled = '';
                this.newbornBaby.newborn_baby_hypothermia = '';
            }
        },
        'newbornBaby.newborn_baby_abnormal_arry'(val) {
            if (!val.includes(1)) {
                this.newbornBaby.newborn_baby_asphyxia_arry = [];
                this.newbornBaby.newborn_baby_asphyxia_others = '';
            }
            if (!val.includes(2)) {
                this.newbornBaby.newborn_baby_disabled = '';
            }
            if (!val.includes(3)) {
                this.newbornBaby.newborn_baby_hypothermia = '';
            }
        },
        'newbornBaby.newborn_baby_breast_milk_type_id'(val) {
            if (val === 2) {
                this.newbornBaby.newborn_baby_breast_milk_not = '';
            }
            if (val === 1) {
                this.newbornBaby.newborn_baby_breast_milk_hours = '';
            }
        },


    },

    computed: {
        hasUnsavedChanges() {
            // Check for edited rows in Pemantauan Persalinan
            return this.pemantauanPersalinanRows.some(row => row.isEdited);
            // You can also add checks for other forms if needed
        }
    },

    mounted() {
        window.addEventListener('beforeunload', this.handleBeforeUnload);
    },
    beforeDestroy() {
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
    },

    methods: {
        handleBeforeUnload(e) {
            if (this.hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        },
        handleTabChange(tab) {
            if (this.hasUnsavedChanges) {
                if (confirm('Ada perubahan yang belum tersimpan. Yakin ingin pindah tab?')) {
                    this.activeTab = tab;
                }
            } else {
                this.activeTab = tab;
            }
        },
        handleDeleteConfirmed() {
            if (!this.deleteContext) return;
            this.deletePartographItem(this.deleteContext);
            // If deleting cervical dilation, also delete head descent at the same label
            if (this.deleteContext.type === 'cervical_dilation') {
                this.deletePartographItem({
                    label: this.deleteContext.label,
                    item_type_id: 2 // 2 = head descent
                });
            }
            // If deleting blood pressure & pulse, also delete pulse at the same label
            if (this.deleteContext.type === 'blood_pressure_pulse') {
                this.deletePartographItem({
                    label: this.deleteContext.label,
                    item_type_id: 8 // 8 = pulse
                });
            }
        },
        resetUnsavedFlags() {
            // Reset isEdited for pemantauanPersalinanRows
            if (Array.isArray(this.pemantauanPersalinanRows)) {
                this.pemantauanPersalinanRows.forEach(row => row.isEdited = false);
            }
            // Reset other unsaved flags if you have them for other sections
            // e.g. this.birthRecordKala.isEdited = false;
            // e.g. this.newbornBaby.isEdited = false;
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        formatTime(timeStr) {
            if (!timeStr) return '';
            if (/^\d{2}:\d{2}$/.test(timeStr)) return timeStr;
            if (/^\d{2}:\d{2}:\d{2}$/.test(timeStr)) return timeStr.slice(0, 5);
            return timeStr;
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },

        getData(regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },

        getDoctorList() {
            this.$store.dispatch('employee/getDoctorList', {}).then(response => {
            this.doctor_list = response;
            // console.log('doctor_list: ', this.doctor_list)
            });
        },

        getMidwifeList(){
            this.$store.dispatch('employee/getMidwifeList', {}).then(response => {
                this.midwife_list = response;
                // console.log('midwife_list: ', this.midwife_list)
            });
        },

        getFetalHeartRateChartData() {
          // Fetch and align data as before
          const labels = this.labels; // array of time labels
          const allHeartRates = Array.isArray(this.partograph.fetal_heart_rate)
            ? this.partograph.fetal_heart_rate
            : [];
          const heartRateMap = {};
          for (let obj of allHeartRates) {
            const time = obj.observation_hours?.slice(0,5);
            heartRateMap[time] = obj.fetal_heart_rate;
          }
          const alignedData = labels.map(label => heartRateMap[label] !== undefined ? heartRateMap[label] : null);
          return {
            labels,
            datasets: [
              {
                label: 'Fetal Heart Rate',
                backgroundColor: '#FBA2F7',
                borderColor: "rgb(255, 99, 132)",
                data: alignedData,
                trendlineLinear: {
                  colorMin: "gray",
                  colorMax: "gray",
                  lineStyle: "solid",
                  width: 2,
                  projection: false
                }
              }
            ]
          };
        },

        getCervicalDilationChartData() {
          const labels = this.labels;
          const cervicalDilations = Array.isArray(this.partograph.cervical_dilation)
            ? this.partograph.cervical_dilation
            : [];

            const headDescents = Array.isArray(this.partograph.head_descent)
              ? this.partograph.head_descent
              : [];

          const cervicalDilationMap = {};
          const headDescentMap = {};
          for (let obj of cervicalDilations) {
            const time = obj.observation_hours?.slice(0, 5);
            cervicalDilationMap[time] = Number(obj.cervical_dilation);
          }

          for (let obj of headDescents) {
            const time = obj.observation_hours?.slice(0, 5);
            headDescentMap[time] = Number(obj.head_descent);
          }

          const alignedCervicalDilation = labels.map(label =>
            cervicalDilationMap[label] !== undefined ? cervicalDilationMap[label] : null
          );
          const alignedHeadDescent = labels.map(label =>
            headDescentMap[label] !== undefined ? headDescentMap[label] : null
          );
        //   console.log('alignedCervicalDilation: ', alignedCervicalDilation);
        //   console.log('alignedHeadDescent: ', alignedHeadDescent);
          return {
            labels,
            datasets: [
              {
                label: 'Pembukaan Serviks',
                backgroundColor: '#1b1c1c',
                borderColor: 'rgba(100, 120, 220, 0.7)',
                data: alignedCervicalDilation,
                trendlineLinear: {
                  colorMin: "gray",
                  colorMax: "gray",
                  lineStyle: "solid",
                  width: 2,
                  projection: false
                },
                pointStyle: this.createAsteriskPoint(18, '#1b1c1c'),
                pointRadius: 10,
                pointHoverRadius: 12
              },
              {
                label: 'Penurunan Kepala',
                backgroundColor: '#1b1c1c',
                borderColor: 'rgba(180, 120, 220, 0.7)',
                data: alignedHeadDescent,
                pointStyle: 'circle',
                pointRadius: 7,
                pointHoverRadius: 9
              }
            ]
          };
        },

        getTensiNadiChartData() {
          const labels = this.labels;
          const bloodPressures = Array.isArray(this.partograph.blood_pressure)
            ? this.partograph.blood_pressure
            : [];
          const pulses = Array.isArray(this.partograph.pulse)
            ? this.partograph.pulse
            : [];

          const systolicMap = {};
          const diastolicMap = {};
          for (let obj of bloodPressures) {
            const time = obj.observation_hours?.slice(0, 5);
            systolicMap[time] = Number(obj.systolic);
            diastolicMap[time] = Number(obj.diastolic);
          }

          const pulseMap = {};
          for (let obj of pulses) {
            const time = obj.observation_hours?.slice(0, 5);
            pulseMap[time] = Number(obj.pulse);
          }

          const alignedSystolic = labels.map(label =>
            systolicMap[label] !== undefined ? systolicMap[label] : null
          );
          const alignedDiastolic = labels.map(label =>
            diastolicMap[label] !== undefined ? diastolicMap[label] : null
          );
          const alignedPulse = labels.map(label =>
            pulseMap[label] !== undefined ? pulseMap[label] : null
          );

          return {
            labels,
            datasets: [
            {
                label: 'Tekanan Darah Sistolik',
                backgroundColor: '#000',
                borderColor: 'rgba(0,0,0,0.15)',
                data: alignedSystolic,
                pointStyle: 'triangle',
                pointRadius: 10,
                pointHoverRadius: 12,
                pointBackgroundColor: '#000',
                pointBorderColor: '#000',
                fill: false,
                yAxisID: 'y',
            },
            {
                label: 'Tekanan Darah Diastolik',
                backgroundColor: '#000',
                borderColor: 'rgba(0,0,0,0.15)',
                data: alignedDiastolic,
                pointStyle: this.createTriangleDown(20, '#000'),
                pointRadius: 10,
                pointHoverRadius: 12,
                pointBackgroundColor: '#000',
                pointBorderColor: '#000',
                fill: false,
                yAxisID: 'y',
            },
            {
                label: 'Nadi',
                backgroundColor: '#7a7a7a',
                borderColor: 'rgba(0,0,0,0.15)',
                data: alignedPulse,
                pointStyle: 'circle',
                pointRadius: 7,
                pointHoverRadius: 9,
                pointBackgroundColor: '#7a7a7a',
                pointBorderColor: '#7a7a7a',
                fill: false,
                yAxisID: 'y',
            }
            ]
          };
        },

        createAsteriskPoint(size = 16, color = '#1b1c1c') {
            const canvas = document.createElement('canvas');
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');
            ctx.strokeStyle = color;
            ctx.lineWidth = 2;
            ctx.beginPath();
            // Draw X only (diagonals)
            ctx.moveTo(3, 3);
            ctx.lineTo(size - 3, size - 3);
            ctx.moveTo(size - 3, 3);
            ctx.lineTo(3, size - 3);
            ctx.stroke();
            return canvas;
        },

        createTriangleDown(size = 20, color = '#000') {
            const canvas = document.createElement('canvas');
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = color;
            ctx.beginPath();
            ctx.moveTo(size / 2, size); // bottom center
            ctx.lineTo(size, 0);        // top right
            ctx.lineTo(0, 0);           // top left
            ctx.closePath();
            ctx.fill();
            return canvas;
        },

        async handleSubmit(){
            const payload = {
                registration_destination_id: this.reg_des_id,
                ...this.partograph
            }
            try {
                const response = await this.$store.dispatch('partograph/createPartograph', payload)
                // console.log('response: ', response)
                // console.log('response.data.id: ', response.data.id)
                // console.log('this.partograph.id: ', this.partograph.id)
                this.$set(this.partograph, 'id', response.data.id)
                // console.log('after this.partograph.id: ', this.partograph.id)

                // this.showTabs = true
                this.notification('Sukses', 'CheckIcon', 'success', 'Partograf berhasil dibuat')
                } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal membuat partograf')
                }
        },

        addFetalHeartRate() {
            if (this.observationHourFetalHeartRate && this.fetalHeartRate) {
            this.tempFetalHeartRates.push({
                // item_type_id: 4,
                observation_hours: this.observationHourFetalHeartRate,
                fetal_heart_rate: this.fetalHeartRate
            });
            // console.log('tempFetalHeartRates: ', this.tempFetalHeartRates)
            this.fetalHeartRate = '';
            this.observationHourFetalHeartRate = '';
            }
        },

        onFetalHeartRatePointClicked(point) {
            if (!this.isEditingFetalHeartRate) return;
            // console.log('Fetal Heart Rate Point Clicked: ', point);
            this.selectedFetalHeartRatePoint = { ...point };
        },

        onFluidInfiltrationCellClicked({ type, label, value }) {
            if (!this.isEditingFluidInfiltration) return;
            // Reverse map for amniotic fluid code to number
            const amnioticFluidCodeToNumber = {
                U: 1,
                J: 2,
                M: 3,
                D: 4,
                K: 5
            };
            this.selectedFluidInfiltrationPoint = {
                type,
                label,
                value: type === 'amniotic_fluid' ? amnioticFluidCodeToNumber[value] || null : value
            };
        },

        onCervicalDilationPointClicked(point) {
            if (!this.isEditingCervicalDilation) return;
            this.selectedCervicalDilationPoint = {
            label: point.label,
            cervicalDilation: point.cervicalDilation,
            headDescent: point.headDescent
            };
        },

        onContractionBarClicked(point) {
            if (!this.isEditingContraction) return;
            this.selectedContractionPoint = {
                label: point.label,
                contraction: point.contraction,
                duration: point.duration
            };
        },

        onOxytocinCellClicked({ label, value, dripRate }) {
            if (!this.isEditingOxytocin) return;
            this.selectedOxytocinPoint = {
                label,
                value,
                dripRate
            };
        },

        onIVFluidsMedsCellClicked({ label, value }) {
            if (!this.isEditingIVFluidsMeds) return;
            this.selectedIVFluidsMedsPoint = { label, value };
        },

        onBloodPressurePulsePointClicked({ label, systolic, diastolic, pulse }) {
          if (!this.isEditingBloodPressurePulse) return;
          this.selectedBloodPressurePulsePoint = {
            label,
            systolic,
            diastolic,
            pulse
          };
        },

        onTemperatureCellClicked({ label, value }) {
            if (!this.isEditingTemperature) return;
            this.selectedTemperaturePoint = { label, value };
        },

        onUrineCellClicked({ label, protein, acetone, volume }) {
            if (!this.isEditingUrine) return;
            this.selectedUrinePoint = { label, protein, acetone, volume };
        },

        toggleEditFetalHeartRate() {
            this.isEditingFetalHeartRate = !this.isEditingFetalHeartRate;
            if (!this.isEditingFetalHeartRate) {
                this.selectedFetalHeartRatePoint = null;
            }
        },

        toggleEditFluidInfiltration() {
            this.isEditingFluidInfiltration = !this.isEditingFluidInfiltration;
            if (!this.isEditingFluidInfiltration) this.selectedFluidInfiltrationPoint = null;
        },

        toggleEditCervicalDilation() {
            this.isEditingCervicalDilation = !this.isEditingCervicalDilation;
            if (!this.isEditingCervicalDilation) this.selectedCervicalDilationPoint = null;
        },

        toggleEditContraction() {
            this.isEditingContraction = !this.isEditingContraction;
            if (!this.isEditingContraction) this.selectedContractionPoint = null;
        },

        toggleEditOxytocin() {
            this.isEditingOxytocin = !this.isEditingOxytocin;
            if (!this.isEditingOxytocin) this.selectedOxytocinPoint = null;
        },

        toggleEditIVFluidsMeds() {
            this.isEditingIVFluidsMeds = !this.isEditingIVFluidsMeds;
            if (!this.isEditingIVFluidsMeds) this.selectedIVFluidsMedsPoint = null;
        },

        toggleEditBloodPressurePulse() {
          this.isEditingBloodPressurePulse = !this.isEditingBloodPressurePulse;
          if (!this.isEditingBloodPressurePulse) this.selectedBloodPressurePulsePoint = null;
        },

        toggleEditTemperature() {
            this.isEditingTemperature = !this.isEditingTemperature;
            if (!this.isEditingTemperature) this.selectedTemperaturePoint = null;
        },

        toggleEditUrine() {
            this.isEditingUrine = !this.isEditingUrine;
            if (!this.isEditingUrine) this.selectedUrinePoint = null;
        },

        // confirmDeleteFetalHeartRatePoint() {
        //     this.$refs.deleteFhrModal.show();
        // },

        confirmDeleteFetalHeartRatePoint() {
            this.deleteContext = {
                label: this.selectedFetalHeartRatePoint.label,
                value: this.selectedFetalHeartRatePoint.value,
                item_type_id: 4,
                type: 'fetal_heart_rate'
            };
            this.$refs.deleteModal.show();
        },

        confirmDeleteFluidInfiltrationPoint() {
            const type = this.selectedFluidInfiltrationPoint.type;
            this.deleteContext = {
                label: this.selectedFluidInfiltrationPoint.label,
                value: this.selectedFluidInfiltrationPoint.value,
                item_type_id: type === 'amniotic_fluid' ? 5 : 6,
                type
            };
            this.$refs.deleteModal.show();
        },

        confirmDeleteCervicalDilationPoint() {
            this.deleteContext = {
                label: this.selectedCervicalDilationPoint.label,
                value: this.selectedCervicalDilationPoint.cervicalDilation,
                item_type_id: 1,
                type: 'cervical_dilation'
            };
            console.log('confirmDeleteCervicalDilationPoint: ', this.deleteContext);
            this.$refs.deleteModal.show();
        },

        confirmDeleteContractionPoint() {
            this.deleteContext = {
                label: this.selectedContractionPoint.label,
                value: this.selectedContractionPoint.contraction,
                value2: this.selectedContractionPoint.duration,
                item_type_id: 3,
                type: 'contraction'
            };
            this.$refs.deleteModal.show();
        },

        confirmDeleteOxytocinPoint() {
            this.deleteContext = {
                label: this.selectedOxytocinPoint.label,
                value: this.selectedOxytocinPoint.value,
                value2: this.selectedOxytocinPoint.dripRate,
                item_type_id: 11,
                type: 'oxytocin'
            };
            this.$refs.deleteModal.show();
        },

        confirmDeleteIVFluidsMedsPoint() {
            this.deleteContext = {
                label: this.selectedIVFluidsMedsPoint.label,
                value: this.selectedIVFluidsMedsPoint.value,
                item_type_id: 12,
                type: 'iv_fluids_meds'
            };
            this.$refs.deleteModal.show();
        },

        confirmDeleteBloodPressurePulsePoint() {
          this.deleteContext = {
            label: this.selectedBloodPressurePulsePoint.label,
            systolic: this.selectedBloodPressurePulsePoint.systolic,
            diastolic: this.selectedBloodPressurePulsePoint.diastolic,
            pulse: this.selectedBloodPressurePulsePoint.pulse,
            item_type_id: 7,
            type: 'blood_pressure_pulse'
          };
          this.$refs.deleteModal.show();
        },

        confirmDeleteTemperaturePoint() {
            this.deleteContext = {
                label: this.selectedTemperaturePoint.label,
                value: this.selectedTemperaturePoint.value,
                item_type_id: 9, // 9 = temperature
                type: 'temperature'
            };
            this.$refs.deleteModal.show();
        },

        confirmDeleteUrinePoint() {
          this.deleteContext = {
            label: this.selectedUrinePoint.label,
            protein: this.selectedUrinePoint.protein,
            acetone: this.selectedUrinePoint.acetone,
            volume: this.selectedUrinePoint.volume,
            item_type_id: 10, // urine
            type: 'urine'
          };
          this.$refs.deleteModal.show();
        },

        async deletePartographItem({ label, item_type_id }) {
            if (!label || !this.partograph.id) return;
            const payload = {
                partograph_id: this.partograph.id,
                observation_hours: label + ':00',
                item_type_id
            };
            try {
                await this.$store.dispatch('partograph/deletePartographItem', payload);
                this.notification('Sukses', 'CheckIcon', 'success', 'Data berhasil dihapus');
                this.selectedFetalHeartRatePoint = null;
                this.selectedFluidInfiltrationPoint = null;
                this.selectedCervicalDilationPoint = null;
                this.selectedContractionPoint = null;
                this.selectedOxytocinPoint = null;
                this.selectedIVFluidsMedsPoint = null;
                this.selectedBloodPressurePulsePoint = null;
                this.selectedTemperaturePoint = null;
                this.selectedUrinePoint = null;
                this.isEditingFetalHeartRate = false;
                this.isEditingFluidInfiltration = false;
                this.isEditingCervicalDilation = false;
                this.isEditingContraction = false;
                this.isEditingOxytocin = false;
                this.isEditingIVFluidsMeds = false;
                this.isEditingBloodPressurePulse = false;
                this.isEditingTemperature = false;
                this.isEditingUrine = false;
                await this.checkOrFetchPartograph();
            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menghapus data');
            }
        },

        async savePartographItemEdit({ items }) {
            if (!items || !Array.isArray(items) || !items.length || !this.partograph.id) {
                console.log('savePartographItemEdit called with empty items or no partograph id');
                    console.log('items: ', items);
                    console.log('this.partograph.id: ', this.partograph.id);

                return;
            }

            console.log('savePartographItemEdit called with items: ', items);
            const payload = {
                partograph_id: this.partograph.id,
                items: items.map(item => ({
                    observation_hours: item.label + ':00',
                    item_type_id: item.item_type_id,
                    name_1: item.name_1 || "",
                    name_2: item.name_2 || "",
                    name_3: item.name_3 || "",
                    value_1: item.value_1 !== undefined ? item.value_1 : (item.value || ""),
                    value_2: item.value_2 !== undefined ? item.value_2 : "",
                    value_3: item.value_3 !== undefined ? item.value_3 : ""
                }))
            };
            console.log('savePartographItemEdit payload: ', payload);
            try {
                await this.$store.dispatch('partograph/updatePartographItem', payload);
                this.notification('Sukses', 'CheckIcon', 'success', 'Data berhasil diperbarui');
                await this.checkOrFetchPartograph();
            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal memperbarui data');
            }
        },

        saveFetalHeartRateEdit() {
            this.savePartographItemEdit({
                items: [{
                    label: this.selectedFetalHeartRatePoint.label,
                    value: this.selectedFetalHeartRatePoint.value,
                    item_type_id: 4,
                    name_1: "fetal_heart_rate"
                }]
            });
            this.selectedFetalHeartRatePoint = null;
            this.isEditingFetalHeartRate = false;

        },

        saveAmnioticFluidEdit() {
            this.savePartographItemEdit({
                items: [{
                    label: this.selectedFluidInfiltrationPoint.label,
                    value: this.selectedFluidInfiltrationPoint.value,
                    item_type_id: 5,
                    name_1: "amniotic_fluid"
                }]
            });
            this.selectedFluidInfiltrationPoint = null;
            this.isEditingFluidInfiltration = false;
        },

        saveInfiltrationEdit() {
            this.savePartographItemEdit({
                items: [{
                    label: this.selectedFluidInfiltrationPoint.label,
                    value: this.selectedFluidInfiltrationPoint.value,
                    item_type_id: 6,
                    name_1: "infiltration"
                }]
            });
            this.selectedFluidInfiltrationPoint = null;
            this.isEditingFluidInfiltration = false;
        },

        saveCervicalDilationEdit() {
            this.savePartographItemEdit({
                items: [
                    {
                        label: this.selectedCervicalDilationPoint.label,
                        item_type_id: 1,
                        name_1: "cervical_dilation",
                        value_1: this.selectedCervicalDilationPoint.cervicalDilation
                    },
                    {
                        label: this.selectedCervicalDilationPoint.label,
                        item_type_id: 2,
                        name_1: "head_descent",
                    value_1: this.selectedCervicalDilationPoint.headDescent
                }
            ]});
            // Optionally clear selection and edit state here
            this.selectedCervicalDilationPoint = null;
            this.isEditingCervicalDilation = false;
        },

        saveContractionEdit() {
            console.log('saveContractionEdit called with selectedContractionPoint: ', this.selectedContractionPoint);
            this.savePartographItemEdit({
                items: [{
                    label: this.selectedContractionPoint.label,
                    item_type_id: 3,
                    name_1: "contraction",
                    name_2: "duration",
                    value_1: this.selectedContractionPoint.contraction,
                    value_2: this.selectedContractionPoint.duration
                }]
            });
            this.selectedContractionPoint = null;
            this.isEditingContraction = false;
        },

        saveOxytocinEdit() {
            this.savePartographItemEdit({
                items: [
                    {
                        label: this.selectedOxytocinPoint.label,
                        item_type_id: 11,
                        name_1: "oxytocin",
                        name_2: "drops",
                        value_1: this.selectedOxytocinPoint.value,
                        value_2: this.selectedOxytocinPoint.dripRate
                    }
                ]
            });
            this.selectedOxytocinPoint = null;
            this.isEditingOxytocin = false;
        },

        saveIVFluidsMedsEdit() {
            this.savePartographItemEdit({
                items: [
                {
                    label: this.selectedIVFluidsMedsPoint.label,
                    item_type_id: 12,
                    name_1: "iv_fluids_meds",
                    value_1: this.selectedIVFluidsMedsPoint.value
                }
                ]
            });
            this.selectedIVFluidsMedsPoint = null;
            this.isEditingIVFluidsMeds = false;
        },

        saveBloodPressurePulseEdit() {
          this.savePartographItemEdit({
            items: [
              {
                label: this.selectedBloodPressurePulsePoint.label,
                item_type_id: 7,
                name_1: "systolic",
                name_2: "diastolic",
                value_1: this.selectedBloodPressurePulsePoint.systolic,
                value_2: this.selectedBloodPressurePulsePoint.diastolic
              },
              {
                label: this.selectedBloodPressurePulsePoint.label,
                item_type_id: 8,
                name_1: "pulse",
                value_1: this.selectedBloodPressurePulsePoint.pulse
              }
            ]
          });
          this.selectedBloodPressurePulsePoint = null;
          this.isEditingBloodPressurePulse = false;
        },

        saveTemperatureEdit() {
            this.savePartographItemEdit({
                items: [{
                    label: this.selectedTemperaturePoint.label,
                    item_type_id: 9, // 9 = temperature
                    name_1: "temperature",
                    value_1: this.selectedTemperaturePoint.value
                }]
            });
            this.selectedTemperaturePoint = null;
            this.isEditingTemperature = false;
        },

        saveUrineEdit() {
          this.savePartographItemEdit({
            items: [
              {
                label: this.selectedUrinePoint.label,
                item_type_id: 10, // 10 = urine
                name_1: "protein",
                name_2: "acetone",
                name_3: "volume",
                value_1: this.selectedUrinePoint.protein,
                value_2: this.selectedUrinePoint.acetone,
                value_3: this.selectedUrinePoint.volume
              }
            ]
          });
          this.selectedUrinePoint = null;
          this.isEditingUrine = false;
        },

        addAmnioticFluid() {
            // console.log("here")
            if (this.observationHourAmnioticFluid && this.amnioticFluid) {
            this.tempAmnioticFluids.push({
                // item_type_id: 4,
                observation_hours: this.observationHourAmnioticFluid,
                amniotic_fluid: this.amnioticFluid
            });
            // console.log('tempAmnioticFluids: ', this.tempAmnioticFluids)
            this.amnioticFluid = null;
            this.observationHourAmnioticFluid = '';
            }
        },

        addInfiltration() {
            if (
                this.observationHourInfiltration &&
                this.infiltration !== null &&
                this.infiltration !== undefined
            ) {
                this.tempInfiltration.push({
                    observation_hours: this.observationHourInfiltration,
                    infiltration: this.infiltration
                });
                this.infiltration = null;
                this.observationHourInfiltration = '';
            }
        },

        addCervicalDilation() {
            if (
                this.observationHourCervicalDilation !== '' &&
                this.cervicalDilation !== null &&
                this.headDescent !== null
            ) {
                this.tempCervicalDilation.push({
                    observation_hours: this.observationHourCervicalDilation,
                    cervical_dilation: this.cervicalDilation, // number
                    head_descent: this.headDescent // number
                });
                this.cervicalDilation = null;
                this.headDescent = null;
                this.observationHourCervicalDilation = '';
            }
        },

        addContraction() {
            // console.log("here")
            if (this.observationHourContraction && this.contraction && this.contractionLength) {
            this.tempContraction.push({
                // item_type_id: 4,
                observation_hours: this.observationHourContraction,
                contraction: this.contraction,
                duration: this.contractionLength
            });
            this.contraction = '';
            this.contractionLength = '';
            this.observationHourContraction = '';
            }
        },

        addIVFluidsMeds() {
            // console.log("addIVFluidsMeds called")
            if (this.observationHourIVFluidsMeds && this.IVFluidsMeds) {
                if (!this.tempIVFluidsMeds) this.tempIVFluidsMeds = [];
                this.tempIVFluidsMeds.push({
                    observation_hours: this.observationHourIVFluidsMeds,
                    iv_fluids_meds: this.IVFluidsMeds
                });
                this.IVFluidsMeds = '';
                this.observationHourIVFluidsMeds = '';
            }
        },

        addOxytocin() {
            // console.log("addOxytocin called")
            if (this.observationHourOxytocin && this.oxytocin) {
                if (!this.tempOxytocin) this.tempOxytocin = [];
                this.tempOxytocin.push({
                    observation_hours: this.observationHourOxytocin,
                    oxytocin: this.oxytocin,
                    oxytocin_drip_rate: this.oxytocinDripRate
                });
                this.oxytocin = '';
                this.oxytocinDripRate = '';
                this.observationHourOxytocin = '';
            }
        },

        addBloodPressure() {
            // console.log("addBloodPressure called")
            if (this.observationHourBloodPressure && this.bloodPressureSystolic && this.bloodPressureDiastolic) {
                if (!this.tempBloodPressure) this.tempBloodPressure = [];
                this.tempBloodPressure.push({
                    observation_hours: this.observationHourBloodPressure,
                    blood_pressure_systolic: this.bloodPressureSystolic,
                    blood_pressure_diastolic: this.bloodPressureDiastolic
                });
                this.bloodPressureSystolic = '';
                this.bloodPressureDiastolic = '';
                this.observationHourBloodPressure = '';
            }
        },

        addPulse() {
            // console.log("addPulse called")
            if (this.observationHourPulse && this.pulse) {
                if (!this.tempPulse) this.tempPulse = [];
                this.tempPulse.push({
                    observation_hours: this.observationHourPulse,
                    pulse: this.pulse
                });
                this.pulse = '';
                this.observationHourPulse = '';
            }
        },

        addTemperature() {
            if (this.observationHourTemperature && this.temperature) {
                if (!this.tempTemperature) this.tempTemperature = [];
                this.tempTemperature.push({
                    observation_hours: this.observationHourTemperature,
                    temperature: this.temperature
                });
                this.temperature = '';
                this.observationHourTemperature = '';
            }
        },

        addUrine() {
            if (
                this.observationHourUrine &&
                (this.urineProtein !== null && this.urineProtein !== undefined) &&
                (this.urineAcetone !== null && this.urineAcetone !== undefined) &&
                this.urineVolume
            ) {
                if (!this.tempUrine) this.tempUrine = [];
                this.tempUrine.push({
                    observation_hours: this.observationHourUrine,
                    protein: this.urineProtein,
                    acetone: this.urineAcetone,
                    volume: this.urineVolume
                });
                this.observationHourUrine = '';
                this.urineProtein = null;
                this.urineAcetone = null;
                this.urineVolume = '';
            }
        },

        handleSave() {
            this.partograph.fetal_heart_rate = [...this.tempFetalHeartRates];
            this.partograph.amniotic_fluid = [...this.tempAmnioticFluids];
            this.partograph.infiltration = [...this.tempInfiltration];
            this.partograph.cervical_dilation = [...this.tempCervicalDilation];
            this.partograph.contraction = [...this.tempContraction];
            this.partograph.iv_fluids_meds = [...this.tempIVFluidsMeds];
            this.partograph.oxytocin = [...this.tempOxytocin];
            this.partograph.blood_pressure = [...this.tempBloodPressure];
            this.partograph.pulse = [...this.tempPulse];
            this.partograph.temperature = [...this.tempTemperature];
            this.partograph.urine = [...this.tempUrine];
            // console.log('this.partograph: ', this.partograph);

            // await this.updatePartographDetail();

            // Return the Promise so .then() can be chained
            return this.updatePartographDetail()
        },

        handleSaveAll() {
            // console.log('this.partograph handleSaveAll: ', this.partograph);
            this.$store.dispatch('partograph/updatePartograph', this.partograph)
            .then(() => {
                this.handleSave()
                .then(() => {
                    this.checkOrFetchPartograph()
                    .then(() => {
                        this.notification('Sukses', 'CheckIcon', 'success', 'Data berhasil disimpan');
                    })
                    .catch((e) => {
                        this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data terbaru');
                    });
                })
                .catch((e) => {
                    this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan detail partograf');
                });
            })
            .catch((e) => {
                console.log('Error updating partograph:', e);
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan data dasar test');
            });
        },

        async updatePartographDetail() {
            // Fetal Heart Rate items
            const fetalHeartRateItems = (this.partograph.fetal_heart_rate || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 4,
                name_1: "fetal_heart_rate",
                name_2: "",
                name_3: "",
                value_1: item.fetal_heart_rate,
                value_2: "",
                value_3: ""
            }));

            // Amniotic Fluid items
            const amnioticFluidItems = (this.partograph.amniotic_fluid || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 5,
                name_1: "amniotic_fluid",
                name_2: "",
                name_3: "",
                value_1: item.amniotic_fluid,
                value_2: "",
                value_3: ""
            }));

            // infiltration items
            const infiltrationItems = (this.partograph.infiltration || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 6,
                name_1: "infiltration",
                name_2: "",
                name_3: "",
                value_1: item.infiltration,
                value_2: "",
                value_3: ""
            }));

            // Cervical Dilation items
            const cervicalDilationItems = (this.partograph.cervical_dilation || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 1,
                name_1: "cervical_dilation",
                name_2: "",
                name_3: "",
                value_1: item.cervical_dilation,
                value_2: "",
                value_3: ""
            }));

            // Head Descent items
            const headDescentItems = (this.partograph.cervical_dilation || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 2,
                name_1: "head_descent",
                name_2: "",
                name_3: "",
                value_1: item.head_descent,
                value_2: "",
                value_3: ""
            }));

            // Contraction items
            const contractionItems = (this.partograph.contraction || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 3,
                name_1: "contraction",
                name_2: "duration",
                name_3: "",
                value_1: item.contraction,
                value_2: item.duration,
                value_3: ""
            }));

            // Oxytocin items
            const oxytocinItems = (this.partograph.oxytocin || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 11,
                name_1: "oxytocin",
                name_2: "oxytocin_drip_rate",
                name_3: "",
                value_1: item.oxytocin,
                value_2: item.oxytocin_drip_rate,
                value_3: ""
            }));

            // IV Drugs & Fluids items
            const IVFluidsMedsItems = (this.partograph.iv_fluids_meds || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 12,
                name_1: "iv_fluids_meds",
                name_2: "",
                name_3: "",
                value_1: item.iv_fluids_meds,
                value_2: "",
                value_3: ""
            }));

            // Blood Pressure items
            const bloodPressureItems = (this.partograph.blood_pressure || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 7,
                name_1: "blood_pressure_systolic",
                name_2: "blood_pressure_diastolic",
                name_3: "",
                value_1: item.blood_pressure_systolic,
                value_2: item.blood_pressure_diastolic,
                value_3: ""
            }));

            // Pulse items
            const pulseItems = (this.partograph.pulse || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 8,
                name_1: "pulse",
                name_2: "",
                name_3: "",
                value_1: item.pulse,
                value_2: "",
                value_3: ""
            }));

            // Temperature items
            const temperatureItems = (this.partograph.temperature || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 9,
                name_1: "temperature",
                name_2: "",
                name_3: "",
                value_1: item.temperature,
                value_2: "",
                value_3: ""
            }));

            // Urine items
            const urineItems = (this.partograph.urine || []).map(item => ({
                observation_hours: item.observation_hours,
                item_type_id: 10,
                name_1: "protein",
                name_2: "acetone",
                name_3: "volume",
                value_1: item.protein,
                value_2: item.acetone,
                value_3: item.volume
            }));


            // Combine all items
            const items = [
                ...fetalHeartRateItems,
                ...amnioticFluidItems,
                ...infiltrationItems,
                ...cervicalDilationItems,
                ...headDescentItems,
                ...contractionItems,
                ...oxytocinItems,
                ...IVFluidsMedsItems,
                ...bloodPressureItems,
                ...pulseItems,
                ...temperatureItems,
                ...urineItems
            ];

            const payload = {
                partograph_id: this.partograph.id,
                items
            };


                    // Return the Promise so .then() can be chained
            return this.$store.dispatch('partograph/updatePartographItem', payload)
                .then(() => {
                    // Optionally clear temp arrays here
                    this.tempFetalHeartRates = [];
                    this.tempAmnioticFluids = [];
                    this.tempInfiltration = [];
                    this.tempCervicalDilation = [];
                    this.tempContraction = [];
                    this.tempOxytocin = [];
                    this.tempIVFluidsMeds = [];
                    this.tempBloodPressure = [];
                    this.tempPulse = [];
                    this.tempTemperature = [];
                    this.tempUrine = [];
                })
                .catch((err) => {
                    this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan data detail');
                    throw err; // propagate error to handleSaveAll
                });


        },

        checkOrFetchPartograph() {
            // console.log('checkOrFetchPartograph called');
            this.loading = true;
            let params = {};
            if (this.partograph.id) {
                params.partographId = this.partograph.id;
            } else {
                params.registration_destination_id = this.reg_des_id;
            }
            // console.log("params checkOrFetchPartograph: ", params);
            return this.$store.dispatch('partograph/getPartographDetail', params)
                .then(data => {
                    // console.log('Fetched partograph data:', data);
                    if (data && data.data && data.data[0]) {
                        // console.log('Fetched partograph data inside data.data[0]:', data.data[0]);
                        Object.assign(this.partograph, data.data[0]);
                        // console.log("partograph id", this.partograph.id);
                    } else {
                        this.partograph.id = "";
                    }
                })
                .catch(e => {
                    this.partograph.id = "";
                    this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data partograf');
                })
                .finally(() => {
                    this.loading = false;
                });
        },


        editItem(type, idx) {
            this.editType = type;
            this.editIndex = idx;
            // console.log('Editing item of type:', type, 'at index:', idx);
            const item = this[`temp${type}`][idx];
            // console.log('Editing item:', item);
            if (type === 'FetalHeartRates') {
                this.observationHourFetalHeartRate = item.observation_hours;
                this.fetalHeartRate = item.fetal_heart_rate;
            } else if (type === 'AmnioticFluids') {
                this.observationHourAmnioticFluid = item.observation_hours;
                this.amnioticFluid = item.amniotic_fluid;
            } else if (type === 'Infiltration') {
                this.observationHourInfiltration = item.observation_hours;
                this.infiltration = item.infiltration;
            } else if (type === 'CervicalDilation') {
                this.observationHourCervicalDilation = item.observation_hours;
                this.cervicalDilation = item.cervical_dilation;
                this.headDescent = item.head_descent;
            } else if (type === 'Contraction') {
                this.observationHourContraction = item.observation_hours;
                this.contraction = item.contraction;
                this.contractionLength = item.duration;
            } else if (type === 'IVFluidsMeds') {
                this.observationHourIVFluidsMeds = item.observation_hours;
                this.IVFluidsMeds = item.iv_fluids_meds;
            } else if (type === 'Oxytocin') {
                this.observationHourOxytocin = item.observation_hours;
                this.oxytocin = item.oxytocin;
                this.oxytocinDripRate = item.oxytocin_drip_rate;
            } else if (type === 'BloodPressure') {
                this.observationHourBloodPressure = item.observation_hours;
                this.bloodPressureSystolic = item.blood_pressure_systolic;
                this.bloodPressureDiastolic = item.blood_pressure_diastolic;
            } else if (type === 'Pulse') {
                this.observationHourPulse = item.observation_hours;
                this.pulse = item.pulse;
            } else if (type === 'Temperature') {
                this.observationHourTemperature = item.observation_hours;
                this.temperature = item.temperature;
            } else if (type === 'Urine') {
                this.observationHourUrine = item.observation_hours;
                this.urineProtein = item.protein;
                this.urineAcetone = item.acetone;
                this.urineVolume = item.volume;
            }
            const fieldMap = {
                FetalHeartRates: {
                    observation_hours: 'observationHourFetalHeartRate',
                    fetal_heart_rate: 'fetalHeartRate'
                },
                AmnioticFluids: {
                    observation_hours: 'observationHourAmnioticFluid',
                    amniotic_fluid: 'amnioticFluid'
                },
                Infiltration: {
                    observation_hours: 'observationHourInfiltration',
                    infiltration: 'infiltration'
                },
                CervicalDilation: {
                    observation_hours: 'observationHourCervicalDilation',
                    cervical_dilation: 'cervicalDilation',
                    head_descent: 'headDescent'
                },
                Contraction: {
                    observation_hours: 'observationHourContraction',
                    contraction: 'contraction',
                    duration: 'contractionLength'
                },
                IVFluidsMeds: {
                    observation_hours: 'observationHourIVFluidsMeds',
                    iv_fluids_meds: 'IVFluidsMeds'
                },
                Oxytocin: {
                    observation_hours: 'observationHourOxytocin',
                    oxytocin: 'oxytocin',
                    oxytocin_drip_rate: 'oxytocinDripRate'
                },
                BloodPressure: {
                    observation_hours: 'observationHourBloodPressure',
                    blood_pressure_systolic: 'bloodPressureSystolic',
                    blood_pressure_diastolic: 'bloodPressureDiastolic'
                },
                Pulse: {
                    observation_hours: 'observationHourPulse',
                    pulse: 'pulse'
                },
                Temperature: {
                    observation_hours: 'observationHourTemperature',
                    temperature: 'temperature'
                },
                Urine: {
                    observation_hours: 'observationHourUrine',
                    protein: 'urineProtein',
                    acetone: 'urineAcetone',
                    volume: 'urineVolume'
                }
            };

            Object.keys(item).forEach(key => {
            const dataKey = fieldMap[type]?.[key];
            // console.log('Mapping key:', key, 'to dataKey:', dataKey);
                if (dataKey) {
                    // console.log(
                    //     `Setting this['${dataKey}'] (was: ${this[dataKey]}) = item['${key}'] (value: ${item[key]})`
                    // );
                    this[dataKey] = item[key];
                }
            });
        },

        updateItem(type) {
            if (this.editIndex !== null && this.editType === type) {
                const item = { ...this[`temp${type}`][this.editIndex] };
                // console.log('Updating item:', item);
                const fieldMap = {
                    FetalHeartRates: {
                        observation_hours: 'observationHourFetalHeartRate',
                        fetal_heart_rate: 'fetalHeartRate'
                    },
                    AmnioticFluids: {
                        observation_hours: 'observationHourAmnioticFluid',
                        amniotic_fluid: 'amnioticFluid'
                    },
                    Infiltration: {
                        observation_hours: 'observationHourInfiltration',
                        infiltration: 'infiltration'
                    },
                    CervicalDilation: {
                        observation_hours: 'observationHourCervicalDilation',
                        cervical_dilation: 'cervicalDilation',
                        head_descent: 'headDescent'
                    },
                    Contraction: {
                        observation_hours: 'observationHourContraction',
                        contraction: 'contraction',
                        duration: 'contractionLength'
                    },
                    IVFluidsMeds: {
                        observation_hours: 'observationHourIVFluidsMeds',
                        iv_fluids_meds: 'IVFluidsMeds'
                    },
                    Oxytocin: {
                        observation_hours: 'observationHourOxytocin',
                        oxytocin: 'oxytocin',
                        oxytocin_drip_rate: 'oxytocinDripRate'
                    },
                    BloodPressure: {
                        observation_hours: 'observationHourBloodPressure',
                        blood_pressure_systolic: 'bloodPressureSystolic',
                        blood_pressure_diastolic: 'bloodPressureDiastolic'
                    },
                    Pulse: {
                        observation_hours: 'observationHourPulse',
                        pulse: 'pulse'
                    },
                    Temperature: {
                        observation_hours: 'observationHourTemperature',
                        temperature: 'temperature'
                    },
                    Urine: {
                        observation_hours: 'observationHourUrine',
                        protein: 'urineProtein',
                        acetone: 'urineAcetone',
                        volume: 'urineVolume'
                    }
                };
                // Update item fields from form fields using the mapping
                Object.keys(item).forEach(key => {
                    const dataKey = fieldMap[type]?.[key];
                    if (dataKey && dataKey in this) item[key] = this[dataKey];
                });
                this.$set(this[`temp${type}`], this.editIndex, item);
                this.resetEditState(type);
            }
        },

        deleteItem(type, idx) {
            this[`temp${type}`].splice(idx, 1);
            if (this.editType === type && this.editIndex === idx) {
            this.resetEditState(type);
            }
        },

        resetEditState(type) {
            this.editIndex = null;
            this.editType = null;
            // Optionally clear form fields for this type
            const tempArr = this[`temp${type}`];
            if (tempArr && tempArr[0]) {
            Object.keys(tempArr[0]).forEach(key => {
                if (key in this) this[key] = '';
            });
            }
        },

        handleCancel() {
            this.tempFetalHeartRates = [];
            this.tempAmnioticFluids = [];
            this.tempInfiltration = [];
            this.tempCervicalDilation = [];
            this.tempContraction = [];
            this.tempIVFluidsMeds = [];
            this.tempOxytocin = [];
            this.tempBloodPressure = [];
            this.tempPulse = [];
            this.tempTemperature = [];
            this.tempUrine = [];
            this.editIndex = null;
            this.editType = null;
        },

        rerenderCharts() {
            this.showCharts = false;
            this.$nextTick(() => {
            this.showCharts = true;
            });
        },
        cellKey(rowIdx, col) {
            return `${rowIdx}-${col}`;
        },
        isEditingCell(rowIdx, col) {
            return this.editingCell.row === rowIdx && this.editingCell.col === col;
        },
        isSavedCell(rowIdx, col) {
            return !!this.savedCells[this.cellKey(rowIdx, col)];
        },
        enterEditCell(rowIdx, col) {
            this.editingCell = { row: rowIdx, col };
            this.$nextTick(() => {
            // Focus the input if needed
            const input = document.querySelector(`.cell-input-${rowIdx}-${col}`);
            if (input) input.focus();
            });
        },
        saveCell(rowIdx, col) {
            const value = this.pemantauanPersalinanRows[rowIdx][col];
            const isValid =
                (value !== null && value !== '');

            if (isValid) {
                this.$set(this.savedCells, this.cellKey(rowIdx, col), true);
                this.$set(this.pemantauanPersalinanRows[rowIdx], 'isEdited', true);
            } else {
                // Remove saved state if value is invalid
                this.$delete(this.savedCells, this.cellKey(rowIdx, col));
                this.$set(this.pemantauanPersalinanRows[rowIdx], 'isEdited', false);
            }
            this.editingCell = { row: null, col: null };
        },
        handleCellKeydown(e, rowIdx, col) {
            if (e.key === 'Enter') {
            this.saveCell(rowIdx, col);
            }
        },
        handleCellDblClick(rowIdx, col) {
            this.enterEditCell(rowIdx, col);
        },
         handleBloodPressureKeydown(e, rowIdx) {
            if (e.key === 'Enter') {
            this.saveCell(rowIdx, 'systolic');
            this.saveCell(rowIdx, 'diastolic');
            }
        },
        handleBloodPressureBlur(rowIdx) {
            setTimeout(() => {
            // Check if focus is still inside either input
            const active = document.activeElement;
            const isInside =
                active &&
                (active.classList.contains(`cell-input-${rowIdx}-systolic`) ||
                active.classList.contains(`cell-input-${rowIdx}-diastolic`));
            if (!isInside) {
                const row = this.pemantauanPersalinanRows[rowIdx];
                // Only save if both fields are filled (not null/empty)
                if (
                row.systolic !== null && row.systolic !== '' &&
                row.diastolic !== null && row.diastolic !== ''
                ) {
                this.saveCell(rowIdx, 'systolic');
                this.saveCell(rowIdx, 'diastolic');
                }
            }
            }, 10); // Timeout to allow focus to move between inputs
        },
        async handleSubmitBirthNotes(){
            this.birthNotes.partograph_id = this.partograph.id;
            const payload = {
                ...this.birthNotes
            }
            // console.log('handleSubmitBirthNotes payload: ', payload);
            try {
                const response = await this.$store.dispatch('birthrecord/createBirthRecord', payload)
                this.$set(this.birthNotes, 'id', response.data.id)
                this.notification('Sukses', 'CheckIcon', 'success', 'Catatan Persalinan berhasil dibuat')
                // Fetch the latest data and update UI
                await this.checkOrFetchBirthRecord();
                } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal membuat Catatan Persalinan')
                }
        },

        checkOrFetchBirthRecord() {
            this.loading = true;
            let params = {};
            if (this.birthNotes.id) {
                params.birth_record_id = this.birthNotes.id;
            } else {
                params.partograph_id = this.partograph.id;
            }

            // Helper to convert array of strings to array of integers
            function toIntArray(arr) {
                if (!Array.isArray(arr)) return [];
                return arr.map(v => parseInt(v, 10)).filter(v => !isNaN(v));
            }

            // Helper to convert value to number or null
            function toNumber(val) {
                return val !== undefined && val !== null && val !== '' ? Number(val) : null;
            }

            return this.$store.dispatch('birthrecord/getBirthRecordDetail', params)
                .then(data => {
                    if (data && data.data && data.data[0]) {
                        const record = data.data[0];
                        // Assign birthNotes fields with reactivity for arrays and numbers
                        this.birthNotes.id = record.id;
                        this.birthNotes.partograph_id = record.partograph_id;
                        this.birthNotes.date_birth = record.date_birth;
                        this.birthNotes.birthing_place_type_id = toNumber(record.birthing_place_type_id);
                        this.birthNotes.birthing_place_other = record.birthing_place_other;
                        this.birthNotes.birthing_place_address = record.birthing_place_address;
                        this.birthNotes.referral_type_id = toNumber(record.referral_type_id);
                        this.birthNotes.referral_kala = toNumber(record.referral_kala);
                        this.birthNotes.referral_reason = record.referral_reason;
                        this.birthNotes.referral_place = record.referral_place;
                        this.birthNotes.referral_companion_type_id = toNumber(record.referral_companion_type_id);
                        this.$set(this.birthNotes, 'referral_companion_arry', toIntArray(record.referral_companion_arry));
                        this.$set(this.birthNotes, 'problem_arry', toIntArray(record.problem_arry));

                        // Assign newbornBaby fields with reactivity for arrays and numbers
                        this.newbornBaby.partograph_id = record.partograph_id;
                        this.newbornBaby.newborn_baby_weight = toNumber(record.newborn_baby_weight);
                        this.newbornBaby.newborn_baby_height = toNumber(record.newborn_baby_height);
                        this.newbornBaby.newborn_baby_sex_type_id = toNumber(record.newborn_baby_sex_type_id);
                        this.newbornBaby.newborn_baby_assessment_type_id = toNumber(record.newborn_baby_assessment_type_id);
                        this.$set(this.newbornBaby, 'newborn_baby_normal_arry', toIntArray(record.newborn_baby_normal_arry));
                        this.$set(this.newbornBaby, 'newborn_baby_abnormal_arry', toIntArray(record.newborn_baby_abnormal_arry));
                        this.$set(this.newbornBaby, 'newborn_baby_asphyxia_arry', toIntArray(record.newborn_baby_asphyxia_arry));
                        this.newbornBaby.newborn_baby_asphyxia_others = record.newborn_baby_asphyxia_others || "";
                        this.newbornBaby.newborn_baby_disabled = record.newborn_baby_disabled || "";
                        this.newbornBaby.newborn_baby_hypothermia_type_id = toNumber(record.newborn_baby_hypothermia_type_id);
                        this.newbornBaby.newborn_baby_hypothermia = record.newborn_baby_hypothermia || "";
                        this.newbornBaby.newborn_baby_breast_milk_type_id = toNumber(record.newborn_baby_breast_milk_type_id);
                        this.newbornBaby.newborn_baby_breast_milk_hours = record.newborn_baby_breast_milk_hours || "";
                        this.newbornBaby.newborn_baby_breast_milk_not = record.newborn_baby_breast_milk_not || "";
                        this.newbornBaby.newborn_baby_problem = record.newborn_baby_problem || "";
                        this.newbornBaby.newborn_baby_problem_management = record.newborn_baby_problem_management || "";
                        this.newbornBaby.newborn_baby_result = record.newborn_baby_result || "";

                        this.$nextTick(() => {
                            this.birthNotesUpdate = true;
                        });
                        this.birthRecordKala.birth_record_id = this.birthNotes.id;
                    }
                })
                .catch(e => {
                    this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data birth record');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        // checkOrFetchBirthRecord() {
        //     this.loading = true;
        //     let params = {};
        //     if (this.birthNotes.id) {
        //         params.birth_record_id = this.birthNotes.id;
        //     } else {
        //         // params.registration_destination_id = this.reg_des_id;
        //         params.partograph_id = this.partograph.id;
        //     }
        //     // console.log('checkOrFetchBirthRecord params: ', params);
        //     function toIntArray(arr) {
        //         if (!Array.isArray(arr)) return [];
        //         return arr.map(v => parseInt(v, 10)).filter(v => !isNaN(v));
        //     }

        //     return this.$store.dispatch('birthrecord/getBirthRecordDetail', params)
        //         .then(data => {
        //             if (data && data.data && data.data[0]) {
        //                 const record = data.data[0];
        //                 // Assign birthNotes fields
        //                 Object.assign(this.birthNotes, {
        //                     id: record.id,
        //                     partograph_id: record.partograph_id,
        //                     date_birth: record.date_birth,
        //                     birthing_place_type_id: record.birthing_place_type_id,
        //                     birthing_place_other: record.birthing_place_other,
        //                     birthing_place_address: record.birthing_place_address,
        //                     referral_type_id: record.referral_type_id,
        //                     referral_kala: record.referral_kala,
        //                     referral_reason: record.referral_reason,
        //                     referral_place: record.referral_place,
        //                     referral_companion_type_id: record.referral_companion_type_id,
        //                     referral_companion_arry: record.referral_companion_arry || [],
        //                     problem_arry: record.problem_arry || []
        //                 });
        //                 // Assign newbornBaby fields
        //                 Object.assign(this.newbornBaby, {
        //                     partograph_id: record.partograph_id,
        //                     newborn_baby_weight: record.newborn_baby_weight,
        //                     newborn_baby_height: record.newborn_baby_height,
        //                     newborn_baby_sex_type_id: record.newborn_baby_sex_type_id,
        //                     newborn_baby_assessment_type_id: record.newborn_baby_assessment_type_id,
        //                     newborn_baby_normal_arry: record.newborn_baby_normal_arry || [],
        //                     newborn_baby_abnormal_arry: record.newborn_baby_abnormal_arry || [],
        //                     newborn_baby_asphyxia_arry: record.newborn_baby_asphyxia_arry || [],
        //                     newborn_baby_asphyxia_others: record.newborn_baby_asphyxia_others || "",
        //                     newborn_baby_disabled: record.newborn_baby_disabled || "",
        //                     newborn_baby_hypothermia_type_id: record.newborn_baby_hypothermia_type_id,
        //                     newborn_baby_hypothermia: record.newborn_baby_hypothermia || "",
        //                     newborn_baby_breast_milk_type_id: record.newborn_baby_breast_milk_type_id,
        //                     newborn_baby_breast_milk_hours: record.newborn_baby_breast_milk_hours || "",
        //                     newborn_baby_breast_milk_not: record.newborn_baby_breast_milk_not || "",
        //                     newborn_baby_problem: record.newborn_baby_problem || "",
        //                     newborn_baby_problem_management: record.newborn_baby_problem_management || "",
        //                     newborn_baby_result: record.newborn_baby_result || ""
        //                 });
        //                 this.$nextTick(() => {
        //                     this.birthNotesUpdate = true;
        //                 });
        //                 this.birthRecordKala.birth_record_id = this.birthNotes.id;
        //             }
        //         })
        //         .catch(e => {
        //             this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data birth record');
        //         })
        //         .finally(() => {
        //             this.loading = false;
        //         });
        // },

        handleSaveAllBirthNotes() {
            // // console.log('handleSaveAllBirthNotes called');
            // console.log('this.birthNotes handleSaveAllBirthNotes: ', this.birthNotes);
                const payload = {
                    ...this.birthNotes,
                    partograph_id: this.partograph.id
                };
            this.$store.dispatch('birthrecord/updateBirthRecord', payload)
            .then(() => {
                // this.handleSave()
                // console.log('this.birthNotes after update: ', this.birthNotes);
                this.checkOrFetchBirthRecord()
                    .then(() => {
                        this.notification('Sukses', 'CheckIcon', 'success', 'Data berhasil disimpan');
                    })
                    .catch((e) => {
                        this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data terbaru');
                    });
            })
            .catch((e) => {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan detail partograf');
            });
        },

        checkOrFetchBirthRecordKala() {
            this.loading = true;
            let params = {};
            params.birth_record_id = this.birthNotes.id;

            // console.log('checkOrFetchBirthRecordKala params: ', params);

            return this.$store.dispatch('birthrecord/getBirthRecordKala', params)
                .then(data => {
                    // console.log('Fetched birth record data:', data);
                    if (data && data.data && data.data[0]) {
                        // console.log('Fetched birth record kala data inside data.data[0]:', data.data[0]);
                        Object.assign(this.birthRecordKala, data.data[0]);
                        const defaultRows = [
                            { time_id: 1, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false },
                            { time_id: 2, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false },
                            { time_id: 3, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false },
                            { time_id: 4, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false },
                            { time_id: 5, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false },
                            { time_id: 6, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false },
                        ];
                        // console.log('this.birthRecordKala in checkOrFetchBirthRecordKala: ', this.birthRecordKala);
                        if (Array.isArray(data.data[0].monitoring_kala_four) && data.data[0].monitoring_kala_four.length > 0) {
                            // console.log('monitoring_kala_four is an array');
                            // console.log('monitoring_kala_four data: ', data.data[0].monitoring_kala_four);
                            data.data[0].monitoring_kala_four.forEach((row, idx) => {
                                if (defaultRows[idx]) {
                                    Object.assign(defaultRows[idx], row, { isEdited: false });
                                }
                            });
                            // console.log('defaultRows after assignment: ', defaultRows);
                        }
                        this.pemantauanPersalinanRows = defaultRows;
                        this.savedCells = {};
                        this.pemantauanPersalinanRows.forEach((row, rowIdx) => {
                            [
                                'time', 'systolic', 'diastolic', 'pulse', 'temperature',
                                'fundal_height', 'uterus_contraction', 'bladder_volume', 'bleeding_volume'
                            ].forEach(col => {
                                // Only mark as saved if the value is not null/empty/undefined
                                if (
                                    row[col] !== null &&
                                    row[col] !== '' &&
                                    row[col] !== undefined
                                ) {
                                    this.$set(this.savedCells, `${rowIdx}-${col}`, true);
                                }
                            });
                        });
                        // console.log('this.pemantauanPersalinanRows: ', this.pemantauanPersalinanRows);

                    }
                    else {
                        this.birthRecordKala.id = "";
                        this.birthRecordKala.birth_record_id = this.birthNotes.id;
                        this.birthRecordKala = {
                            id: "",
                            birth_record_id: this.birthNotes.id,
                            kala_one_latent_phase: '',
                            kala_one_intervention_type_id: null,
                            kala_one_alert_line_type_id: null,
                            kala_one_problem: '',
                            kala_one_problem_management: '',
                            kala_one_result: '',
                            kala_two_episiotomy_type_id: null,
                            kala_two_episiotomy: "",
                            kala_two_fetal_distress_type_id: null,
                            kala_two_fetal_distress: "",
                            kala_two_companion_type_id: null,
                            kala_two_companion_arry: [],
                            kala_two_shoulder_dystocia_type_id: null,
                            kala_two_shoulder_dystocia: "",
                            kala_two_problem: "",
                            kala_two_problem_management: "",
                            kala_two_result: "",
                            kala_three_initiation_type_id: null,
                            kala_three_initiation: "",
                            kala_three_long: null,
                            kala_three_oxytocin_type_id: null,
                            kala_three_oxytocin: null,
                            kala_three_oxytocin_reason: "",
                            kala_three_oxytocin_second_type_id: null,
                            kala_three_oxytocin_second: "",
                            kala_three_umbilical_cord_type_id: null,
                            kala_three_umbilical_cord: "",
                            kala_three_uterine_fundus_type_id: null,
                            kala_three_uterine_fundus: "",
                            kala_three_placenta_type_id: null,
                            kala_three_placenta: "",
                            kala_three_placenta_not_type_id: null,
                            kala_three_placenta_not: "",
                            kala_three_laceration_type_id: null,
                            kala_three_laceration: "",
                            kala_three_laceration_degree: null,
                            kala_three_laceration_suture: null,
                            kala_three_laceration_anesthesia: null,
                            kala_three_laceration_not_suture_reason: "",
                            kala_three_uterine_atony_type_id: null,
                            kala_three_uterine_atony: "",
                            kala_three_amount_blood: null,
                            kala_three_problem: "",
                            kala_three_problem_management: "",
                            kala_three_result: "",
                            kala_four_mother_ku: null,
                            kala_four_systolic: null,
                            kala_four_diastolic: null,
                            kala_four_pulse: null,
                            kala_four_breath: null,
                            kala_four_problem: "",
                            kala_four_problem_management: "",
                            kala_four_result: ""
                        };
                        this.pemantauanPersalinanRows = [
                            { time_id: 1, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null },
                            { time_id: 2, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null },
                            { time_id: 3, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null },
                            { time_id: 4, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null },
                            { time_id: 5, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null },
                            { time_id: 6, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null },
                        ];
                    }


                })
                .catch(e => {
                    this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data birth record kala');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        async handleSubmitKalaOne() {
            this.active.disabled.kalaOne = true;
            try {
                // console.log('handleSubmitKalaOne called with birthNotes: ', this.birthNotes);
                const payload = {
                    birth_record_id: this.birthNotes.id,
                    kala_one_latent_phase: this.birthRecordKala.kala_one_latent_phase,
                    kala_one_intervention_type_id: this.birthRecordKala.kala_one_intervention_type_id,
                    kala_one_alert_line_type_id: this.birthRecordKala.kala_one_alert_line_type_id,
                    kala_one_problem: this.birthRecordKala.kala_one_problem,
                    kala_one_problem_management: this.birthRecordKala.kala_one_problem_management,
                    kala_one_result: this.birthRecordKala.kala_one_result
                };
                // console.log('handleSubmitKalaOne called with payload: ', payload);
                await this.$store.dispatch('birthrecord/saveBirthRecordKalaOne', payload)
                .then(
                    async (res) => {
                        this.notification('Sukses', 'CheckIcon', 'success', 'Catatan Kala 1 berhasil disimpan');
                        await this.checkOrFetchBirthRecordKala();
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })

            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan Catatan Kala 1');
            } finally {
                this.active.disabled.kalaOne = false;
            }
        },
        async handleSubmitKalaTwo() {
            this.active.disabled.kalaTwo = true;
            try {
                // console.log('handleSubmitKalaTwo called with birthNotes: ', this.birthNotes);
                const payload = {
                    birth_record_id: this.birthNotes.id,
                    kala_two_episiotomy_type_id: this.birthRecordKala.kala_two_episiotomy_type_id,
                    kala_two_episiotomy: this.birthRecordKala.kala_two_episiotomy,
                    kala_two_fetal_distress_type_id: this.birthRecordKala.kala_two_fetal_distress_type_id,
                    kala_two_fetal_distress: this.birthRecordKala.kala_two_fetal_distress,
                    // kala_two_fetal_distress_a: this.birthRecordKala.kala_two_fetal_distress_a,
                    // kala_two_fetal_distress_b: this.birthRecordKala.kala_two_fetal_distress_b,
                    kala_two_companion_type_id: this.birthRecordKala.kala_two_companion_type_id,
                    kala_two_companion_arry: this.birthRecordKala.kala_two_companion_arry,
                    kala_two_shoulder_dystocia_type_id: this.birthRecordKala.kala_two_shoulder_dystocia_type_id,
                    kala_two_shoulder_dystocia: this.birthRecordKala.kala_two_shoulder_dystocia,
                    // kala_two_active_phase: this.birthRecordKala.kala_two_active_phase,
                    kala_two_problem: this.birthRecordKala.kala_two_problem,
                    kala_two_problem_management: this.birthRecordKala.kala_two_problem_management,
                    kala_two_result: this.birthRecordKala.kala_two_result,
                };
                // console.log('handleSubmitKalaOne called with payload: ', payload);
                await this.$store.dispatch('birthrecord/saveBirthRecordKalaTwo', payload)
                .then(
                    async (res) => {
                        this.notification('Sukses', 'CheckIcon', 'success', 'Catatan Kala II berhasil disimpan');
                        await this.checkOrFetchBirthRecordKala();
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })

            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan Catatan Kala II');
            } finally {
                this.active.disabled.kalaTwo = false;
            }
        },
        async handleSubmitKalaThree() {
            this.active.disabled.kalaThree = true;
            try {
                const payload = {
                    birth_record_id: this.birthNotes.id,
                    kala_three_initiation_type_id: this.birthRecordKala.kala_three_initiation_type_id,
                    kala_three_initiation: this.birthRecordKala.kala_three_initiation,
                    kala_three_long: this.birthRecordKala.kala_three_long,
                    kala_three_oxytocin_type_id: this.birthRecordKala.kala_three_oxytocin_type_id,
                    kala_three_oxytocin: this.birthRecordKala.kala_three_oxytocin,
                    kala_three_oxytocin_reason: this.birthRecordKala.kala_three_oxytocin_reason,
                    kala_three_oxytocin_second_type_id: this.birthRecordKala.kala_three_oxytocin_second_type_id,
                    kala_three_oxytocin_second: this.birthRecordKala.kala_three_oxytocin_second,
                    kala_three_umbilical_cord_type_id: this.birthRecordKala.kala_three_umbilical_cord_type_id,
                    kala_three_umbilical_cord: this.birthRecordKala.kala_three_umbilical_cord,
                    kala_three_uterine_fundus_type_id: this.birthRecordKala.kala_three_uterine_fundus_type_id,
                    kala_three_uterine_fundus: this.birthRecordKala.kala_three_uterine_fundus,
                    kala_three_placenta_type_id: this.birthRecordKala.kala_three_placenta_type_id,
                    kala_three_placenta: this.birthRecordKala.kala_three_placenta,
                    kala_three_placenta_not_type_id: this.birthRecordKala.kala_three_placenta_not_type_id,
                    kala_three_placenta_not: this.birthRecordKala.kala_three_placenta_not,
                    kala_three_laceration_type_id: this.birthRecordKala.kala_three_laceration_type_id,
                    kala_three_laceration: this.birthRecordKala.kala_three_laceration,
                    kala_three_laceration_degree: this.birthRecordKala.kala_three_laceration_degree,
                    kala_three_laceration_suture: this.birthRecordKala.kala_three_laceration_suture,
                    kala_three_laceration_anesthesia: this.birthRecordKala.kala_three_laceration_anesthesia,
                    kala_three_laceration_not_suture_reason: this.birthRecordKala.kala_three_laceration_not_suture_reason,
                    kala_three_uterine_atony_type_id: this.birthRecordKala.kala_three_uterine_atony_type_id,
                    kala_three_uterine_atony: this.birthRecordKala.kala_three_uterine_atony,
                    kala_three_amount_blood: this.birthRecordKala.kala_three_amount_blood,
                    kala_three_problem: this.birthRecordKala.kala_three_problem,
                    kala_three_problem_management: this.birthRecordKala.kala_three_problem_management,
                    kala_three_result: this.birthRecordKala.kala_three_result,
                };
                await this.$store.dispatch('birthrecord/saveBirthRecordKalaThree', payload)
                    .then(async (res) => {
                        this.notification('Sukses', 'CheckIcon', 'success', 'Catatan Kala III berhasil disimpan');
                        await this.checkOrFetchBirthRecordKala();
                    })
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    });
            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan Catatan Kala III');
            } finally {
                this.active.disabled.kalaThree = false;
            }
        },
        async handleSubmitKalaFour() {
            this.active.disabled.kalaFour = true;
            try {
                const payload = {
                    birth_record_id: this.birthNotes.id,
                    kala_four_mother_ku: this.birthRecordKala.kala_four_mother_ku,
                    kala_four_systolic: this.birthRecordKala.kala_four_systolic,
                    kala_four_diastolic: this.birthRecordKala.kala_four_diastolic,
                    kala_four_pulse: this.birthRecordKala.kala_four_pulse,
                    kala_four_breath: this.birthRecordKala.kala_four_breath,
                    kala_four_problem: this.birthRecordKala.kala_four_problem,
                    kala_four_problem_management: this.birthRecordKala.kala_four_problem_management,
                    kala_four_result: this.birthRecordKala.kala_four_result,
                };
                await this.$store.dispatch('birthrecord/saveBirthRecordKalaFour', payload)
                    .then(async (res) => {
                        this.notification('Sukses', 'CheckIcon', 'success', 'Catatan Kala IV berhasil disimpan');
                        await this.checkOrFetchBirthRecordKala();
                    })
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    });
            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan Catatan Kala IV');
            } finally {
                this.active.disabled.kalaFour = false;
            }
        },

        async handleSubmitNewborn() {
            this.active.disabled.newborn = true;
            try {
                const payload = {
                    partograph_id: this.partograph.id,
                    newborn_baby_weight: this.newbornBaby.newborn_baby_weight,
                    newborn_baby_height: this.newbornBaby.newborn_baby_height,
                    newborn_baby_sex_type_id: this.newbornBaby.newborn_baby_sex_type_id,
                    newborn_baby_assessment_type_id: this.newbornBaby.newborn_baby_assessment_type_id,
                    newborn_baby_normal_arry: this.newbornBaby.newborn_baby_normal_arry,
                    newborn_baby_abnormal_arry: this.newbornBaby.newborn_baby_abnormal_arry,
                    newborn_baby_asphyxia_arry: this.newbornBaby.newborn_baby_asphyxia_arry,
                    newborn_baby_asphyxia_others: this.newbornBaby.newborn_baby_asphyxia_others,
                    newborn_baby_disabled: this.newbornBaby.newborn_baby_disabled,
                    newborn_baby_hypothermia_type_id: this.newbornBaby.newborn_baby_hypothermia_type_id,
                    newborn_baby_hypothermia: this.newbornBaby.newborn_baby_hypothermia,
                    newborn_baby_breast_milk_type_id: this.newbornBaby.newborn_baby_breast_milk_type_id,
                    newborn_baby_breast_milk_hours: this.newbornBaby.newborn_baby_breast_milk_hours,
                    newborn_baby_breast_milk_not: this.newbornBaby.newborn_baby_breast_milk_not,
                    newborn_baby_problem: this.newbornBaby.newborn_baby_problem,
                    newborn_baby_problem_management: this.newbornBaby.newborn_baby_problem_management,
                    newborn_baby_result: this.newbornBaby.newborn_baby_result
                };
                await this.$store.dispatch('birthrecord/saveNewbornBaby', payload)
                    .then(async (res) => {
                        this.notification('Sukses', 'CheckIcon', 'success', 'Catatan Bayi Baru Lahir berhasil disimpan');
                        await this.checkOrFetchBirthRecord();
                    })
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    });
            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan Catatan Bayi Baru Lahir');
            } finally {
                this.active.disabled.newborn = false;
            }
        },

        async handleSubmitPemantauanPersalinan() {
            this.active.disabled.laborMonitoring = true;
            try {
                const changedRows = this.pemantauanPersalinanRows.filter(row => row.isEdited);
                // Build payload from pemantauanPersalinanRows
                const payload = {
                    birth_record_id: this.birthNotes.id,
                    monitoring: changedRows.map(row => ({
                        time_id: row.time_id,
                        time: row.time,
                        systolic: row.systolic,
                        diastolic: row.diastolic,
                        pulse: row.pulse,
                        temperature: row.temperature,
                        fundal_height: row.fundal_height,
                        uterus_contraction: row.uterus_contraction,
                        bladder_volume: row.bladder_volume,
                        bleeding_volume: row.bleeding_volume
                    }))
                };
                // console.log('handleSubmitPemantauanPersalinan payload: ', payload);
                await this.$store.dispatch('birthrecord/savePemantauanPersalinan', payload);
                this.notification('Sukses', 'CheckIcon', 'success', 'Pemantauan Persalinan berhasil disimpan');
                // await this.checkOrFetchBirthRecordKala();
                this.pemantauanPersalinanRows.forEach(row => { row.isEdited = false; });
            } catch (e) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal menyimpan Pemantauan Persalinan');
            } finally {
                this.active.disabled.laborMonitoring = false;
            }
        },
    },


    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.checkOrFetchPartograph(this.$route.params.id)
            .then(() => {
                if (this.partograph.id){
                    this.checkOrFetchBirthRecord(this.$route.params.id)
                    .then(() => {
                        if (this.birthNotes.id) {
                            // console.log('we are here in created with birthNotes.id: ', this.birthNotes.id);
                            this.checkOrFetchBirthRecordKala();
                        }
                    });
                }
            })
            this.partograph.datetime_start = moment().format('YYYY-MM-DD HH:mm:ss')
            this.partograph.stomachache_since = this.partograph.datetime_start
            this.getDoctorList()
            this.getMidwifeList()
        }
    }
}
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    .expandable-header:hover {
        background: #e1e4ea;
    }
    .birth-notes-nav-link {
        color: #555 !important;
        font-weight: 500;
        border-radius: 6px;
        margin-bottom: 0;
        margin-right: 4px;
        transition: background 0.2s, color 0.2s;
        padding: 10px 18px;
        white-space: nowrap;
    }
    .birth-notes-nav-link.active-birth-notes {
        background: #e1e4ea !important;
        color: #222 !important;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .cell-edit-btn {
        position: absolute;
        top: 2px;
        right: 4px;
        opacity: 0.6;
        z-index: 2;
        padding: 0;
    }
    td.position-relative {
        position: relative;
    }
    .unsaved-alert {
        position: absolute;
        top: 8px;
        right: 16px;
        z-index: 10;
        padding: 6px 18px;
        font-size: 15px;
        background: #fffbe6;
        border: 1px solid #ffe58f;
        color: #ad8b00;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        font-weight: 500;
        }
</style>
