<?php

namespace App\Http\Controllers\MobileApps;

use App\Models\EmAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\AppealRepository;
use App\Repositories\CitizenRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\PeshkarNoteRepository;
use App\Repositories\LogManagementRepository;
use App\Repositories\CitizenCaseCountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\API\BaseController as BaseController;

class GetDataAppsController extends BaseController
{
     //count org rep dashboard data
     public function emc_citizen_dashboard_data(Request $request)
     {
         
         $get_data = json_decode($request->user_data);
         $user = $get_data->auth_user;
         $request_data = $get_data->request_data;

         $data['total_pending_case_count_applicant'] = $this->total_pending_case_count_applicant($user, $request_data);
         $data['total_case_count_applicant'] = $this->total_case_count_applicant($user, $request_data);
         $data['total_running_case_count_applicant'] = $this->total_running_case_count_applicant($user, $request_data);
         $data['total_completed_case_count_applicant'] = $this->total_completed_case_count_applicant($user, $request_data);

        //  $data['total_pending_appeal_case_count_applicant'] = $this->total_pending_appeal_case_count_applicant($user);
        //  $data['total_appeal_case_count_applicant'] = $this->total_appeal_case_count_applicant($user);
        //  $data['total_running_appeal_case_count_applicant'] = $this->total_running_appeal_case_count_applicant($user);
        //  $data['total_completed_appeal_case_count_applicant'] = $this->total_completed_appeal_case_count_applicant($user);
         

        
         return $data;
     }
 
 
 
