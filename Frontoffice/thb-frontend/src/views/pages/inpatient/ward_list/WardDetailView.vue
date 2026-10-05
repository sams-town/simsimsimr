<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Denah Bangsal', link: '/inpatient/ward' },
                { text: `${getHFIName(active_hfii)}` },
            ]"
        >
        <section class="">
            <section class="fs-14 mb-20">
                <div class="font-weight-bold">KETERANGAN</div>
                <table class="fs-12">
                    <tr>
                        <td class="align-top" style="width: 80px;">Bed</td>
                        <td class="align-top" style="width: 20px;">:</td>
                        <td>
                            <div class="d-flex">
                                <div v-for="item in legendBed" class="d-flex align-items-center mr-2">
                                    <div class="legend-box mr-1" :class="item.color"></div>
                                    <span style="line-height: 100%;">{{ item.name }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top" rowspan="2">Ruangan</td>
                        <td class="align-top">:</td>
                        <td>
                            <div class="d-flex">
                                <div v-for="item in legendRoomGender" class="d-flex align-items-center mr-2">
                                    <div class="legend-box mr-1" :class="item.color"></div>
                                    <span style="line-height: 100%;">{{ item.name }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top"></td>
                        <td>
                            <div class="d-flex">
                                <div v-for="item in legendRoomStatus" class="d-flex align-items-center mr-2">
                                    <feather-icon size="13" :color="item.color" :icon="item.icon" class="mr-1"/> 
                                    <span style="line-height: 100%;">{{ item.name }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div @click="active_toggle = !active_toggle" class="d-inline-block pointer">
                                <div class="d-flex align-items-center">
                                    <feather-icon :icon="active_toggle ? 'EyeOffIcon' : 'EyeIcon'" class="mr-2" />
                                    <span style="line-height: 100%;">{{ active_toggle ? 'Sembunyikan' : 'Tampilkan'}} pasien</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </section>
            
            <div class="d-flex mt-16" :style="active_toggle ? 'gap: 20px' : ''">
                <div class="cs-sidebar" :class="{ 'cs-collapsed': !active_toggle }">
                    <b-table bordered fixed small striped :fields="wardFields" :items="room_items" class="text-center fs-12">
                        <template #cell(patient)="row">
                            <div class="text-left">{{ row.item.patient_alias }}. {{ row.item.patient_name }}</div>
                            <div class="text-left">{{ row.item.patient_insurance_id != 1 ? row.item.patient_company_name : row.item.patient_insurance_name }} Kelas {{ row.item.patient_service_class_name }}</div>
                        </template>
                        <template #cell(room)="row">
                            <div>{{ row.item.room_code }} {{ row.item.room_number ? `- ${row.item.room_number}` : '' }}</div>
                            <div>Kelas {{ row.item.room_service_class_name }}</div>
                        </template>
                    </b-table>
                </div>

                <div class="main-content" :class="{ 'expanded': !active_toggle }">
                    <template v-if="active_hfii == hfii.gdak">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in gdak.left_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
        
                            <div style="width: 200px;"></div>
        
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in gdak.right_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.gdkn">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in gdkn.left_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
        
                            <div style="width: 200px;"></div>
        
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in gdkn.right_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.rujw">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in rujw_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.odc">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in odc_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.orcd">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in orcd.left_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
        
                            <div style="width: 200px;"></div>
        
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in orcd.right_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.orcb">
                        <section class="d-flex flex-nowrap justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in orcb.left_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
        
                            <div style="width: 200px;"></div>
        
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in orcb.right_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.jsmn">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: calc(200*2px);">
                                <div class="w-100 mb-1 d-flex" style="height: calc(137*2px); border: 2px solid black;">
                                    <div class="m-auto">Nurse Station</div>
                                </div>
        
                                <div class="d-flex flex-column" :class="{ 'special-margin': shouldSpecMargin, 'aditional-margin': shouldAddMargin}">
                                    <div class="d-flex align-items-end">
                                        <div style="width: 200px" v-for="(item_r, index_r) in jsmn.second_side" :class="{'ml-1' : index_r != 0 }">
                                            <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" class="w-100"/>
                                        </div>                        
                                    </div>
        
                                    <div style="height: 30px;"></div>
                                    
                                    <div class="d-flex align-items-end">
                                        <div style="width: 200px" v-for="(item_r, index_r) in jsmn.third_side" :class="{'ml-1' : index_r != 0 }">
                                            <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" class="w-100"/>
                                        </div>                        
                                    </div>
                                </div>
        
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-end" :style="{ marginBottom: (jsmn.fourth_side?.length > 0 && !jsmn.fifth_side?.length) ? '137px' : '0' }">
                                        <div style="width: calc((200px * 2) / 3)" v-for="(item_r, index_r) in jsmn.fourth_side" :class="{'ml-1' : index_r != 0 }">
                                            <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" class="w-100"/>
                                        </div>                        
                                    </div>
        
                                    <div style="height: 30px;"></div>
                                    
                                    <div class="d-flex align-items-end">
                                        <div style="width: calc((200px * 2) / 3)" v-for="(item_r, index_r) in jsmn.fifth_side" :class="{'ml-1' : index_r != 0 }">
                                            <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" class="w-100"/>
                                        </div>                        
                                    </div>
                                </div>                        
                            </div>
        
                            <div style="width: 200px;"></div>
        
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in jsmn.first_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.bgvl">
                        <div class="d-flex justify-content-center">
                            <section class="">
                                <div class="d-flex align-items-end">
                                    <div class="d-flex flex-column" style="width: 200px">
                                        <template v-for="(item_r, index_r) in bgvl.first_side">
                                            <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                        </template>                        
                                    </div>
                                    
                                    <div style="width: 200px;"></div>
            
                                    <div class="d-flex flex-wrap" style="width: calc(200*2.04px)">
                                        <template v-for="(item_r, index_r) in bgvl.second_side">
                                            <RoomBluePrint :datasets="item_r" :posWidth="50" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                        </template>                        
                                    </div>
                                </div>
            
                                <div style="height: 137px;"></div>
        
                                <div class="d-flex">
                                    <div class="d-flex flex-column" style="width: 200px">
                                        <template v-for="(item_r, index_r) in bgvl.third_side">
                                            <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                        </template>                        
                                    </div>
        
                                    <div class="d-flex mr-1" style="width: calc(200*0.5px); margin-left: calc(200*0.5px); border: 2px solid black;">
                                        <div class="m-auto text-center">Nurse Station</div>
                                    </div>  
        
                                    <div class="d-flex flex-wrap" style="width: calc(200*3.08px)">
                                        <template v-for="(item_r, index_r) in bgvl.fourth_side">
                                            <RoomBluePrint :datasets="item_r" :posWidth="33" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                        </template>                        
                                    </div>
                                </div>
                                
                                <div style="height: calc(137*0.5px);"></div>
            
                                <div class="d-flex" style="width: calc(200*5.08px)">
                                    <template v-for="(item_r, index_r) in bgvl.fifth_side">
                                        <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" :class="{'ml-1' : index_r != 0 }"/>
                                    </template>                        
                                </div>
                            </section>
                        </div>
                    </template>
        
                    <template v-if="active_hfii == hfii.skra">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in skra.left_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>
                                
                                <div style="height: calc(137*0.5px);"></div>
        
                                <div class="w-100 mb-1 d-flex" style="height: calc(137*1.5px); border: 2px solid black;">
                                    <div class="m-auto">Nurse Station</div>
                                </div>           
                            </div>
        
                            <div style="width: 200px;"></div>
        
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in skra.right_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.tulp">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex flex-column" style="width: 200px;">
                                <template v-for="(item_r, index_r) in tulp.left_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
        
                            <div style="width: 200px;"></div>
        
                            <div class="d-flex flex-column" style="width: 200px;">
                                <div class="w-100 mb-1 d-flex" style="height: calc(137*1px); border: 2px solid black;">
                                    <div class="m-auto">Nurse Station</div>
                                </div> 
        
                                <div style="height: calc(137*0.5px);"></div>
        
                                <template v-for="(item_r, index_r) in tulp.right_side">
                                    <RoomBluePrint :datasets="item_r" :reverse="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>
                            </div>
                        </section>
                    </template>
        
                    <!-- <template v-if="active_hfii == hfii.icu || active_hfii == hfii.icui || active_hfii == hfii.iccu"> -->
                    <template v-if="active_hfii == hfii.icu || active_hfii == hfii.iccu">
                        <div class="d-flex justify-content-center">
                            <div>
                                <div class="d-flex" style="width: calc(200*5px);">
                                    <template v-for="(item_r, index_r) in icus.top_side">
                                        <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" :class="{'ml-1' : index_r != 0 }"/>
                                    </template>                        
                                </div>
                                <div class="d-flex mt-20">
                                    <!-- <div class="d-flex flex-column" style="width: 200px;"></div> -->
                                    <div class="d-flex flex-nowrap align-items-start justify-content-between flex-grow-1">
                                        <div class="d-flex flex-column" style="width: 200px; margin-top: 137px;">
                                            <template v-for="(item_r, index_r) in icus.left_side">
                                                <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                            </template>                        
                                        </div>
                                        
                                        <div class="mb-1 d-flex" style="height: calc(137*1.5px); width: calc(200*1.5px); border: 2px solid black; margin-top: 137px;">
                                            <div class="m-auto">Nurse Station</div>
                                        </div>           
                    
                                        <div class="d-flex flex-column" style="width: 200px;">
                                            <template v-for="(item_r, index_r) in icus.right_side">
                                                <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                            </template>                        
                                        </div>
                                    </div>
                                </div>
                
                                <!-- <div class="d-flex justify-content-end" style="margin: calc(-137px - 4px) calc(200px + 4px) 0 0;"> -->
                                <div class="d-flex justify-content-end" style="margin: calc(-274px - 4px) calc(200px + 4px) 0 0;">
                                    <div style="width: 200px" v-for="(item_r, index_r) in icus.bottom_side">
                                        <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" class="w-100"/>
                                    </div> 
                                </div>  
                                
                                
                            </div>
                        </div>
        
                        <!-- <div class="d-flex justify-content-center w-100 mt-40">
                            <div class="d-flex" style="width: calc(200*4px);">
                                <template v-for="(item_r, index_r) in ok_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" :class="{'ml-1' : index_r != 0 }"/>
                                </template>                        
                            </div>
                        </div> -->
                    </template>
        
                    <template v-if="active_hfii == hfii.picu">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex" style="width: calc(200*3px)">
                                <template v-for="(item_r, index_r) in picu_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" :class="{'ml-1' : index_r != 0 }"/>
                                </template>                        
                            </div>
                        </section>
                    </template>

                    <template v-if="active_hfii == hfii.ok">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex" style="width: calc(200*4px)">
                                <template v-for="(item_r, index_r) in ok_side">
                                    <RoomBluePrint :datasets="item_r" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform" :class="{'ml-1' : index_r != 0 }"/>
                                </template>                      
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.nicu">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex" style="width: calc(200*2.2px)">
                                <template v-for="(item_r, index_r) in nicu_side">
                                    <RoomBluePrint :datasets="item_r" :fullWidth="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
        
                    <template v-if="active_hfii == hfii.prna">
                        <section class="d-flex flex-nowrap align-items-end justify-content-center">
                            <div class="d-flex" style="width: calc(200*1.2px)">
                                <template v-for="(item_r, index_r) in prna_side">
                                    <RoomBluePrint :datasets="item_r" :fullWidth="true" @childSwap="openSwap" @childSetting="openSetting" @childConfirmTranfer="triggerConfirm" @childInform="openInform"/>
                                </template>                        
                            </div>
                        </section>
                    </template>
                </div>
            </div>
        </section>
        </Layout>

        <!-- <b-modal ref="swapBedModal" :size="temp_unit.type_status_bed_id == 1 || userData.employee_id != admin_id ? 'sm' : 'md'" :hide-footer="temp_unit.type_status_bed_id != 2 || userData.employee_id != admin_id" no-close-on-backdrop> -->
        <b-modal ref="swapBedModal" :size="temp_unit.type_status_bed_id == 1 ? 'sm' : 'md'" :hide-footer="temp_unit.type_status_bed_id != 2" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase w-100">
                    RUANGAN {{ temp_unit.room_name }},
                    BED {{ temp_unit.bed_name }}
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="fs-13">
                <template v-if="temp_unit.type_status_bed_id == 1">
                    <div class="d-flex justify-content-center align-items-center p-3 text-center" :class="getBedStatusVariant(temp_unit.type_status_bed_id)">Status bed: {{ getBedStatusName(temp_unit.type_status_bed_id) }}</div>
                </template>
                <template v-if="temp_unit.type_status_bed_id == 2">
                    <div class="p-2 bg-success-100">
                        <table>
                            <tr>
                                <td class="align-top" style="width: 100px;">Nama Pasien</td>
                                <td class="align-top" style="width: 20px;">:</td>
                                <td>
                                    <span v-for="(item, index) in temp_unit.patients">
                                        {{ item.alias_name }}. {{ item.patient_name }}
                                        <span v-if="index+1 != temp_unit.patients.length">,</span>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-top">Status bed</td>
                                <td class="align-top">:</td>
                                <td>{{ getBedStatusName(temp_unit.type_status_bed_id) }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- <section v-if="userData.employee_id == admin_id" class="mt-20"> -->
                    <section class="mt-20">
                        <div>
                            <i>Pindahkan bed pasien ini dari <span class="font-weight-bold">{{ temp_unit.room_name }} bed {{ temp_unit.bed_name }}</span> ke?</i>
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <label class="fs-13 mb-0">Bangsal</label>
                                <v-select v-model="form_swap.des_hfii" :options="filteredOptHFII" :disabled="isHFIIDisabled" :reduce="item => item.value" label="title" placeholder="pilih bangsal" class="select-size-sm"></v-select>
                                <!-- <v-select v-model="form_swap.des_type_room_id" :options="TypeRoomOptions" :reduce="item => item.value" label="title" placeholder="pilih bangsal" class="select-size-sm"></v-select> -->
                            </div>
                            <div class="col-lg-4 mt-8 mt-lg-0">
                                <label class="fs-13 mb-0">Ruangan</label>
                                <v-select v-model="form_swap.des_room_id" :options="OptionsRoom" :reduce="item => item.value"
                                label="title" placeholder="pilih ruangan" class="select-size-sm" />
                            </div>
    
                            <div class="col-lg-4 mt-8 mt-lg-0">
                                <label class="fs-13 mb-0">Bed</label>
                                <v-select v-model="form_swap.des_bed_id" :options="OptionsBed" :reduce="item => item.value"
                                label="title" placeholder="pilih bed" class="select-size-sm" />
                            </div>
                        </div>
                    </section>
                </template>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationSwapBed" :disabled="disabled_swap" size="sm" variant="primary" class="px-32">PINDAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="roomSettingModal" size="md" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase w-100">
                    ANDA SEDANG MELAKUKAN PERBARUAN SETTING RUANGAN {{ temp_setting.room_name }},
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="fs-13 row">
                <div class="col-lg-4">
                    <label class="fs-13 mb-0">Bedah</label>
                    <v-select v-model="form_setting.is_surgery" :options="optSurgery" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                </div>
                <div class="col-lg-4 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Isolasi</label>
                    <v-select v-model="form_setting.is_isolation" :options="optIsolation" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                </div>
                <div class="col-lg-4 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Tekanan</label>
                    <v-select v-model="form_setting.room_pressure" :options="optRoomPressure" :reduce="item => item.value" label="title" placeholder="pilih tekanan" class="select-size-sm"/>
                </div>
                <div class="col-lg-4 mt-8">
                    <label class="fs-13 mb-0">Gender</label>
                    <v-select v-model="form_setting.gender_availability" :options="optGenderAvailability" :reduce="item => item.value" label="title" placeholder="pilih gender" class="select-size-sm"/>
                </div>
                <div class="col-lg-4 mt-8">
                    <label class="fs-13 mb-0">Kelas Layanan</label>
                    <v-select v-model="form_setting.service_class_id" :options="OptionsServiceClass" :reduce="item => item.value" label="title" placeholder="pilih kelas" class="select-size-sm"/>
                </div>
                <div class="col-lg-4 mt-8">
                    <label class="fs-13 mb-0">Tarif</label>
                    <v-select v-model="form_setting.costing_id" :options="CostingOptions" :reduce="item => item.value" label="title" placeholder="pilih tarif" class="select-size-sm"/>
                </div>
                <!-- <div class="col-lg-4 mt-8">
                    <label class="fs-13 mb-0">Penyakit dalam</label>
                    <v-select v-model="form_setting.is_internal" :options="optInternal" :reduce="item => item.value" label="title" placeholder="pilih penyakit dalam" class="select-size-sm"/>
                </div> -->
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationRoomSetting" :disabled="disabled_setting" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="informBedModal" size="sm" hide-footer>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase w-100">
                    RUANGAN {{ temp_inform.room_name }},
                    BED {{ temp_inform.bed_name }}
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="fs-13">
                <template v-if="temp_inform.type_status_bed_id == 1">
                    <div class="d-flex justify-content-center align-items-center p-3 text-center" :class="getBedStatusVariant(temp_inform.type_status_bed_id)">Status bed: {{ getBedStatusName(temp_inform.type_status_bed_id) }}</div>
                </template>
                <template v-else>
                    <div v-for="(item, index) in temp_inform.patients" class="p-2 bg-success-100" :class="{'mb-2': index+1 != temp_inform.patients.length}">
                        <table>
                            <tr>
                                <td class="align-top" style="width: 100px;">Nama Pasien</td>
                                <td class="align-top" style="width: 20px;">:</td>
                                <td>{{ item.alias_name }}. {{ item.name }}</td>
                            </tr>
                            <tr>
                                <td class="align-top">Tanggal Lahir</td>
                                <td class="align-top">:</td>
                                <td>{{ localDate(item.birth_date) }} ({{ parsedAge(item.birth_date) }})</td>
                            </tr>
                            <tr>
                                <td class="align-top">Jenis Kelamin</td>
                                <td class="align-top">:</td>
                                <td>{{ item.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="align-top">Nomor RM</td>
                                <td class="align-top">:</td>
                                <td>{{ item.norm }}</td>
                            </tr>
                            <tr>
                                <td class="align-top">DPJP</td>
                                <td class="align-top">:</td>
                                <td>{{ item.doctor_in_charge_name }}</td>
                            </tr>
                            <tr>
                                <td class="align-top">Status bed</td>
                                <td class="align-top">:</td>
                                <td>{{ getBedStatusName(temp_inform.type_status_bed_id) }}</td>
                            </tr>
                        </table>
                    </div>
                </template>
            </div>
        </b-modal>

        <ConfirmTransferView ref="confirmTransferRef" placement="ward" @refreshWard="getSelectionWards" />
    </div>
</template>

<script>
import { mapState } from 'vuex'
import optionMixin from '@/mixins/optionMixin';
import dateMixin from '@/mixins/dateMixin';

import RoomBluePrint from '@/@core/components/room/RoomBluePrint.vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import ConfirmTransferView from '@/views/pages/patient/detail/ConfirmTransferView.vue';

export default {
    components: { RoomBluePrint, ConfirmTransferView },
    mixins: [optionMixin, dateMixin],

    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',

            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfii: {
                gdak: 'c1002e5b-5f1f-4837-af45-122673052259',
                gdkn: '3905d3e8-26a3-4166-965a-a91b5edad463',
                rujw: 'cb8fea0f-ce57-4596-b901-4671c8a2ef29',
                odc: '1843843c-af62-4452-b19f-beddd1acd0ec',
                orcd: '5d92766f-f1e8-42cd-9067-df8cc0fca864',
                orcb: '4a8a1d01-692c-40a8-8e41-969bbbc090b0',
                jsmn: '2a2b727f-631e-4d46-86b1-410fb8e40445',
                skra: 'e6f9a05c-3be9-4847-bca8-6983af3fb230',
                icu: '6c7d7e53-ff24-4246-85b2-07ea77ac801f',
                // icui: 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d',
                iccu: '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c',
                picu: 'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039',
                nicu: '5515af72-6dcb-415d-89e4-2433e7b0149d',
                prna: '05079cd9-07c6-4cf9-beb0-271eb301b7c1',
                tulp: '58f3ae39-1444-4e43-bdf8-0e624f8d7928',
                bgvl: 'edda56f0-920d-4c6a-b8bd-b084d3c368a7',
                ok: 'cd72c7f0-920b-4260-b602-541d0a15305a'
            },
            position:{
                orchid_id:'080edb93-e827-4e09-8e35-913b9bc843bf',
                orchid_bedah_id:'cadfae3b-7539-4a17-8bcd-8155fbda840d',
            },
            isHFIIDisabled:false,
            active_hfii: '',
            OptionsBed: [],
            OptionsRoom: [],

            temp_unit: {
                room_name: '',
                bed_name: '',
                patients: [],
                type_status_bed_id: ''
            },

            form_swap: {
                existing_bed_id: '',
                des_hfii: '',
                des_room_id: '',
                des_bed_id: '',
                // des_type_room_id: '',
            },
            disabled_swap: false,

            temp_setting: {
                room_name: '',
            },
            form_setting: {
                room_id: '',
                is_surgery: '',
                // is_internal: '',
                is_isolation: '',
                gender_availability: '',
                room_pressure: '',
                service_class_id:'',
                costing_id: '',
            },
            disabled_setting: false,

            temp_inform: {
                room_name: '',
                bed_id: '',
                bed_name: '',
                type_status_bed_id: '',
                patients: '',
            },

            gdak: {
                left_side: [],
                right_side: [],
            },
            gdkn: {
                left_side: [],
                right_side: [],
            },
            rujw_side: [],
            odc_side: [],
            orcd: {
                left_side: [],
                right_side: [],
            },
            orcb: {
                left_side: [],
                right_side: [],
            },
            jsmn: {
                first_side: [],
                second_side: [],
                third_side: [],
                fourth_side: [],
                fifth_side: [],
            },
            bgvl: {
                first_side: [],
                second_side: [],
                third_side: [],
                fourth_side: [],
                fifth_side: [],
            },
            skra: {
                left_side: [],
                right_side: [],
            },
            icus: {
                top_side: [],
                right_side: [],
                bottom_side: [],
                left_side: [],
            },
            picu_side: [],
            nicu_side: [],
            prna_side: [],
            ok_side: [],
            tulp: {
                left_side: [],
                right_side: [],
            },

            wardFields: [
                { key: 'patient', label: 'PASIEN', thStyle: { textAlign: 'left' } },
                { key: 'room', label: 'KAMAR' },
                { key: 'bed_name', label: 'BED', thStyle: { width: '70px' } },
            ],
            room_items: [],
            active_toggle: true,
        }
    },

    computed: {
        ...mapState({ 
            optHFII: state => state.healthfacilityitem.options.healthfacilityitem,
            optSurgery: state => state.frontoptions.master.room.surgery,
            optIsolation: state => state.frontoptions.master.room.isolation,
            optRoomPressure: state => state.frontoptions.master.room.room_pressure,
            optGenderAvailability: state => state.frontoptions.master.room.gender_availability,
            optInternal: state => state.frontoptions.master.room.internal_disease,
            // TypeRoomOptions: state => state.helper.options.type_room,
            // OptionsRoom: state => state.room.options.room,
            OptionsServiceClass: state => state.helper.options.service_class,
            CostingOptions: state => state.costing.options.serviceData,

            legendRoomGender: state => state.frontoptions.master.blue_print.legends.room_gender,         
            legendRoomStatus: state => state.frontoptions.master.blue_print.legends.room_status,         
            legendBed: state => state.frontoptions.master.blue_print.legends.bed,      
        }),

        shouldSpecMargin() {
            return this.jsmn.fourth_side.length === 0 && this.jsmn.fifth_side.length === 0
        },

        shouldAddMargin() {
            return this.jsmn.fourth_side.length === 0 && this.jsmn.fifth_side.length >= 0
        },

        filteredOptHFII() {
            if((this.userData.position_id == this.position.orchid_id || 
                this.userData.position_id == this.position.orchid_bedah_id) &&
                (this.form_swap.des_hfii == this.hfii.orcd ||
                this.form_swap.des_hfii == this.hfii.orcb)
                )
            {
                this.isHFIIDisabled = false
                return this.optHFII.filter(item => {
                    return item.value==this.hfii.orcd || item.value==this.hfii.orcb
                })
            } else {
                this.isHFIIDisabled = true
                return this.optHFII
            }
        }
    },

    watch: {
        // 'form_swap.des_type_room_id'(value) {
        //     this.form_swap.des_bed_id = ''
        //     this.form_swap.des_room_id = ''

        //     if (value) {
        //         return this.$store.dispatch('room/getParamRoom', value)
        //     } else {
        //         this.$store.dispatch('room/getParamRoom', '')
        //         this.OptionsBed = []
        //     }
        // },

        'form_swap.des_hfii'(value) {
            this.form_swap.des_bed_id = ''
            this.form_swap.des_room_id = ''

            if (value) {
                return this.getRoomOptions(value)
            } else {
                this.OptionsBed = []
            }
        },

        'form_swap.des_room_id'(value) {
            this.form_swap.des_bed_id = ''

            if (value) {
                this.$store.dispatch('room/getOptAllBed', { room_id: value }).then(
                    async response => {
                        this.OptionsBed = response                        
                    }
                )
            } else {
                this.OptionsBed = []
            }
        },
    },

    methods: {
        async getRoomOptions(value) {
            let params = {
                health_facility_item_id: value 
            }
            try {                
                await this.$store.dispatch('room/getOptRoom', params)
                .then(
                    response => {
                        // console.log(response);
                        this.OptionsRoom = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('response', response)                        
                })
            } catch (error) {
                console.error('Error catch while GET room/getOptRoom at: ', error);
            }
        },

        getHFIName(value) {
            const result = this.optHFII.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // awal selalu number
        // extractNumber(str) {
        //     if (!str) return null;
        //     const numberMatch = str.match(/- (\d+)/);
        //     return numberMatch ? parseInt(numberMatch[1], 10) : null;
        // },

        // selalu string
        // extractNumber(str) {
        //     if (!str) return null;
        //     const match = str.match(/-\s*(.+)/);
        //     return match ? match[1].trim() : null;
        // },

        // handling number/string tp bingung bacanya pake ini
        // extractNumber(str) {
        //     if (!str) return null;
        //     const match = str.match(/-\s*(.+)/);
        //     let room = match ? match[1].trim() : null
        //     if (!room) return null
        //     let int_room = Number(room) || null
        //     if (!int_room) return String(room)
        //     return int_room
        // },

        // handling number/string
        extractNumber(str) {
            if (!str) return null;
            const room = str.match(/-\s*(.+)/)?.[1]?.trim();
            if (!room) return null;
            
            const num = Number(room);
            return isNaN(num) ? room : num;
        },

        async getReturnWardDetail(hfii) {
            try {
                const response = await this.$store.dispatch('room/getFloorPlan', {health_facility_item_id: hfii});
                const temp_bp = response || []
                return temp_bp.map(item => ({
                    ...item,
                    room_number: this.extractNumber(item.name)
                }));
            } catch (error) {
                console.error('Error while GET DATA at: ', error);
                return [];
            }
        },

        async getIcusWard() {
            const roomCategories = {
                top_side: [1, 2, 3, '3A', 5],
                right_side: [6, 7, 8, 9],
                bottom_side: [10],
                left_side: [12, 11],
                // left_side: [2, 1, 12, 11],
            }

            const [var_icu, var_iccu] = await Promise.all([
                this.getReturnWardDetail(this.hfii.icu),
                this.getReturnWardDetail(this.hfii.iccu)
            ]);

            // let combined =  [...var_icu, ...var_iccu]
            // console.log('each:', { var_icu, var_iccu })
            // console.log('combined', combined);

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.icus[key] = var_icu 
                    ? var_icu
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })

            var_iccu.map(item => {
                this.icus.left_side.push(item)
            })

            const new_left_side = [2, 1, 12, 11]

            this.icus.left_side.sort((a, b) => {
                const indexA = new_left_side.indexOf(a.room_number)
                const indexB = new_left_side.indexOf(b.room_number)
                return indexA - indexB
            }) // ngurutin berdasarkan roomCategories.second_side
            
            const combArry = [...this.icus.top_side, ...this.icus.right_side, ...this.icus.bottom_side, ...this.icus.left_side]            
            this.manageData(combArry)
        },

        // async getIcusWard() {
        //     const [var_icu, var_icui, var_iccu] = await Promise.all([
        //         this.getReturnWardDetail(this.hfii.icu),
        //         this.getReturnWardDetail(this.hfii.icui),
        //         this.getReturnWardDetail(this.hfii.iccu)
        //     ]);

        //     let combined =  [...var_icu, ...var_icui]
        //     let result = combined.filter((item) => item.code.toLowerCase() != 'ok')
        //     let var_ok = combined.filter((item) => item.code.toLowerCase() == 'ok')
            
        //     // console.log('Results:', { var_icu, var_icui, var_iccu })
        //     // console.log('combined', combined);
        //     // console.log('result', result);

        //     this.icus.top_side = result.filter(item => Number(item.room_number) <= 7).sort((a, b) => a.room_number - b.room_number)
        //     this.icus.right_side = result.filter(item => Number(item.room_number) >= 8 && Number(item.room_number) <= 10).sort((a, b) => a.room_number - b.room_number)
        //     this.icus.bottom_side = result.filter(item => Number(item.room_number) == 11)
        //     this.icus.left_side = [...var_iccu].sort((a, b) => b.room_number - a.room_number)
        //     this.ok_side = [...var_ok].sort((a, b) => a.room_number - b.room_number)

        //     const combArry = [...this.icus.top_side, ...this.icus.right_side, ...this.icus.bottom_side, ...this.icus.left_side, ...this.ok_side]            
        //     this.active_toggle = false
        //     this.manageData(combArry)
        // },

        getWardDetail(hfii) {
            try {
                this.$store.dispatch('room/getFloorPlan', { health_facility_item_id: hfii }).then(
                    async response => {
                        const temp_bp = response || []
                        const bluePrints = temp_bp.map(item => ({ ...item, room_number: this.extractNumber(item.name) }));
                        
                        this.active_toggle = true
                        this.manageData([...bluePrints])
                        
                        if (this.active_hfii == this.hfii.gdak) return this.splitGDAK(bluePrints)
                        if (this.active_hfii == this.hfii.gdkn) return this.splitGDKN(bluePrints)
                        if (this.active_hfii == this.hfii.rujw) return this.rujw_side = bluePrints.sort((a, b) => b.room_number - a.room_number)
                        if (this.active_hfii == this.hfii.odc) return this.odc_side = bluePrints.sort((a, b) => a.room_number - b.room_number)
                        if (this.active_hfii == this.hfii.orcd) return this.splitORCD(bluePrints)
                        if (this.active_hfii == this.hfii.orcb) return this.splitORCB(bluePrints)
                        if (this.active_hfii == this.hfii.jsmn) return this.splitJSMN(bluePrints)
                        if (this.active_hfii == this.hfii.bgvl) return this.splitBGVL(bluePrints)
                        if (this.active_hfii == this.hfii.skra) return this.splitSKRA(bluePrints)
                        if (this.active_hfii == this.hfii.picu) return this.picu_side = bluePrints.sort((a, b) => a.room_number - b.room_number)
                        if (this.active_hfii == this.hfii.nicu) return this.nicu_side = bluePrints.sort((a, b) => a.room_number - b.room_number)
                        if (this.active_hfii == this.hfii.prna) return this.prna_side = bluePrints.sort((a, b) => a.room_number - b.room_number)
                        if (this.active_hfii == this.hfii.tulp) return this.splitTULP(bluePrints)
                        if (this.active_hfii == this.hfii.ok) return this.ok_side = bluePrints.sort((a, b) => a.room_number - b.room_number)
                    }
                )
            } catch (error) {
                console.error('Error while GET DATA at: ', error);                
            }
        },

        // SWAP BED
        openSwap(obj) {
            // console.log(obj);
            this.temp_unit.room_name = obj.room_name
            this.temp_unit.bed_name = obj.bed_name
            this.temp_unit.patients = obj.patients
            this.temp_unit.type_status_bed_id = obj.type_status_bed_id

            this.form_swap.existing_bed_id = obj.bed_id
            this.form_swap.des_hfii = this.active_hfii
            this.form_swap.des_room_id = ''
            this.form_swap.des_bed_id = ''

            this.$refs.swapBedModal.show()
        },

        async validationSwapBed() {
            let send_swap = {
                id: this.form_swap.existing_bed_id,
                swap_bed_id: this.form_swap.des_bed_id
            }

            try {
                this.disabled_swap = true
                await this.$store.dispatch('room/putSwapBed', send_swap)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.$refs.swapBedModal.hide() 
                        this.getWardDetail(this.active_hfii)                    
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.disabled_swap = false
            }   
        },

        triggerConfirm(param) {
            const message = param
            this.$refs.confirmTransferRef.openConfirm(message);
        },

        async openInform(param) {
            this.temp_inform.room_name = param?.room_name
            this.temp_inform.bed_id = param?.bed_id
            this.temp_inform.bed_name = param?.bed_name
            this.temp_inform.type_status_bed_id = param?.type_status_bed_id
            this.temp_inform.patients = await this.getFullInformations(param?.patients)

            this.$refs.informBedModal.show()
        },

        async getFullInformations(arr_px) {
            let temp = []
            
            try {
                for (const item of arr_px) {
                    const response = await this.$store.dispatch('registration/getById', item.registration_destination_id)
                    
                    if (response?.length > 0) {
                        temp.push(response[0])
                    }
                }                
                return temp
            } catch (error) {
                console.error('Error in getFullInformations:', error)
                throw error
            }
        },
        openSetting(obj) {
            this.temp_setting.room_name = obj?.name

            this.form_setting.room_id = obj?.id
            this.form_setting.is_surgery = obj?.is_surgery
            // this.form_setting.is_internal = obj?.is_internal
            this.form_setting.is_isolation = obj?.is_isolation
            this.form_setting.gender_availability = obj?.gender_availability
            this.form_setting.room_pressure = obj?.room_pressure
            this.form_setting.service_class_id = obj?.service_class_id
            this.form_setting.costing_id = obj?.costing_id

            this.$refs.roomSettingModal.show()
        },

        async validationRoomSetting() {
            try {
                this.disabled_setting = true
                await this.$store.dispatch('room/putRoomTemp', this.form_setting)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.$refs.roomSettingModal.hide() 
                        this.getSelectionWards()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.disabled_setting = false
            }   
        },

        splitGDAK(val) {
            const roomCategories = {
                left_side: [16, '13A', 12, 10, 8],
                right_side: [17, 15, 13, 11, 9, 7],
            }

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.gdak[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })
        },

        splitGDKN(val) {
            const roomCategories = {
                left_side: [6, '3A', 2],
                right_side: [5, 3, 1],
            };

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.gdkn[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })
        },
        
        splitORCD(val) {
            const roomCategories = {
                left_side: [12, 10, 8, 6, '3A', 2],
                right_side: [11, 9, 7, 5, 3, 1],
            };

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.orcd[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })
        },

        splitORCB(val) {
            const roomCategories = {
                left_side: [18, 16,'13A'],
                right_side: [17, 15, 13],
            };

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.orcb[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })
        },

        splitJSMN(val) {
            const roomCategories = {
                first_side: [1, 2, 3, '3A', 5, 6, 7, 8, 9],
                second_side: [18, 19],
                third_side: [17, 16],
                fourth_side: [13, '13A', 15],
                fifth_side: [12, 11, 10],
            }

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.jsmn[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })

            // breakdown alinya yang punya 2 Object.entries 
            // cari yang sesuai kategori
            // Object.entries(roomCategories).forEach(([key, ids]) => {
            //     const idSet = new Set(ids);
            //     this.jsmn[key] = val ? val.filter(item => idSet.has(item.room_number)) : []
            // })
            // sort berdasarkan target
            // Object.entries(roomCategories).forEach(([key, value]) => {
            //     this.jsmn[key].sort((a, b) => 
            //         roomCategories[key].indexOf(a.room_number) - roomCategories[key].indexOf(b.room_number)
            //     )
            // })

            // sort biasa tapi yang di sort bisa number atau string
            // this.jsmn.fifth_side.sort((a, b) => {
            //     const numA = Number(a.room_number);
            //     const numB = Number(b.room_number);
                
            //     if (!isNaN(numA) && !isNaN(numB)) return numB - numA
            //     return String(b.room_number).localeCompare(String(a.room_number));  // String sort
            // })
            // Hasil: [5, 10, "25", 100, "123", "abc"]
        },

        splitBGVL(val) {
            const roomCategories = {
                first_side: [8, 9, 10, 11],
                second_side: [6, 7, 5, '3A'],
                third_side: [12, 13],
                fourth_side: [1, 2, 3, 17, 19, 21],
                fifth_side: ['13A', 15, 16, 18, 20],
            }

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.bgvl[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })
        },

        // splitBGVL(val) {
        //     const roomCategories = {
        //         first_side: [1, 2, 3, 4],
        //         second_side: [5, 6, 8, 7],
        //         third_side: [12, 20],
        //         fourth_side: [9, 10, 11, 14, 16, 18],
        //         fifth_side: [13, 15, 17, 19, 21],
        //     }

        //     Object.entries(roomCategories).forEach(([key, ids]) => {
        //         const idSet = new Set(ids);
        //         this.bgvl[key] = val ? val.filter(item => idSet.has(Number(item.room_number))) : []
        //     });

        //     this.bgvl.first_side.sort((a, b) => a.room_number - b.room_number)
        //     this.bgvl.second_side.sort((a, b) => {
        //         const indexA = roomCategories.second_side.indexOf(a.room_number)
        //         const indexB = roomCategories.second_side.indexOf(b.room_number)
        //         return indexA - indexB
        //     }) // ngurutin berdasarkan roomCategories.second_side

        //     // Aturan return value:
        //     // Negative number (< 0): a harus datang sebelum b
        //     // Zero (=== 0): a dan b sama, urutan tidak berubah
        //     // Positive number (> 0): a harus datang setelah b

        //     // urutan asli: [5, 6, 7, 8] 
        //     // second_side: [5, 6, 8, 7]
        //     // Bandingkan 7 vs 8:
        //     // indexA = roomCategories.second_side.indexOf(7) = 3
        //     // indexB = roomCategories.second_side.indexOf(8) = 2
        //     // return 3 - 2 = 1 → 8 datang sebelum 7

        //     this.bgvl.third_side.sort((a, b) => a.room_number - b.room_number)
        //     this.bgvl.fourth_side.sort((a, b) => a.room_number - b.room_number)
        //     this.bgvl.fifth_side.sort((a, b) => a.room_number - b.room_number)
        // },

        splitSKRA(val) {
            const roomCategories = {
                left_side: [8, 9, 10, 11],
                right_side: [7, 6, 5, '3A', 3, 2, 1],
            }

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.skra[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })
        },

        splitTULP(val) {
            const roomCategories = {
                left_side: [1, 2, 3, '3A', 5, 6, 7],
                right_side: [11, 10, 9, 8],
            }

            Object.entries(roomCategories).forEach(([key, ids]) => {
                const orderMap = new Map(ids.map((id, index) => [id, index]))                
                this.tulp[key] = val 
                    ? val
                        .filter(item => orderMap.has(item.room_number))
                        .sort((a, b) => orderMap.get(a.room_number) - orderMap.get(b.room_number))
                    : []
            })
        },

        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },

        getSelectionWards() {
            // if (this.active_hfii == this.hfii.icu || this.active_hfii == this.hfii.icui || this.active_hfii == this.hfii.iccu) {
            if (this.active_hfii == this.hfii.icu || this.active_hfii == this.hfii.iccu) {
                this.getIcusWard()
            } else {
                this.getWardDetail(this.$route.params.health_facility_item_id)
            }
        },

        manageData(inputArray) {
            this.room_items = inputArray.flatMap(room => 
                room.beds.flatMap(bed => 
                    bed.patients?.map(patient => ({
                        room_code: room.code,
                        room_number: room.room_number,
                        room_service_class_name: room.service_class_name,
                        bed_name: bed.name,
                        patient_alias: patient.alias_name,
                        patient_name: this.getAbbreviatedName(patient.patient_name),
                        patient_service_class_name: patient.service_class_name,
                        patient_insurance_name: patient.insurance_name,
                        patient_company_name: patient.company_name,
                        patient_insurance_id: patient.insurance_id
                    })) || []
                )
            )            
            // console.log('inputArray:', inputArray)
            // console.log('room_items:', this.room_items)
        },
        
        getAbbreviatedName(input_name) {
            if (!input_name) return ""

            const words = input_name.toString().trim().split(" ").filter(Boolean)
            
            if (words.length === 0) return ""
            if (words.length === 1) return words[0]
            
            const firstName = words.slice(0, 2).join(" ")
            const lastNameAbbr = words.slice(2).map(word => `${word[0].toUpperCase()}.`).join(" ")

            return lastNameAbbr ? `${firstName} ${lastNameAbbr}` : firstName
        },
    },

    created() {
        this.$store.dispatch('healthfacilityitem/getParam', this.hfi_inpatient)
        this.$store.dispatch('costing/getParamByTypeCostingId', 7)

        if (this.$route.params.health_facility_item_id) {
            this.active_hfii = this.$route.params.health_facility_item_id
            this.form_swap.des_hfii = this.$route.params.health_facility_item_id
            this.getSelectionWards()
        }
    }
}
</script>

<style scoped>
.legend-box {
    width: 12px; height: 12px; aspect-ratio: 1 / 1; border: 0.5px solid black
}
.special-margin {
    margin-bottom: calc(137px * 2);
}
.aditional-margin {
    margin-bottom: 137px;
}
</style>

<style scoped>
.bg-blinking {
    animation: blink 1.5s infinite ease-in-out;
}
@keyframes blink {
    0% {
        background-color: yellowgreen;
    }
    50% {
        background-color: yellow;
    }
    100% {
        background-color: yellowgreen;
    }
}

.cs-sidebar {
    flex: 0 0 400px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.cs-sidebar.cs-collapsed {
    flex: 0 0 0px;
    padding: 0;
    border-right: 0;
}

.main-content {
    flex: 1;
    transition: all 0.3s ease;
}


.pointer:hover {
    color: blue;
}
</style>