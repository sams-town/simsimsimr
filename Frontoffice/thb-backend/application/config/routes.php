<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
// Initial API Routes
$route['{VERSION}/initial/register'] = '{VERSION}/initial/register';

// authentication API Routes
$route['{VERSION}/authentication/auth/signin'] = '{VERSION}/authentication/auth/signin';
$route['{VERSION}/authentication/auth/profile'] = '{VERSION}/authentication/auth/profile';

// setting/Institute API Routes
$route['{VERSION}/setting/institute/detail'] = '{VERSION}/setting/institute/detail';
$route['{VERSION}/setting/institute/detail/(:any)'] = '{VERSION}/setting/institute/detail/$1';
$route['{VERSION}/setting/institute/update/(:any)'] = '{VERSION}/setting/institute/update/$1';

// setting/Institute_branch API Routes
$route['{VERSION}/setting/institute_branch/create'] = '{VERSION}/setting/institute_branch/create';
$route['{VERSION}/setting/institute_branch/detail'] = '{VERSION}/setting/institute_branch/detail';
$route['{VERSION}/setting/institute_branch/detail/(:any)'] = '{VERSION}/setting/institute_branch/detail/$1';
$route['{VERSION}/setting/institute_branch/update/(:any)'] = '{VERSION}/setting/institute_branch/update/$1';

// setting/Module API Routes
$route['{VERSION}/setting/module/create'] = '{VERSION}/setting/module/create';
$route['{VERSION}/setting/module/detail'] = '{VERSION}/setting/module/detail';
$route['{VERSION}/setting/module/detail/(:any)'] = '{VERSION}/setting/module/detail/$1';
$route['{VERSION}/setting/module/update/(:any)'] = '{VERSION}/setting/module/update/$1';
$route['{VERSION}/setting/module/delete/(:any)'] = '{VERSION}/setting/module/delete/$1';

// setting/Module_item API Routes
$route['{VERSION}/setting/module_item/create'] = '{VERSION}/setting/module_item/create';
$route['{VERSION}/setting/module_item/detail'] = '{VERSION}/setting/module_item/detail';
$route['{VERSION}/setting/module_item/detail/(:any)'] = '{VERSION}/setting/module_item/detail/$1';
$route['{VERSION}/setting/module_item/update/(:any)'] = '{VERSION}/setting/module_item/update/$1';
$route['{VERSION}/setting/module_item/delete/(:any)'] = '{VERSION}/setting/module_item/delete/$1';

// setting/Group API Routes
$route['{VERSION}/setting/group/create'] = '{VERSION}/setting/group/create';
$route['{VERSION}/setting/group/detail'] = '{VERSION}/setting/group/detail';
$route['{VERSION}/setting/group/detail/(:any)'] = '{VERSION}/setting/group/detail/$1';
$route['{VERSION}/setting/group/update/(:any)'] = '{VERSION}/setting/group/update/$1';
$route['{VERSION}/setting/group/delete/(:any)'] = '{VERSION}/setting/group/delete/$1';

// setting/Group_module_item API Routes
$route['{VERSION}/setting/group_module_item/create'] = '{VERSION}/setting/group_module_item/create';
$route['{VERSION}/setting/group_module_item/detail'] = '{VERSION}/setting/group_module_item/detail';
$route['{VERSION}/setting/group_module_item/detail/(:any)'] = '{VERSION}/setting/group_module_item/detail/$1';
$route['{VERSION}/setting/group_module_item/update/(:any)'] = '{VERSION}/setting/group_module_item/update/$1';
$route['{VERSION}/setting/group_module_item/delete/(:any)'] = '{VERSION}/setting/group_module_item/delete/$1';

