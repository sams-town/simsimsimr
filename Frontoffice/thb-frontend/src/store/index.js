import Vue from "vue";
import Vuex from "vuex";
import patient from "./mr/patient";
import helper from "./setting/helper";
import healthfacility from "./setting/healthfacility";
import healthfacilityitem from "./setting/healthfacilityitem";
import serviceclass from "./setting/serviceclass";
import employee from "./hr/employee";
import icd10 from "./mr/icd10";
import icd9cm from "./mr/icd9cm";
import position from "./hr/position";
import registration from "./mr/registration";
import howtoenter from "./setting/howtoenter";
import catalog from "./pharmacy/catalog";
import prescription from "./pharmacy/prescription";
import kardex from "./pharmacy/kardex";
import mutation from "./pharmacy/mutation";
import depo from "./pharmacy/depo";
import generik from "./pharmacy/generik";
import counseling from "./pharmacy/counseling";
import appointment from "./mr/registration/appointment";
import nurse from "./mr/nurse";
import nutrition from "./mr/nutrition";
import treatment from "./mr/treatment";
import jkn from "./mr/jkn";
import vitalsign from "./mr/vitalsign";
import fetalheart from "./mr/fetalheart";
import slidingscale from "./mr/slidingscale";
import bloodtransfusion from "./mr/bloodtransfusion";
import ews from "./mr/ews";
import partograph from "./mr/partograph";
import birthrecord from "./mr/birthrecord";
import temperaturechart from "./mr/temperaturechart";
import fluidbalance from "./mr/fluidbalance";
import operatie from "./mr/operatie";
import triage from "./mr/triage";
import sbar from "./mr/sbar";
import resume from "./mr/resume";
import cppt from "./mr/cppt";
import soap from "./mr/soap";
import sirs from "./mr/sirs";
import costing from "./finance/costing";
import classification from "./backoffice/classification";
import payment from "./tr/payment";
import laboratory from "./health_facility/laboratory";
import radiology from "./health_facility/radiology";
import physiotherapy from "./health_facility/physiotherapy";
import doctorschedule from "./medical_support/doctorschedule";
import company from "./third_party/company";
import module from "./setting/module";
import user from "./setting/user";
import queue from "./setting/queue";
import group from "./setting/group";
import display from "./setting/display";
import moduleitem from "./setting/moduleitem";
import exit from "./setting/exit";
import general from "./general/general";
import timestamp from "./setting/timestamp";
import brand from "./third_party/brand";
import category_catalog from "./third_party/category_catalog";
import packaged from "./setting/package";
import dose from "./setting/dose";
import opname from "./pharmacy/opname";
import letter from "./mr/letter";
import lettertype from "./setting/letter";
import serviceactivity from "./report/serviceactivity";
import dashboard from "./dashboard/dashboard";
import executive from "./dashboard/executive";
import doctorscheduleitem from "./medical_support/doctorscheduleitem";
import room from "./health_facility/room";
import packages from "./setting/packages";
import signaturevalidation from "./setting/signaturevalidation";
import planning from "./pharmacy/planning";
import purchaseorder from "./pharmacy/purchaseorder";
import reduction from "./pharmacy/reduction";
import paymentmethod from "./finance/paymentmethod";
import receipt from "./pharmacy/receipt";
import assets from "./pharmacy/assets";
import rackname from "./pharmacy/rackname";
import rack from "./pharmacy/rack";
import tool from "./mr/tool";
import blood from "./mr/blood";
import administration from "./mr/administration";
import bhp from "./mr/bhp";
import bed from "./mr/bed";
import cssd from "./cssd/cssd";
import rehabilitation from "./mr/rehabilitation";
import hemodialysis from "./mr/hemodialysis";
import painobservation from "./mr/painobservation";
import consultation from "./mr/consultation";
import templating from "./setting/templating";
import frontoptions from "./front_options/frontoptions"
import soapinpatient from "./mr/soapinpatient"
import notification from "./setting/notification"
import codeblue from "./code_blue/codeblue"
import department from "./department/department";
import ads from "./setting/ads";
import satusehat from "./bridging/satusehat"
import setting from "./setting/setting"
// import ai from "./ai/ai";

import appConfig from './app-config'
import verticalMenu from './vertical-menu'
import app from './app'

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    patient,
    helper,
    healthfacility,
    healthfacilityitem,
    rackname,
    serviceclass,
    employee,
    icd10,
    icd9cm,
    position,
    registration,
    howtoenter,
    catalog,
    prescription,
    kardex,
    counseling,
    depo,
    generik,
    appointment,
    nutrition,
    nurse,
    triage,
    operatie,
    treatment,
    jkn,
    vitalsign,
    fetalheart,
    slidingscale,
    bloodtransfusion,
    ews,
    partograph,
    birthrecord,
    temperaturechart,
    fluidbalance,
    rehabilitation,
    hemodialysis,
    sbar,
    resume,
    cppt,
    soap,
    sirs,
    costing,
    classification,
    payment,
    laboratory,
    radiology,
    physiotherapy,
    mutation,
    company,
    module,
    user,
    queue,
    group,
    moduleitem,
    exit,
    general,
    timestamp,
    doctorschedule,
    brand,
    category_catalog,
    packaged,
    dose,
    opname,
    lettertype,
    letter,
    serviceactivity,
    dashboard,
    doctorscheduleitem,
    room,
    packages,
    signaturevalidation,
    planning,
    purchaseorder,
    reduction,
    paymentmethod,
    receipt,
    assets,
    tool,
    blood,
    administration,
    rack,
    bhp,
    bed,
    cssd,
    display,
    painobservation,
    consultation,
    templating,
    frontoptions,
    soapinpatient,
    notification,
    codeblue,
    department,
    executive,
    // ai,
    appConfig,
    verticalMenu,
    ads,
    app,
    satusehat,
    setting,
  },
  strict: process.env.DEV,
});