     public function total_case_count_applicant($user, $request_data)
     {

        $page = $request_data->page;
        $limit = $request_data->limit;
  
         $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['CLOSED', 'ON_TRIAL', 'ON_TRIAL_DM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*');
            
            
        $appeal_ids_from_db_data = $appeal_ids_from_db->paginate($limit, ['*'], 'page', $page); 
        $totalCount = count($appeal_ids_from_db->get());
        $data['totalCount'] = $totalCount;
        $caseList=null;
        foreach ($appeal_ids_from_db_data as $appeal_ids_from_db_single) {
            
            $ct_info = DB::table('users')
                ->where('users.common_login_user_id', $appeal_ids_from_db_single->created_by)
                ->join('em_citizens', 'users.citizen_id', 'em_citizens.id')
                ->select('em_citizens.citizen_name')
                ->first();
            if ($ct_info) {
                $applicant_name= $ct_info->citizen_name;
            }else {
                $applicant_name= null;
            }

            $court_name =  DB::table('court')
            ->where('id', $appeal_ids_from_db_single->court_id)
            ->first()->court_name;
            
            $caseList[] = [
                'id' => $appeal_ids_from_db_single->appeal_id,
                'applicant_name' => $applicant_name,
                'appeal_status' => $appeal_ids_from_db_single->appeal_status,
                'case_no' => $appeal_ids_from_db_single->case_no,
                'manual_case_no' => $appeal_ids_from_db_single->manual_case_no,
                'court_name' => $court_name,
                'next_date' => $appeal_ids_from_db_single->next_date,
            ];
        }
        $data['caseList'] = $caseList;
        return ['total_count' => $data['totalCount'], 'all_appeals' => $data['caseList']];
     }
     public function total_running_case_count_applicant($user, $request_data)
     {
        $page = $request_data->page;
        $limit = $request_data->limit;

        $appeal_ids_from_db = DB::table('em_appeal_citizens')
             ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
             ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            //  ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
             ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
             ->whereIn('em_appeals.appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])
             ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*');

        $appeal_ids_from_db_data = $appeal_ids_from_db->paginate($limit, ['*'], 'page', $page); 
        $totalCount = count($appeal_ids_from_db->get());
        $data['totalCount'] = $totalCount;
        $caseList=null;
        foreach ($appeal_ids_from_db_data as $appeal_ids_from_db_single) {
        
            $ct_info = DB::table('users')
                ->where('users.common_login_user_id', $appeal_ids_from_db_single->created_by)
                ->join('em_citizens', 'users.citizen_id', 'em_citizens.id')
                ->select('em_citizens.citizen_name')
                ->first();
            if ($ct_info) {
                $applicant_name= $ct_info->citizen_name;
            }else {
                $applicant_name= null;
            }

            $court_name =  DB::table('court')
            ->where('id', $appeal_ids_from_db_single->court_id)
            ->first()->court_name;
            
            $caseList[] = [
                'id' => $appeal_ids_from_db_single->appeal_id,
                'applicant_name' => $applicant_name,
                'appeal_status' => $appeal_ids_from_db_single->appeal_status,
                'case_no' => $appeal_ids_from_db_single->case_no,
                'manual_case_no' => $appeal_ids_from_db_single->manual_case_no,
                'court_name' => $court_name,
                'next_date' => $appeal_ids_from_db_single->next_date,
            ];
        }
        $data['caseList'] = $caseList;
        return ['total_count' => $data['totalCount'], 'all_appeals' => $data['caseList']];
     }
     public function total_pending_case_count_applicant($user, $request_data)
     {

        $page = $request_data->page;
        $limit = $request_data->limit;

        $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['SEND_TO_ASST_EM', 'SEND_TO_EM', 'SEND_TO_DM', 'SEND_TO_ASST_DM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*');
       
        
        $appeal_ids_from_db_data = $appeal_ids_from_db->paginate($limit, ['*'], 'page', $page); 
        $totalCount = count($appeal_ids_from_db->get());
        $data['totalCount'] = $totalCount;
        $caseList=null;
        foreach ($appeal_ids_from_db_data as $appeal_ids_from_db_single) {
        
            $ct_info = DB::table('users')
                ->where('users.common_login_user_id', $appeal_ids_from_db_single->created_by)
                ->join('em_citizens', 'users.citizen_id', 'em_citizens.id')
                ->select('em_citizens.citizen_name')
                ->first();
            if ($ct_info) {
                $applicant_name= $ct_info->citizen_name;
            }else {
                $applicant_name= null;
            }

            $court_name =  DB::table('court')
            ->where('id', $appeal_ids_from_db_single->court_id)
            ->first()->court_name;
            
            $caseList[] = [
                'id' => $appeal_ids_from_db_single->appeal_id,
                'applicant_name' => $applicant_name,
                'appeal_status' => $appeal_ids_from_db_single->appeal_status,
                'case_no' => $appeal_ids_from_db_single->case_no,
                'manual_case_no' => $appeal_ids_from_db_single->manual_case_no,
                'court_name' => $court_name,
                'next_date' => $appeal_ids_from_db_single->next_date,
            ];
        }
        $data['caseList'] = $caseList;
        return ['total_count' => $data['totalCount'], 'all_appeals' => $data['caseList']];
     }
     public function total_completed_case_count_applicant($user, $request_data)
     {
        $page = $request_data->page;
        $limit = $request_data->limit;

        $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['CLOSED'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*');
 
 
        $appeal_ids_from_db_data = $appeal_ids_from_db->paginate($limit, ['*'], 'page', $page); 
        $totalCount = count($appeal_ids_from_db->get());
        $data['totalCount'] = $totalCount;
        $caseList=null;
        foreach ($appeal_ids_from_db_data as $appeal_ids_from_db_single) {
        
            $ct_info = DB::table('users')
                ->where('users.common_login_user_id', $appeal_ids_from_db_single->created_by)
                ->join('em_citizens', 'users.citizen_id', 'em_citizens.id')
                ->select('em_citizens.citizen_name')
                ->first();
            if ($ct_info) {
                $applicant_name= $ct_info->citizen_name;
            }else {
                $applicant_name= null;
            }

            $court_name =  DB::table('court')
            ->where('id', $appeal_ids_from_db_single->court_id)
            ->first()->court_name;
            
            $caseList[] = [
                'id' => $appeal_ids_from_db_single->appeal_id,
                'applicant_name' => $applicant_name,
                'appeal_status' => $appeal_ids_from_db_single->appeal_status,
                'case_no' => $appeal_ids_from_db_single->case_no,
                'manual_case_no' => $appeal_ids_from_db_single->manual_case_no,
                'court_name' => $court_name,
                'next_date' => $appeal_ids_from_db_single->next_date,
            ];
        }
        $data['caseList'] = $caseList;
        return ['total_count' => $data['totalCount'], 'all_appeals' => $data['caseList']];
     }

      //citizen appeal case details
      public function appeal_case_details_apps(Request $request){
        $data = json_decode($request->body_data, true);
        $id = $data;
        try {
            $appeal = EmAppeal::findOrFail($id);
            $data = AppealRepository::getAllAppealInfo($id);
            $data['all_data']  = $data;
            $data['appeal']  = $appeal;
            $data["notes"] = $appeal->appealNotes;
            $data["appeal_id"]=$id;
            $data['page_title'] = 'মামলার বিস্তারিত তথ্য';
            
            $data['shortOrderNameList'] = PeshkarNoteRepository::get_order_list($appeal->id);


            $data['page_title'] = 'মামলা ট্র্যাকিং';
            return ['status' => true,  "data" => $data];
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
            ], 404);
        }
       
       
       
     }

     //citizen appeal traking
     public function showAppealTraking(Request $request){
        
        $data = json_decode($request->body_data, true);
        
        $id = $data;

        try {
           
            $appeal = EmAppeal::findOrFail($id);
            
            $data = AppealRepository::getAllAppealInfo($id);
            
            
            $data['appeal']  = $appeal;
            
            $data['shortOrderNameList'] = PeshkarNoteRepository::get_order_list($appeal->id);


            $data['page_title'] = 'মামলা ট্র্যাকিং';
            return ['status' => true,  "data" => $data];
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
            ], 404);
        }
       
       
     }

     
}
