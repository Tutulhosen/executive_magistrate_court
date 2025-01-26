<?php

namespace App\Http\Controllers\EmcApi;

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
use App\Http\Controllers\API\BaseController as BaseController;

class GetDataController extends BaseController
{

    //store citizen appeal
    public function storeCitizenAppeal(Request $request)
    {

        $data = json_decode($request->aaa, true);
        
        $appealInfo=$data['appealinfo'];
        $user_info=$data['citizeninfo']['auth_user_and_necessary_data'];
        // dd($user_info);
        DB::beginTransaction();
        try {

            $appealId = AppealRepository::storeAppeal($appealInfo);

            CitizenRepository::storeCitizen($data['citizeninfo'], $appealId);
            $attach_file = $data['log_file_data'];

            if ($attach_file) {

                $log_file_data = AttachmentRepository::storeReqAttachment($attach_file, $appealId, $user_info);
            } else {
                $log_file_data = null;
            }

            LogManagementRepository::citizen_appeal_store($data['appealinfo'], $data['citizeninfo'], $appealId, $log_file_data);
            // dd('come');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            $flag = 'false';
            return redirect()
                ->back()
                ->with('error', 'দুঃখিত! তথ্য সংরক্ষণ করা হয়নি ');
        }

        return response()->json([
            'success'    =>true,
            'message'   =>'তথ্য সফলভাবে সংরক্ষণ করা হয়েছে.'
        ]);
       
    }



     //count org rep dashboard data
     public function emc_citizen_dashboard_data(Request $request)
     {
         
         $user = json_decode($request->user_data);
         $total_case_count_citizen=CitizenCaseCountRepository::total_case_count_citizen_new($user);
         $data['total_pending_case_count_applicant'] = $this->total_pending_case_count_applicant($user);
         $data['total_case_count_applicant'] = $this->total_case_count_applicant($user);
         $data['total_running_case_count_applicant'] = $this->total_running_case_count_applicant($user);
         $data['total_completed_case_count_applicant'] = $this->total_completed_case_count_applicant($user);

         $data['total_pending_appeal_case_count_applicant'] = $this->total_pending_appeal_case_count_applicant($user);
         $data['total_appeal_case_count_applicant'] = $this->total_appeal_case_count_applicant($user);
         $data['total_running_appeal_case_count_applicant'] = $this->total_running_appeal_case_count_applicant($user);
         $data['total_completed_appeal_case_count_applicant'] = $this->total_completed_appeal_case_count_applicant($user);
         
         $appeal = EmAppeal::whereIn('id', $total_case_count_citizen['appeal_id_array'])->limit(10)->get();
         if (!empty($total_case_count_citizen['appeal_id_array'])) {
            foreach ($appeal as $key => $value) {
                $citizen_info=AppealRepository::getCauselistCitizen($value->id);
                $notes=PeshkarNoteRepository::get_last_order_list($value->id);
                if(isset($citizen_info) && !empty($citizen_info))
                {
                    $citizen_info=$citizen_info;
                }
                else
                {
                    $citizen_info=null;
                }
                if(isset($notes) && !empty($notes))
                {
                    $notes=$notes;
                }
                else
                {
                    $notes=null;
                }
             
                $data['appeal'][$key]['citizen_info'] = $citizen_info;
                $data['appeal'][$key]['notes'] =$notes; 
                // $data["notes"] = $value->appealNotes;
            }
        } else {
          
            $data['appeal']['citizen_info'] = '';
            $data['appeal']['notes'] = '';
        }
        
         return $data;
     }
 
 
 