// setting/Helper API Routes
$route['{VERSION}/setting/helper/create'] = '{VERSION}/setting/helper/create';
$route['{VERSION}/setting/helper/detail'] = '{VERSION}/setting/helper/detail';
$route['{VERSION}/setting/helper/detail/(:any)'] = '{VERSION}/setting/helper/detail/$1';
$route['{VERSION}/setting/helper/update/(:any)'] = '{VERSION}/setting/helper/update/$1';
$route['{VERSION}/setting/helper/delete/(:any)'] = '{VERSION}/setting/helper/delete/$1';

// setting/Service_class API Routes
$route['{VERSION}/setting/service_class/create'] = '{VERSION}/setting/service_class/create';
$route['{VERSION}/setting/service_class/detail'] = '{VERSION}/setting/service_class/detail';
$route['{VERSION}/setting/service_class/detail/(:any)'] = '{VERSION}/setting/service_class/detail/$1';
$route['{VERSION}/setting/service_class/update/(:any)'] = '{VERSION}/setting/service_class/update/$1';
$route['{VERSION}/setting/service_class/delete/(:any)'] = '{VERSION}/setting/service_class/delete/$1';

// setting/How_To_Enter API Routes
$route['{VERSION}/setting/How_To_Enter/create'] = '{VERSION}/setting/How_To_Enter/create';
$route['{VERSION}/setting/How_To_Enter/detail'] = '{VERSION}/setting/How_To_Enter/detail';
$route['{VERSION}/setting/How_To_Enter/detail/(:any)'] = '{VERSION}/setting/How_To_Enter/detail/$1';
$route['{VERSION}/setting/How_To_Enter/update/(:any)'] = '{VERSION}/setting/How_To_Enter/update/$1';
$route['{VERSION}/setting/How_To_Enter/delete/(:any)'] = '{VERSION}/setting/How_To_Enter/delete/$1';

// setting/Exit API Routes
$route['{VERSION}/setting/Exits/create'] = '{VERSION}/setting/Exits/create';
$route['{VERSION}/setting/Exits/detail'] = '{VERSION}/setting/Exits/detail';
$route['{VERSION}/setting/Exits/detail/(:any)'] = '{VERSION}/setting/Exits/detail/$1';
$route['{VERSION}/setting/Exits/update/(:any)'] = '{VERSION}/setting/Exits/update/$1';
$route['{VERSION}/setting/Exits/delete/(:any)'] = '{VERSION}/setting/Exits/delete/$1';

// setting/Timestamp API Routes
$route['{VERSION}/setting/Timestamp/create'] = '{VERSION}/setting/Timestamp/create';
$route['{VERSION}/setting/Timestamp/detail'] = '{VERSION}/setting/Timestamp/detail';
$route['{VERSION}/setting/Timestamp/detail/(:any)'] = '{VERSION}/setting/Timestamp/detail/$1';
$route['{VERSION}/setting/Timestamp/update/(:any)'] = '{VERSION}/setting/Timestamp/update/$1';
$route['{VERSION}/setting/Timestamp/delete/(:any)'] = '{VERSION}/setting/Timestamp/delete/$1';

$route['v1/setting/detail'] = 'v1/setting/setting/detail';
$route['v1/setting/update'] = 'v1/setting/setting/update';

// setting/Template Anamnesis API Routes
$route['v1/setting/user/template_anamnesis/detail'] = 'v1/setting/template_anamnesis/detail';
$route['v1/setting/user/template_anamnesis/create'] = 'v1/setting/template_anamnesis/create';
$route['v1/setting/user/template_anamnesis/update/(:any)'] = 'v1/setting/template_anamnesis/update/$1';
$route['v1/setting/user/template_anamnesis/delete/(:any)'] = 'v1/setting/template_anamnesis/delete/$1';

// setting/Template Resume API Routes
$route['v1/setting/user/template_resume/detail'] = 'v1/setting/template_resume/detail';
$route['v1/setting/user/template_resume/create'] = 'v1/setting/template_resume/create';
$route['v1/setting/user/template_resume/update/(:any)'] = 'v1/setting/template_resume/update/$1';
$route['v1/setting/user/template_resume/delete/(:any)'] = 'v1/setting/template_resume/delete/$1';

