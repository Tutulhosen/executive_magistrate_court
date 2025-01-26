<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\AppealRepository;

class LogManagementApiController extends Controller
{
    public function index(Request $request)
    {
        $requestData = $request->all();
        $userInfo = $requestData['body_data'];
        $case_no=$userInfo['case_no'];

        $cases = DB::table('em_appeals');
        if (!empty($case_no)) {
            $cases=$cases->where('em_appeals.case_no', 'LIKE', '%'.$case_no.'%');
        };
          $cases=$cases->orderBy('id', 'DESC')
            ->join('court', 'em_appeals.court_id', '=', 'court.id')
            ->join('division', 'court.division_id', '=', 'division.id')
            ->join('district', 'court.district_id', '=', 'district.id')
            ->join('upazila', 'em_appeals.upazila_id', '=', 'upazila.id')
            ->select('em_appeals.*', 'court.court_name', 'division.division_name_bn', 'district.district_name_bn', 'upazila.upazila_name_bn')->get();
        $page_title = 'মামলা কার্যকলাপ নিরীক্ষা';

        return ['success' => true, 'page_title' => $page_title, "data" => $cases];
    }
    public function log_index_single(Request $request, $id = null)
    {

        $requestData = $request->all();

        $userInfo = $requestData['body_data'];
        $user = $userInfo['user'];
        $office_id = $userInfo['office_id'];
        $officeInfo = $userInfo['officeInfo'];
        $id = $userInfo['id'];
        $appeal = EmAppeal::findOrFail($id);
        $data = AppealRepository::getAllAppealInfo($id);
        $data['appeal']  = $appeal;
        $data["notes"] = $appeal->appealNotes;
        $data["districtId"] = $officeInfo['district_id'];
        $data["divisionId"] = $officeInfo['division_id'];
        $data["office_id"] = $office_id;
        $data["gcoList"] = User::where('office_id', $office_id)->where('id', '!=', $user['id'])->get();

        $info = DB::table('em_appeals')
            ->join('court', 'em_appeals.court_id', '=', 'court.id')
            ->join('division', 'court.division_id', '=', 'division.id')
            ->join('district', 'court.district_id', '=', 'district.id')
            ->join('upazila', 'em_appeals.upazila_id', '=', 'upazila.id')
            ->select('em_appeals.*', 'court.court_name', 'division.division_name_bn', 'district.district_name_bn', 'upazila.upazila_name_bn')
            ->where('em_appeals.id', '=',  $id)
            ->first();

        $data['info'] = $info;


        $data['page_title'] = 'মামলার কার্যকলাপ নিরীক্ষার বিস্তারিত তথ্য';
        // return $data;
        $data['apepal_id'] = encrypt($id);
        $case_details = DB::table('em_log_book')->where('appeal_id', '=', $id)->orderBy('id', 'desc')->get();
        $data['case_details'] = $case_details;
        return ['success' => true, "data" => $data];
    }
    public function create_log_pdf(Request $request, $id = null)
    {

          
        $id = decrypt($id);
        
        // $user = globalUserInfo();

       

        // $office_id = $user->office_id;
        // $roleID = $user->role_id;
        // $officeInfo = user_office_info();
        $requestData = $request->all();

        $userInfo = $requestData['body_data'];
        $user = $userInfo['user'];
        $office_id = $userInfo['office_id'];
        $officeInfo = $userInfo['officeInfo'];

        $appeal = EmAppeal::findOrFail($id);

        

        $data = AppealRepository::getAllAppealInfo($id);

        

        $data['appeal']  = $appeal;
        $data["notes"] = $appeal->appealNotes;
        $data["districtId"]= $officeInfo['district_id'];
        $data["divisionId"]=$officeInfo['division_id'];
        $data["office_id"] = $office_id;
        $data["gcoList"] = User::where('office_id', $office_id)->where('id', '!=', $user['id'])->get();
         
        $info = DB::table('em_appeals')
        ->join('court', 'em_appeals.court_id', '=', 'court.id')
        ->join('division', 'court.division_id', '=', 'division.id')
        ->join('district', 'court.district_id', '=', 'district.id')
        ->join('upazila', 'em_appeals.upazila_id', '=', 'upazila.id')
        ->select('em_appeals.*', 'court.court_name','division.division_name_bn','district.district_name_bn','upazila.upazila_name_bn')
        ->where('em_appeals.id','=',  $id)
        ->first();
         
        $data['info']=$info;

       
        $data['page_title'] = 'মামলার কার্যকলাপ নিরীক্ষার বিস্তারিত তথ্য';

        
        $case_details=DB::table('em_log_book')->where('appeal_id','=',$id)->orderBy('id','desc')->get();

        $data['case_details']=$case_details;
        

        return ['success' => true, "data" => $data];
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'err_res' => '',
            'status' => 200,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }

    public function log_details_single_by_id($id)
    {
      
        $log_details_single_by_id=DB::table('em_log_book')->where('id','=',$id)->first();
        $data['log_details_single_by_id']=$log_details_single_by_id;
        $data['page_title'] = 'মামলার বিস্তারিত তথ্য';
        return ['success' => true, "data" => $data];

    }
}