     public function total_case_count_applicant($user)
     {
        //  $appeal_ids_from_db = DB::table('em_appeals')
        //      ->where('created_by', $user->user_id)
        //      ->get();
         // $appeal_id_array = [];
         $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['CLOSED', 'ON_TRIAL', 'ON_TRIAL_DM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
            ->get();
            
         $count = 0;
         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
             
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }
     public function total_running_case_count_applicant($user)
     {
         
        $appeal_ids_from_db = DB::table('em_appeal_citizens')
             ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
             ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            //  ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
             ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
             ->whereIn('em_appeals.appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])
             ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
             ->get();
         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
             
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             // $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }
     public function total_pending_case_count_applicant($user)
     {

        $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['SEND_TO_ASST_EM', 'SEND_TO_EM', 'SEND_TO_DM', 'SEND_TO_ASST_DM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
            ->get();

         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
             
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             // $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }
     public function total_completed_case_count_applicant($user)
     {

        $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['CLOSED'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
            ->get();
 
         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
            
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             // $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }

     public function total_appeal_case_count_applicant($user)
     {

         $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['CLOSED_ADM', 'ON_TRIAL_ADM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
            ->get();
            
         $count = 0;
         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
             
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }
     public function total_running_appeal_case_count_applicant($user)
     {
         
        $appeal_ids_from_db = DB::table('em_appeal_citizens')
             ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
             ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            //  ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
             ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
             ->whereIn('em_appeals.appeal_status', ['ON_TRIAL_ADM'])
             ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
             ->get();
         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
             
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             // $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }
     public function total_pending_appeal_case_count_applicant($user)
     {

        $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', [ 'SEND_TO_ADM', 'SEND_TO_ASST_ADM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
            ->get();

         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
             
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             // $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }
     public function total_completed_appeal_case_count_applicant($user)
     {

        $appeal_ids_from_db = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user->username)
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['CLOSED_ADM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
            ->get();
 
         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
            
 
             $appeal_ids_from_db_single->citizen_id =  $appeal_ids_from_db_single ? $appeal_ids_from_db_single->citizen_id : null;
             // $count++;
         }
 
         return ['total_count' => count($appeal_ids_from_db), 'all_appeals' => $appeal_ids_from_db];
     }

     
     public function total_case_count_defaulter($citizen_nid)
     {
 
         $appeal_ids_from_db = DB::table('gcc_appeal_citizens')
             ->join('gcc_appeals', 'gcc_appeal_citizens.appeal_id', 'gcc_appeals.id')
             ->where('gcc_citizens.citizen_NID', '=', $citizen_nid)
             ->whereIn('gcc_appeal_citizens.citizen_type_id', [2])
             ->whereIn('gcc_appeals.appeal_status', ['CLOSED', 'ON_TRIAL', 'ON_TRIAL_DC', 'ON_TRIAL_LAB_CM', 'ON_TRIAL_DIV_COM'])
             ->select('gcc_appeal_citizens.appeal_id')
             ->get();
 
         $appeal_id_array = [];
         $count = 0;
         foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
             array_push($appeal_id_array, $appeal_ids_from_db_single->appeal_id);
             $count++;
         }
 
         return ['total_count' => $count, 'appeal_id_array' => $appeal_id_array];
     }
     public function total_running_case_count_defaulter($citizen_nid)
     {
 
         $appeal_ids_from_db = DB::table('gcc_appeal_citizens')
             ->join('gcc_citizens', 'gcc_citizens.id', '=', 'gcc_appeal_citizens.citizen_id')
             ->join('gcc_appeals', 'gcc_appeal_citizens.appeal_id', 'gcc_appeals.id')
             ->where('gcc_citizens.citizen_NID', '=', $citizen_nid)
             ->whereIn('gcc_appeal_citizens.citizen_type_id', [2])
             ->whereIn('gcc_appeals.appeal_status', ['ON_TRIAL', 'ON_TRIAL_DC', 'ON_TRIAL_LAB_CM', 'ON_TRIAL_DIV_COM'])
             ->select('gcc_appeal_citizens.appeal_id')
             ->get();
 
         return ['total_count' => count($appeal_ids_from_db), 'appeal_id_array' => ''];
     }
     public function total_pending_case_count_defaulter($user)
     {
 
         $appeal_ids_from_db = DB::table('gcc_appeals')
             ->where('organization_routing_number', $user->organization_id)
             ->whereIn('appeal_status', ['SEND_TO_ASST_GCO', 'SEND_TO_GCO'])
             ->get();
 
         return ['total_count' => count($appeal_ids_from_db), 'appeal_id_array' => ''];
     }
     public function total_completed_case_count_defaulter()
     {
 
         $appeal_ids_from_db = DB::table('gcc_appeal_citizens')
             ->join('gcc_citizens', 'gcc_citizens.id', '=', 'gcc_appeal_citizens.citizen_id')
             ->join('gcc_appeals', 'gcc_appeal_citizens.appeal_id', 'gcc_appeals.id')
             ->where('gcc_citizens.citizen_NID', '=', globalUserInfo()->citizen_nid)
             ->whereIn('gcc_appeal_citizens.citizen_type_id', [2, 5])
             ->whereIn('gcc_appeals.appeal_status', ['CLOSED'])
             ->select('gcc_appeal_citizens.appeal_id')
             ->get();
 
         // $appeal_id_array=[];
         // $count=0;
         // foreach ($appeal_ids_from_db as $appeal_ids_from_db_single) {
         //     array_push($appeal_id_array, $appeal_ids_from_db_single->appeal_id);
         //     /;
         // }
 
         return ['total_count' => count($appeal_ids_from_db), 'appeal_id_array' => ''];
     }


     //citizen appeal traking
     public function showAppealTraking(Request $request){
        $data = json_decode($request->body_data, true);
        
        $id = $data;
        $appeal = EmAppeal::findOrFail($id);
        
        $data = AppealRepository::getAllAppealInfo($id);
        
        
        $data['appeal']  = $appeal;

        // return $data;
        //$data["notes"] = $appeal->appealNotes;
        //$data["districtId"]= $officeInfo->district_id;
        //$data["divisionId"]=$officeInfo->division_id;
        //$data["office_id"] = $office_id;
        //$data["gcoList"] = User::where('office_id', $user->office_id)->where('id', '!=', $user->id)->get();
        
        $data['shortOrderNameList'] = PeshkarNoteRepository::get_order_list($appeal->id);


        $data['page_title'] = 'মামলা ট্র্যাকিং';
        if (empty($data)) {
            return $this->sendResponse($data, 'No Found');
        } else {
            return $this->sendResponse($data, 'Data Found Success.');
        }
       
       
     }

     //citizen appeal case details
     public function appeal_case_details(Request $request){
        $data = json_decode($request->body_data, true);
        $id = $data;
        
        $appeal = EmAppeal::findOrFail($id);
        $data = AppealRepository::getAllAppealInfo($id);
        $data['all_data']  = $data;
        $data['appeal']  = $appeal;
        $data["notes"] = $appeal->appealNotes;
        $data["appeal_id"]=$id;
        $data['page_title'] = 'মামলার বিস্তারিত তথ্য';
        
        $data['shortOrderNameList'] = PeshkarNoteRepository::get_order_list($appeal->id);


        $data['page_title'] = 'মামলা ট্র্যাকিং';
        if (empty($data)) {
            return $this->sendResponse($data, 'No Found');
        } else {
            return $this->sendResponse($data, 'Data Found Success.');
        }
       
       
     }

     //citizen appeal case 
     public function case_for_appeal(Request $request){
        
        $data = json_decode($request->body_data, true);
        $id = $data['id'];
        $user = $data['user'];
        
        EmAppeal::where('id', $id)->update([
            'appeal_status' => 'SEND_TO_ASST_ADM',
        ]);

        $all_appeal_list = DB::table('em_appeal_citizens')
            ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
            ->join('em_appeals', 'em_appeal_citizens.appeal_id', 'em_appeals.id')
            // ->where('em_citizens.citizen_NID', '=', $user->citizen_nid)
            ->where('em_citizens.citizen_phone_no', '=', $user['username'])
            ->whereIn('em_appeal_citizens.citizen_type_id', [1,2,5])
            ->whereIn('em_appeals.appeal_status', ['SEND_TO_ADM', 'SEND_TO_ASST_ADM'])
            ->select('em_appeal_citizens.appeal_id','em_appeal_citizens.citizen_id' ,'em_appeal_citizens.citizen_type_id as type_id','em_appeals.*')
            ->get();
        $datas['all_appeal_list']=$all_appeal_list;
            return $this->sendResponse($datas, null);
       
       
       
     }
}