// third_party/Company API Routes
$route['{VERSION}/third_party/company/create'] = '{VERSION}/third_party/company/create';
$route['{VERSION}/third_party/company/detail'] = '{VERSION}/third_party/company/detail';
$route['{VERSION}/third_party/company/detail/(:any)'] = '{VERSION}/third_party/company/detail/$1';
$route['{VERSION}/third_party/company/update/(:any)'] = '{VERSION}/third_party/company/update/$1';
$route['{VERSION}/third_party/company/delete/(:any)'] = '{VERSION}/third_party/company/delete/$1';

// Medical Support API Routes
$route['{VERSION}/medicalSupport/Schedule_Doctor/create'] = '{VERSION}/medicalSupport/Schedule_Doctor/create';
$route['{VERSION}/medicalSupport/Schedule_Doctor/detail'] = '{VERSION}/medicalSupport/Schedule_Doctor/detail';
$route['{VERSION}/medicalSupport/Schedule_Doctor/detail/(:any)'] = '{VERSION}/medicalSupport/Schedule_Doctor/detail/$1';
$route['{VERSION}/medicalSupport/Schedule_Doctor/update/(:any)'] = '{VERSION}/medicalSupport/Schedule_Doctor/update/$1';
$route['{VERSION}/medicalSupport/Schedule_Doctor/delete/(:any)'] = '{VERSION}/medicalSupport/Schedule_Doctor/delete/$1';

// Medical Support API Routes
$route['{VERSION}/medicalSupport/Schedule_Doctor/create'] = '{VERSION}/medicalSupport/Schedule_Doctor/create';
$route['{VERSION}/medicalSupport/Schedule_Doctor/detail'] = '{VERSION}/medicalSupport/Schedule_Doctor/detail';
$route['{VERSION}/medicalSupport/Schedule_Doctor/detail/(:any)'] = '{VERSION}/medicalSupport/Schedule_Doctor/detail/$1';
$route['{VERSION}/medicalSupport/Schedule_Doctor/update/(:any)'] = '{VERSION}/medicalSupport/Schedule_Doctor/update/$1';
$route['{VERSION}/medicalSupport/Schedule_Doctor/delete/(:any)'] = '{VERSION}/medicalSupport/Schedule_Doctor/delete/$1';

// md/Allergy API Routes
$route['{VERSION}/md/allergy/create'] = '{VERSION}/md/allergy/create';
$route['{VERSION}/md/allergy/detail'] = '{VERSION}/md/allergy/detail';
$route['{VERSION}/md/allergy/detail/(:any)'] = '{VERSION}/md/allergy/detail/$1';
$route['{VERSION}/md/allergy/update/(:any)'] = '{VERSION}/md/allergy/update/$1';
$route['{VERSION}/md/allergy/delete/(:any)'] = '{VERSION}/md/allergy/delete/$1';

// mr/Patient API Routes
$route['{VERSION}/mr/patient/create'] = '{VERSION}/mr/patient/create';
$route['{VERSION}/mr/patient/detail'] = '{VERSION}/mr/patient/detail';
$route['{VERSION}/mr/patient/detail/(:any)'] = '{VERSION}/mr/patient/detail/$1';
$route['{VERSION}/mr/patient/update/(:any)'] = '{VERSION}/mr/patient/update/$1';
$route['{VERSION}/mr/patient/delete/(:any)'] = '{VERSION}/mr/patient/delete/$1';

// mr/Patient_allergy API Routes
$route['{VERSION}/mr/patient_allergy/create'] = '{VERSION}/mr/patient_allergy/create';
$route['{VERSION}/mr/patient_allergy/detail'] = '{VERSION}/mr/patient_allergy/detail';
$route['{VERSION}/mr/patient_allergy/detail/(:any)'] = '{VERSION}/mr/patient_allergy/detail/$1';
$route['{VERSION}/mr/patient_allergy/update/(:any)'] = '{VERSION}/mr/patient_allergy/update/$1';

