<?php


use App\Http\Controllers\AppealListApiController;
use App\Http\Controllers\EmcApi\GetDataController;
use App\Http\Controllers\EmcRegisterApiController;
use App\Http\Controllers\EmcShortDecisionController;
use App\Http\Controllers\InvestigationApiController;
use App\Http\Controllers\LogManagementApiController;
use App\Http\Controllers\NewsApiController;
use App\Http\Controllers\RolePermissionApiController;
use App\Http\Controllers\SettingApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CauseListController;
use App\Http\Controllers\EmcApi\EmcReportController;
use App\Http\Controllers\MobileApps\GetDataAppsController;
use App\Http\Controllers\MobileApps\GetDataController as MobileAppsGetDataController;

//----------------------for web route

Route::post('/emc-citizen-dashboard-data', [GetDataController::class, 'emc_citizen_dashboard_data']);
Route::post('/traking', [GetDataController::class, 'showAppealTraking']);
Route::post('/appeal/case/details', [GetDataController::class, 'appeal_case_details']);
Route::post('/case/for/appeal', [GetDataController::class, 'case_for_appeal']);

Route::post('/emc/v2/store-role-permission', [RolePermissionApiController::class, 'store']);


//citizen appeal store
Route::post('/store-citizen-appeal', [GetDataController::class, 'storeCitizenAppeal']);
Route::post('/emc/v2/investigator/verify/form', [InvestigationApiController::class, 'investigator_verify_form_submit']);
Route::post('/emc/v2/sumit/form/data', [InvestigationApiController::class, 'sumbitFromData']);
Route::get('/investigation/approve/', [InvestigationApiController::class, 'investigation_approve']);
Route::get('/investigation/delete/', [InvestigationApiController::class, 'investigation_delete']);
Route::post('/report_pdf', [EmcReportController::class, 'pdf_generate']);

//emc dashboard statistics data
Route::post('/dashboard/crpc/statistics/data', [DashboardController::class, 'ajaxCrpc_new']);
Route::post('/dashboard/case/adalot/data', [DashboardController::class, 'ajaxCaseStatus_new']);
Route::post('/dashboard/case/pie/chart/data', [DashboardController::class, 'ajaxPieChart_new']);
Route::post('/dashboard/heigh/chart/data', [DashboardController::class, 'get_drildown_case_count_new']);

//cause list
Route::post('/emc/cause_list', [CauseListController::class, 'index'])->name('emc_cause_list');



//for short decision
Route::post('/emc/short-decision/store', [EmcShortDecisionController::class, 'short_decision_store']);
Route::post('/emc/short-decision/update/{id}', [EmcShortDecisionController::class, 'short_decision_update']);

Route::post('/emc/peskar-short-decision/store', [EmcShortDecisionController::class, 'peskar_short_decision_update']);
Route::post('/emc/peskar-short-decision-update/{id}', [EmcShortDecisionController::class, 'peskar_short_decision_store']);


//Archive api 
Route::post('/emc/appeal/closed-list', [AppealListApiController::class, 'closed_list']);
Route::post('/emc/appeal/closed-list/search', [AppealListApiController::class, 'closed_list_search']);
Route::post('/emc/appeal/old-closed-list', [AppealListApiController::class, 'old_closed_list']);
Route::post('/emc/appeal/old-closed-list/search', [AppealListApiController::class, 'old_closed_list_search']);
Route::post('/emc/appeal/old-closed-list/details/{id}', [AppealListApiController::class, 'showAppealViewPage']);
Route::post('/emc/generate-pdf/{id}', [AppealListApiController::class, 'generate_pdf']);

Route::post('/emc/appeal/closed-list/details', [AppealListApiController::class, 'closed_list_details']);
Route::post('/emc/appeal/closed-list/case-traking', [AppealListApiController::class, 'closed_list_case_traking']);
Route::post('/emc/appeal/closed-list/nothiView', [AppealListApiController::class, 'closed_list_case_nothiView']);
Route::post('/emc/appeal/closed-list/orderSheetDetails', [AppealListApiController::class, 'closed_list_case_orderSheetDetails']);
Route::post('/emc/appeal/closed-list/shortOrderSheets', [AppealListApiController::class, 'closed_list_case_shortOrderSheets']);

//log cases api

Route::post('/emc/log_index', [LogManagementApiController::class, 'index']);
Route::post('/emc/log_index_single/{id}', [LogManagementApiController::class, 'log_index_single']);
Route::post('/emc/create_log_pdf/{id}', [LogManagementApiController::class, 'create_log_pdf']);
Route::post('/emc/log/logid/details/{id}', [LogManagementApiController::class, 'log_details_single_by_id']);

// EMC News
Route::post('/emc/news/list', [NewsApiController::class, 'index']);


//Emc Register 
Route::post('/emc/register/list', [EmcRegisterApiController::class, 'index']);
Route::post('/emc/printPdf', [EmcRegisterApiController::class, 'index']);



//EMC Crpc settings
Route::post('/emc/crpc-section/save', [SettingApiController::class, 'crpcSectionsSave']);
Route::post('/emc/crpc-section/update/{id}', [SettingApiController::class, 'crpcSectionsUpdate']);

//case count for emc
Route::post('/case/count/for/emc', [DashboardController::class, 'case_count_for_emc']);


//---------------------------for mobile apps route
Route::post('/emc-citizen-dashboard-data-apps', [GetDataAppsController::class, 'emc_citizen_dashboard_data']);
Route::post('/appeal/case/details/apps', [GetDataAppsController::class, 'appeal_case_details_apps']);
Route::post('/traking/apps', [GetDataAppsController::class, 'showAppealTraking']);
