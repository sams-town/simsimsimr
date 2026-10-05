import { mapState } from 'vuex'

export default {
    computed: {
        ...mapState({ 
            // Anamnesa
            OptionAnamnesa: state => state.frontoptions.nurse.assessment.anamnesa.anamnesa_type,
            OptionFamily: state => state.frontoptions.nurse.assessment.anamnesa.family_relation,

            // Tanda Vital
            OptionsOxygenPoint: state => state.soap.vitals.OptionsOxygenPoint,
            OptionsAwarePoint: state => state.soap.vitals.OptionsAwarePoint,
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
            skin_color: state => state.frontoptions.general.vital_sign.skin_color,
            behavior: state => state.frontoptions.general.vital_sign.behavior,

            // Status Generalis
            OptionsHeadCircumference: state => state.soap.generalis.OptionsHeadCircumference,
            OptionsHeadHairColor: state => state.soap.generalis.OptionsHeadHairColor,
            OptionsHeadHairCondition: state => state.soap.generalis.OptionsHeadHairCondition,
            OptionsEyeConjunctiva: state => state.soap.generalis.OptionsEyeConjunctiva,
            OptionsEyeSclera: state => state.soap.generalis.OptionsEyeSclera,
            OptionsEyePupil: state => state.soap.generalis.OptionsEyePupil,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
            OptionsEyeBlindColor: state => state.soap.generalis.OptionsEyeBlindColor,
            OptionsMouthMukosa: state => state.soap.generalis.OptionsMouthMukosa,
            OptionsMouthHiperemis: state => state.soap.generalis.OptionsMouthHiperemis,
            OptionsMouthTongue: state => state.soap.generalis.OptionsMouthTongue,
            OptionsMouthKaries: state => state.soap.generalis.OptionsMouthKaries,
            OptionsNeckKGB: state => state.soap.generalis.OptionsNeckKGB,
            OptionsNeckTrakea: state => state.soap.generalis.OptionsNeckTrakea,
            OptionsNeckMassa: state => state.soap.generalis.OptionsNeckMassa,
            OptionsNeckJVP: state => state.soap.generalis.OptionsNeckJVP,
            OptionsInspectionChest: state => state.soap.generalis.OptionsInspectionChest,
            OptionsInspectionRetraction: state => state.soap.generalis.OptionsInspectionRetraction,
            OptionsPalpationChest: state => state.soap.generalis.OptionsPalpationChest,
            OptionsPalpationFremitus: state => state.soap.generalis.OptionsPalpationFremitus,
            OptionsPalpationKrepitasi: state => state.soap.generalis.OptionsPalpationKrepitasi,
            OptionsAuskultationPulmoVasikuler: state => state.soap.generalis.OptionsAuskultationPulmoVasikuler,
            OptionsAuskultationPulmoRhonki: state => state.soap.generalis.OptionsAuskultationPulmoRhonki,
            OptionsAuskultationPulmoWheezing: state => state.soap.generalis.OptionsAuskultationPulmoWheezing,
            OptionsAuskultationCORBJ: state => state.soap.generalis.OptionsAuskultationCORBJ,
            OptionsAuskultationCORMurmur: state => state.soap.generalis.OptionsAuskultationCORMurmur,
            OptionsAuskultationCORGallop: state => state.soap.generalis.OptionsAuskultationCORGallop,
            OptionsAbdomenSupel: state => state.soap.generalis.OptionsAbdomenSupel,
            OptionsAbdomenBising: state => state.soap.generalis.OptionsAbdomenBising,
            OptionsAbdomenPress: state => state.soap.generalis.OptionsAbdomenPress,
            OptionsAbdomenHepar: state => state.soap.generalis.OptionsAbdomenHepar,
            OptionsEkstremitasAkral: state => state.soap.generalis.OptionsEkstremitasAkral,
            OptionsEkstremitasCRT: state => state.soap.generalis.OptionsEkstremitasCRT,
            OptionsEkstremitasEdema: state => state.soap.generalis.OptionsEkstremitasEdema,
            OptionsOftalVisus: state => state.soap.generalis.OptionsOftalVisus,
            OptionsOftalTIO: state => state.soap.generalis.OptionsOftalTIO,
            OptionsOftalEyePosition: state => state.soap.generalis.OptionsOftalEyePosition,
            OptionsOftalMovement: state => state.soap.generalis.OptionsOftalMovement,
            OptionsOftalSuperSilia: state => state.soap.generalis.OptionsOftalSuperSilia,
            OptionsOftalPalpebraEdema: state => state.soap.generalis.OptionsOftalPalpebraEdema,
            OptionsOftalPalpebraPtosis: state => state.soap.generalis.OptionsOftalPalpebraPtosis,
            OptionsOftalPalpebraLagophtalmus: state => state.soap.generalis.OptionsOftalPalpebraLagophtalmus,
            OptionsOftalPalpebraTumor: state => state.soap.generalis.OptionsOftalPalpebraTumor,
            OptionsOftalApparetus: state => state.soap.generalis.OptionsOftalApparetus,
            OptionsOftalSilia: state => state.soap.generalis.OptionsOftalSilia,
            OptionsOftalConjHiperemis: state => state.soap.generalis.OptionsOftalConjHiperemis,
            OptionsOftalConjBleed: state => state.soap.generalis.OptionsOftalConjBleed,
            OptionsOftalKornea: state => state.soap.generalis.OptionsOftalKornea,
            OptionsOftalAnterior: state => state.soap.generalis.OptionsOftalAnterior,
            OptionsOftalIrisColor: state => state.soap.generalis.OptionsOftalIrisColor,
            OptionsOftalIrisKripte: state => state.soap.generalis.OptionsOftalIrisKripte,
            OptionsOftalPupilForm: state => state.soap.generalis.OptionsOftalPupilForm,
            OptionsOftalPupilDiameter: state => state.soap.generalis.OptionsOftalPupilDiameter,
            OptionsOftalPupilDirectLight: state => state.soap.generalis.OptionsOftalPupilDirectLight,
            OptionsOftalPupilIndirectLight: state => state.soap.generalis.OptionsOftalPupilIndirectLight,
            OptionsOftalLens: state => state.soap.generalis.OptionsOftalLens,
            OptionsOftalGlandula: state => state.soap.generalis.OptionsOftalGlandula,
            OptionsOftalLapang: state => state.soap.generalis.OptionsOftalLapang,
            OptionsOftalShadow: state => state.soap.generalis.OptionsOftalShadow,
            
            // prescription
            parent_package: state => state.frontoptions.doctor.assessment.prescription.parent_package,
            parent_stock: state => state.frontoptions.doctor.assessment.prescription.parent_stock,
            kardex_item_status: state => state.frontoptions.doctor.assessment.prescription.kardex_item_status,

            // transfer internal
            mobilization: state => state.frontoptions.general.transfer.mobilization,

            // checkout
            type_visit: state => state.frontoptions.general.checkout.type_visit,
            type_emergency: state => state.helper.options.typeEmergency,

            // spri
            indication: state => state.frontoptions.general.spri.indication,            
            condition: state => state.frontoptions.general.spri.condition,    
            
            // consultation
            consultation_type: state => state.frontoptions.doctor.consultation.consultation_type,
            
            // operatie
            operation_type: state => state.frontoptions.general.operatie.operation_type,
            infectious: state => state.frontoptions.general.operatie.infectious,

            // master-room
            surgery: state => state.frontoptions.master.room.surgery,
            isolation: state => state.frontoptions.master.room.isolation,
            room_pressure: state => state.frontoptions.master.room.room_pressure,
            gender_availability: state => state.frontoptions.master.room.gender_availability,
            internal_disease: state => state.frontoptions.master.room.internal_disease,

            // resume
            exit: state => state.frontoptions.doctor.resume.exit, 
            exit_condition: state => state.frontoptions.doctor.resume.exit_condition, 
            control_instruction: state => state.frontoptions.doctor.resume.control_instruction, 
        })
    },

    methods: {
        // Anamnesa
        getAnamnesaName(value) {
            const result = this.OptionAnamnesa.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getFamilyName(value) {
            const result = this.OptionFamily.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // Tanda Vital
        getOxygenName(value) {
            const result = this.OptionsOxygen.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getAwareName(value) {
            const result = this.OptionsAware.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsEyeName(value) {
            const result = this.OptionGcsEye.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsVerbalName(value) {
            const result = this.OptionGcsVerbal.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsMotoricName(value) {
            const result = this.OptionGcsMotoric.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getOxygenPoint(value) {
            const result = this.OptionsOxygenPoint.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getAwarePoint(value) {
            const result = this.OptionsAwarePoint.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsEyePoint(value) {
            const result = this.OptionGcsEye.find((item) => item.value == value);
            return result ? result.value : '-';
        },
        getGcsVerbalPoint(value) {
            const result = this.OptionGcsVerbal.find((item) => item.value == value);
            return result ? result.value : '-';
        },
        getGcsMotoricPoint(value) {
            const result = this.OptionGcsMotoric.find((item) => item.value == value);
            return result ? result.value : '-';
        },

        getGcsScore(obj) {
            let eye = Number(this.getGcsEyePoint(obj.eye)) || 0
            let verbal = Number(this.getGcsVerbalPoint(obj.verbal)) || 0
            let motor = Number(this.getGcsMotoricPoint(obj.motor)) || 0
            return eye + verbal + motor
        },
        getColorSkinName(value) {
            const result = this.skin_color.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getBehaviorName(value) {
            const result = this.behavior.find((item) => item.value == value);
            return result ? result.title : '-';
        },


        // Status Generalis
        getHead(param) {
            const obj = this.OptionsHeadCircumference.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getHairColor(param) {
            const obj = this.OptionsHeadHairColor.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getHairCondition(param) {
            const obj = this.OptionsHeadHairCondition.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeConjunctive(param) {
            const obj = this.OptionsEyeConjunctiva.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeSclera(param) {
            const obj = this.OptionsEyeSclera.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyePupil(param) {
            const obj = this.OptionsEyePupil.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeReflect(param) {
            const obj = this.OptionsEyeReflect.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeBlindColor(param) {
            const obj = this.OptionsEyeBlindColor.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthMucosa(param) {
            const obj = this.OptionsMouthMukosa.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthDiscoloration(param) {
            const obj = this.OptionsMouthHiperemis.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthTongue(param) {
            const obj = this.OptionsMouthTongue.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthCaries(param) {
            const obj = this.OptionsMouthKaries.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckKGB(param) {
            const obj = this.OptionsNeckKGB.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckTracheaPosition(param) {
            const obj = this.OptionsNeckTrakea.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckMassa(param) {
            const obj = this.OptionsNeckMassa.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckJVP(param) {
            const obj = this.OptionsNeckJVP.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getInspectionChest(param) {
            const obj = this.OptionsInspectionChest.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getInspectionRetractiont(param) {
            const obj = this.OptionsInspectionRetraction.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationChest(param) {
            const obj = this.OptionsPalpationChest.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationFremitus(param) {
            const obj = this.OptionsPalpationFremitus.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationKrepitasi(param) {
            const obj = this.OptionsPalpationKrepitasi.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationPulmoVasikuler(param) {
            const obj = this.OptionsAuskultationPulmoVasikuler.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        
        getAuskultationPulmoRhonki(param) {
            const obj = this.OptionsAuskultationPulmoRhonki.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        
        getAuskultationPulmoWheezing(param) {
            const obj = this.OptionsAuskultationPulmoWheezing.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORBJ(param) {
            const obj = this.OptionsAuskultationCORBJ.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORMurmur(param) {
            const obj = this.OptionsAuskultationCORMurmur.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORGallop(param) {
            const obj = this.OptionsAuskultationCORGallop.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenSupel(param) {
            const obj = this.OptionsAbdomenSupel.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenBising(param) {
            const obj = this.OptionsAbdomenBising.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenPress(param) {
            const obj = this.OptionsAbdomenPress.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenHepar(param) {
            const obj = this.OptionsAbdomenHepar.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasAkral(param) {
            const obj = this.OptionsEkstremitasAkral.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasCRT(param) {
            const obj = this.OptionsEkstremitasCRT.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasEdema(param) {
            const obj = this.OptionsEkstremitasEdema.find(el => el.value === param)
            return obj ? obj.title : '-'
        },   
        getOftalVisus(param) {
            const obj = this.OptionsOftalVisus.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalTIO(param) {
            const obj = this.OptionsOftalTIO.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalEyePosition(param) {
            const obj = this.OptionsOftalEyePosition.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalMovement(param) {
            const obj = this.OptionsOftalMovement.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalSuperSilia(param) {
            const obj = this.OptionsOftalSuperSilia.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPalpebraEdema(param) {
            const obj = this.OptionsOftalPalpebraEdema.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPalpebraPtosis(param) {
            const obj = this.OptionsOftalPalpebraPtosis.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPalpebraLagophtalmus(param) {
            const obj = this.OptionsOftalPalpebraLagophtalmus.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPalpebraTumor(param) {
            const obj = this.OptionsOftalPalpebraTumor.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalApparetus(param) {
            const obj = this.OptionsOftalApparetus.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalSilia(param) {
            const obj = this.OptionsOftalSilia.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalConjHiperemis(param) {
            const obj = this.OptionsOftalConjHiperemis.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalConjBleed(param) {
            const obj = this.OptionsOftalConjBleed.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalKornea(param) {
            const obj = this.OptionsOftalKornea.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalAnterior(param) {
            const obj = this.OptionsOftalAnterior.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalIrisColor(param) {
            const obj = this.OptionsOftalIrisColor.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalIrisKripte(param) {
            const obj = this.OptionsOftalIrisKripte.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPupilForm(param) {
            const obj = this.OptionsOftalPupilForm.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPupilDiameter(param) {
            const obj = this.OptionsOftalPupilDiameter.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPupilDirectLight(param) {
            const obj = this.OptionsOftalPupilDirectLight.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalPupilIndirectLight(param) {
            const obj = this.OptionsOftalPupilIndirectLight.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalLens(param) {
            const obj = this.OptionsOftalLens.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalGlandula(param) {
            const obj = this.OptionsOftalGlandula.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalLapang(param) {
            const obj = this.OptionsOftalLapang.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getOftalShadow(param) {
            const obj = this.OptionsOftalShadow.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        // transfer internal
        getMobilizationName(value) {
            const result = this.mobilization.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // checkout
        getTypeVisitName(value) {
            const result = this.type_visit.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getTypeEmergencyName(value) {
            const result = this.type_emergency.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // spri
        getIndicationName(value) {
            const result = this.indication.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getConditionName(value) {
            const result = this.condition.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // prescription
        getParentPackageName(value) {
            const result = this.parent_package.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        getPrescParentName(value){
            const result = this.parent_stock.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        getKardexItemStatusName(value){
            const result = this.kardex_item_status.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        getKardexItemStatusVariant(status) {
            if (status == 1) return 'info-700'
            if (status == 2) return 'success-600'
            if (status == 3) return 'warning-800'
            if (status == 4) return 'danger-400'
            return ''
        },

        // consultation
        getConsultationTypeName(value) {
            const result = this.consultation_type.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // peratie
        getOperationTypeName(value) {
            const result = this.operation_type.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        getInfectiousName(value) {
            const result = this.infectious.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // master-room
        getSurgeryName(value) {
            const result = this.surgery.find((item) => item.value === value);
            return result ? result.title : '-';
        },

        getIsolationName(value) {
            const result = this.isolation.find((item) => item.value === value);
            return result ? result.title : '-';
        },

        getRoomPressure(value) {
            // if (value === null || value === '') return 'Normal'            
            const result = this.room_pressure.find((item) => item.value === value);
            return result ? result.title : '';
        },

        getGenderAvailabilityName(value) {
            // if (value === null || value === '') return 'Genderless'            
            const result = this.gender_availability.find((item) => item.value === value);
            return result ? result.title : '-';
        },      

        getInternalDiseaseName(value) {
            const result = this.internal_disease.find((item) => item.value === value);
            return result ? result.title : '-';
        },      

        // resume
        getResExit(value) {
            const result = this.exit.find((item) => item.value === value);
            return result ? result.title : '-';
        },      

        getResExitConditionName(value) {
            const result = this.exit_condition.find((item) => item.value === value);
            return result ? result.title : '-';
        },      

        getResControlInstructionName(value) {
            const result = this.control_instruction.find((item) => item.value === value);
            return result ? result.title : '-';
        },      
        
        // FROM DYNAMIC OPT
        getBedStatusName(value) {
            if(value == 1) return 'Available'       
            if(value == 2) return 'Filled'       
            if(value == 3) return 'Linen'       
            return '-'
        }, 
        
        getBedStatusVariant(status) {
            if (status == 1) return 'bg-neutral-100'
            if (status == 2) return 'bg-w3green text-white'
            if (status == 3) return 'bg-blinking'
            // if (status == 3) return 'bg-warning-500 text-white'
            return ''
        },

        getSexCode(value) {
            if (value != undefined) {
                if(value === 0) return 'P'       
                if(value == 1) return 'L'       
                return '-'
            }
            return '-'
        }, 

        getPrescParentVariant(value){
            if (value == 1) return 'bg-primary-100'
            if (value == 2) return 'bg-success-100'
            if (value == 3) return 'bg-orange-100'
            if (value == 4) return 'bg-danger-100'
            if (value == 5) return 'bg-pink-100'
            return 'bg-white'
        },
        
        getDynamicOptName(value, list) {
            const result = list.find((item) => item.value === value);
            return result ? result.title : '-';
        },  
    },


};