// mr/Insurance API Routes
$route['{VERSION}/mr/insurance/create'] = '{VERSION}/mr/insurance/create';
$route['{VERSION}/mr/insurance/detail'] = '{VERSION}/mr/insurance/detail';
$route['{VERSION}/mr/insurance/detail/(:any)'] = '{VERSION}/mr/insurance/detail/$1';
$route['{VERSION}/mr/insurance/update/(:any)'] = '{VERSION}/mr/insurance/update/$1';

// mr/Icd10 API Routes
$route['{VERSION}/mr/icd10/create'] = '{VERSION}/mr/icd10/create';
$route['{VERSION}/mr/icd10/detail'] = '{VERSION}/mr/icd10/detail';
$route['{VERSION}/mr/icd10/detail/(:any)'] = '{VERSION}/mr/icd10/detail/$1';
$route['{VERSION}/mr/icd10/update/(:any)'] = '{VERSION}/mr/icd10/update/$1';
$route['{VERSION}/mr/icd10/delete/(:any)'] = '{VERSION}/mr/icd10/delete/$1';

// mr/Icd9cm API Routes
$route['{VERSION}/mr/icd9cm/create'] = '{VERSION}/mr/icd9cm/create';
$route['{VERSION}/mr/icd9cm/detail'] = '{VERSION}/mr/icd9cm/detail';
$route['{VERSION}/mr/icd9cm/detail/(:any)'] = '{VERSION}/mr/icd9cm/detail/$1';
$route['{VERSION}/mr/icd9cm/update/(:any)'] = '{VERSION}/mr/icd9cm/update/$1';
$route['{VERSION}/mr/icd9cm/delete/(:any)'] = '{VERSION}/mr/icd9cm/delete/$1';

// mr/Reference API Routes
$route['{VERSION}/mr/reference/create'] = '{VERSION}/mr/reference/create';
$route['{VERSION}/mr/reference/detail'] = '{VERSION}/mr/reference/detail';
$route['{VERSION}/mr/reference/detail/(:any)'] = '{VERSION}/mr/reference/detail/$1';
$route['{VERSION}/mr/reference/update/(:any)'] = '{VERSION}/mr/reference/update/$1';
$route['{VERSION}/mr/reference/delete/(:any)'] = '{VERSION}/mr/reference/delete/$1';

// mr/Document API Routes
$route['{VERSION}/mr/document/create'] = '{VERSION}/mr/document/create';
$route['{VERSION}/mr/document/detail'] = '{VERSION}/mr/document/detail';
$route['{VERSION}/mr/document/detail/(:any)'] = '{VERSION}/mr/document/detail/$1';
$route['{VERSION}/mr/document/update/(:any)'] = '{VERSION}/mr/document/update/$1';

// mr/Registration API Routes
$route['{VERSION}/mr/registration/create'] = '{VERSION}/mr/registration/create';
$route['{VERSION}/mr/registration/detail'] = '{VERSION}/mr/registration/detail';
$route['{VERSION}/mr/registration/detail/(:any)'] = '{VERSION}/mr/registration/detail/$1';
$route['{VERSION}/mr/registration/cancel/(:any)'] = '{VERSION}/mr/registration/cancel/$1';

// mr/Registration_insurance API Routes
$route['{VERSION}/mr/registration_insurance/detail'] = '{VERSION}/mr/registration_insurance/detail';
$route['{VERSION}/mr/registration_insurance/detail/(:any)'] = '{VERSION}/mr/registration_insurance/detail/$1';

// mr/Registration_destination API Routes
$route['{VERSION}/mr/registration_destination/detail'] = '{VERSION}/mr/registration_destination/detail';
$route['{VERSION}/mr/registration_destination/detail/(:any)'] = '{VERSION}/mr/registration_destination/detail/$1';
$route['{VERSION}/mr/registration_destination/update/(:any)'] = '{VERSION}/mr/registration_destination/update/$1';

// mr/Mr_timestamp_patient API Routes
$route['{VERSION}/mr/mr_timestamp_patient/create'] = '{VERSION}/mr/mr_timestamp_patient/create';
$route['{VERSION}/mr/mr_timestamp_patient/detail'] = '{VERSION}/mr/mr_timestamp_patient/detail';
$route['{VERSION}/mr/mr_timestamp_patient/detail/(:any)'] = '{VERSION}/mr/mr_timestamp_patient/detail/$1';
$route['{VERSION}/mr/mr_timestamp_patient/update/(:any)'] = '{VERSION}/mr/mr_timestamp_patient/update/$1';

// mr/Soap API Routes
$route['{VERSION}/mr/soap/create'] = '{VERSION}/mr/soap/create';
$route['{VERSION}/mr/soap/create_full'] = '{VERSION}/mr/soap/create_full';
$route['{VERSION}/mr/soap/detail'] = '{VERSION}/mr/soap/detail';
$route['{VERSION}/mr/soap/detail/(:any)'] = '{VERSION}/mr/soap/detail/$1';
$route['{VERSION}/mr/soap/update/(:any)'] = '{VERSION}/mr/soap/update/$1';
$route['{VERSION}/mr/soap/delete/(:any)'] = '{VERSION}/mr/soap/delete/$1';

// mr/Soap_s API Routes
$route['{VERSION}/mr/soap_s/create'] = '{VERSION}/mr/soap_s/create';
$route['{VERSION}/mr/soap_s/detail'] = '{VERSION}/mr/soap_s/detail';
$route['{VERSION}/mr/soap_s/detail/(:any)'] = '{VERSION}/mr/soap_s/detail/$1';
$route['{VERSION}/mr/soap_s/update/(:any)'] = '{VERSION}/mr/soap_s/update/$1';

// mr/neonatus
// Neonatus Routes
// $route['{VERSION}/mr/neonatus'] = '{VERSION}/mr/neonatus/index'; // GET all neonatus records
$route['{VERSION}/mr/neonatus/detail_neonatus/(:any)'] = '{VERSION}/mr/neonatus/detail_neonatus/$1'; // GET single neonatus record by ID
$route['{VERSION}/mr/neonatus/create'] = '{VERSION}/mr/neonatus/create'; // POST to create new neonatus record
$route['{VERSION}/mr/neonatus/update/(:any)'] = '{VERSION}/mr/neonatus/update/$1'; // PUT/PATCH to update a specific neonatus record
$route['{VERSION}/mr/neonatus/delete/(:any)'] = '{VERSION}/mr/neonatus/delete/$1'; // DELETE to remove a specific neonatus record

$route['{VERSION}/mr/child/detail_child/(:any)'] = '{VERSION}/mr/child/detail_child/$1'; // GET single child record by ID
$route['{VERSION}/mr/child/create'] = '{VERSION}/mr/child/create'; // POST to create new child record
$route['{VERSION}/mr/child/update/(:any)'] = '{VERSION}/mr/child/update/$1'; // PUT/PATCH to update a specific child record
$route['{VERSION}/mr/child/delete/(:any)'] = '{VERSION}/mr/child/delete/$1'; // DELETE to remove a specific child record


// tr/transaction_administration API Routes
$route['{VERSION}/tr/transaction_administration/detail'] = '{VERSION}/tr/transaction_administration/detail';
$route['{VERSION}/tr/transaction_administration/create'] = '{VERSION}/tr/transaction_administration/create';
$route['{VERSION}/tr/transaction_administration/detail/(:any)'] = '{VERSION}/tr/transaction_administration/detail/$1';
$route['{VERSION}/tr/transaction_administration/update/(:any)'] = '{VERSION}/tr/transaction_administration/update/$1';

// general/Catalog API Routes
$route['{VERSION}/general/catalog/create'] = '{VERSION}/general/catalog/create';
$route['{VERSION}/general/catalog/detail'] = '{VERSION}/general/catalog/detail';
$route['{VERSION}/general/catalog/detail/(:any)'] = '{VERSION}/general/catalog/detail/$1';
$route['{VERSION}/general/catalog/update/(:any)'] = '{VERSION}/general/catalog/update/$1';
$route['{VERSION}/general/catalog/delete/(:any)'] = '{VERSION}/general/catalog/delete/$1';

// pharmacy/Catalog API Routes
$route['{VERSION}/pharmacy/catalog/create'] = '{VERSION}/pharmacy/catalog/create';
$route['{VERSION}/pharmacy/catalog/detail'] = '{VERSION}/pharmacy/catalog/detail';
$route['{VERSION}/pharmacy/catalog/detail/(:any)'] = '{VERSION}/pharmacy/catalog/detail/$1';
$route['{VERSION}/pharmacy/catalog/update/(:any)'] = '{VERSION}/pharmacy/catalog/update/$1';
$route['{VERSION}/pharmacy/catalog/delete/(:any)'] = '{VERSION}/pharmacy/catalog/delete/$1';

// finance/Costing API Routes
$route['{VERSION}/finance/costing/create'] = '{VERSION}/finance/costing/create';
$route['{VERSION}/finance/costing/detail'] = '{VERSION}/finance/costing/detail';
$route['{VERSION}/finance/costing/detail/(:any)'] = '{VERSION}/finance/costing/detail/$1';
$route['{VERSION}/finance/costing/update/(:any)'] = '{VERSION}/finance/costing/update/$1';
$route['{VERSION}/finance/costing/delete/(:any)'] = '{VERSION}/finance/costing/delete/$1';

// finance/Costing_services_class API Routes
$route['{VERSION}/finance/costing_services_class/create'] = '{VERSION}/finance/costing_services_class/create';
$route['{VERSION}/finance/costing_services_class/detail'] = '{VERSION}/finance/costing_services_class/detail';
$route['{VERSION}/finance/costing_services_class/detail/(:any)'] = '{VERSION}/finance/costing_services_class/detail/$1';
$route['{VERSION}/finance/costing_services_class/update/(:any)'] = '{VERSION}/finance/costing_services_class/update/$1';
$route['{VERSION}/finance/costing_services_class/delete/(:any)'] = '{VERSION}/finance/costing_services_class/delete/$1';

// finance/Costing_health_facility_item API Routes
$route['{VERSION}/finance/costing_health_facility_item/create'] = '{VERSION}/finance/costing_health_facility_item/create';
$route['{VERSION}/finance/costing_health_facility_item/detail'] = '{VERSION}/finance/costing_health_facility_item/detail';
$route['{VERSION}/finance/costing_health_facility_item/detail/(:any)'] = '{VERSION}/finance/costing_health_facility_item/detail/$1';
$route['{VERSION}/finance/costing_health_facility_item/update/(:any)'] = '{VERSION}/finance/costing_health_facility_item/update/$1';
$route['{VERSION}/finance/costing_health_facility_item/delete/(:any)'] = '{VERSION}/finance/costing_health_facility_item/delete/$1';

// Auth API Routes
// $route['v1/auth/login'] = 'v1/authentication/login';
// $route['v1/auth/profile'] = 'v1/authentication/profile';
// $route['v1/auth/otp/request'] = 'v1/authentication/otp/request';
// $route['v1/auth/otp/verify'] = 'v1/authentication/otp/verify';
// $route['v1/auth/reset'] = 'v1/authentication/reset';

// // Customer API Routes
// $route['v1/customer/register'] = 'v1/customer/register';

// // Institute API Routes
// $route['v1/institute/register'] = 'v1/institute/register';

// // Trainer API Routes
// $route['v1/trainer/register'] = 'v1/trainer/register';
// $route['v1/institute/branch'] = 'v1/institute_branch/listinstitutebranch';

// // home Routes
// $route['v1/landing/home/service'] = 'v1/home/listservice';
// // product Routes
// $route['v1/landing/category/(.+)'] = 'v1/product/listcategory/$1';
// $route['v1/landing/product/(.+)'] = 'v1/product/listproduct/$1';
// $route['v1/landing/institute/(.+)'] = 'v1/product/listinstitute/$1';
// $route['v1/landing/package/(.+)'] = 'v1/product/listpackage/$1';
// $route['v1/transaction/order'] = 'v1/transaction/order';

// // Dashboard Institute
// $route['v1/dashboard/institute/profile'] = 'v1/institute/profile';
// $route['v1/dashboard/institute/branch'] = 'v1/institute_branch/listinstitutebranchtoken';
// $route['v1/dashboard/institute/branch/create'] = 'v1/institute_branch/create';
// $route['v1/dashboard/package/create'] = 'v1/package/create';
// $route['v1/dashboard/package/(.+)'] = 'v1/package/listpackage/$1';
// $route['v1/dashboard/transaction/payment'] = 'v1/transaction/payment';
// $route['v1/dashboard/transaction/payment-method'] = 'v1/transaction/listbank';
// $route['v1/dashboard/transaction/detail/(:num)'] = 'v1/transaction/detailtransaction/$1';
// $route['v1/dashboard/transaction/(.+)'] = 'v1/transaction/listtransaction/$1';
// $route['v1/dashboard/services/material/(.+)'] = 'v1/transaction/listmaterial/$1';
// $route['v1/dashboard/services/submitdetail/(:num)'] = 'v1/transaction/submitdetail/$1';
// $route['v1/dashboard/services/trainerselect'] = 'v1/transaction/trainerselect';
// $route['v1/dashboard/services/list'] = 'v1/transaction/listservices';
// $route['v1/dashboard/services/detail/(:num)'] = 'v1/transaction/detailservices/$1';

// // Dashboard Trainer
// $route['v1/dashboard/trainer/create'] = 'v1/trainer/create';
// $route['v1/dashboard/trainer/profile'] = 'v1/trainer/profile';
// $route['v1/dashboard/trainer/(:num)'] = 'v1/trainer/detailtrainer/$1';
// $route['v1/dashboard/trainer/(.+)'] = 'v1/trainer/listtrainer/$1';

// // Backoffice Institute
// $route['v1/backoffice/institute/accept'] = 'v1/institute/verification/accept';
// $route['v1/backoffice/institute/reject'] = 'v1/institute/verification/reject';
// $route['v1/backoffice/institute/(:num)'] = 'v1/institute/detailinstitute/$1';
// $route['v1/backoffice/institute/(.+)'] = 'v1/institute/listinstitute/$1';

// // Backoffice Trainer
// $route['v1/backoffice/trainer/accept'] = 'v1/trainer/verification/accept';
// $route['v1/backoffice/trainer/reject'] = 'v1/trainer/verification/reject';
// $route['v1/backoffice/trainer/(:num)'] = 'v1/trainer/detailtrainer/$1';
// $route['v1/backoffice/trainer/(.+)'] = 'v1/trainer/listtrainer/$1';

// // Backoffice Transaction
// $route['v1/backoffice/transaction/accept'] = 'v1/transaction/verification/accept';
// $route['v1/backoffice/transaction/reject'] = 'v1/transaction/verification/reject';
// $route['v1/backoffice/transaction/verification/(.+)'] = 'v1/transaction/listverification/$1';
// $route['v1/backoffice/transaction/(:num)'] = 'v1/transaction/detailtransaction/$1';
// $route['v1/backoffice/transaction/(.+)'] = 'v1/transaction/listtransaction/$1';

// getlist Routes
// $route['getlist/(:num)'] = '/$1';

// getlistreff Routes
// $route['getlist/(:num)/(:num)'] = '/reff/$1/$1';