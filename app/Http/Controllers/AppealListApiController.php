<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\AppealRepository;
use App\Repositories\ArchiveRepository;
use App\Repositories\PeshkarNoteRepository;
use App\Models\EmCaseShortdecisionTemplates;
use App\Repositories\CitizenAttendanceRepository;
use App\Services\ShortOrderTemplateServiceUpdated;
use App\Http\Controllers\API\BaseController as BaseController;

class AppealListApiController extends BaseController
{
    public function closed_list(Request $request)
    {
       
        $results = EmAppeal::orderby('id', 'desc')
            ->whereIn('appeal_status', ['CLOSED'])->paginate(10);
        if (!empty($_GET['date_start']) && !empty($_GET['date_end'])) {
            // dd(1);
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_start'])));
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_end'])));
            $results = EmAppeal::orderby('id', 'desc')
                ->whereIn('appeal_status', ['CLOSED'])
                ->whereBetween('case_date', [$dateFrom, $dateTo])->paginate(10);
        }
        if (!empty($_paghi['case_no'])) {
            $results = EmAppeal::orderby('id', 'desc')
                ->whereIn('appeal_status', ['CLOSED'])
                ->where('case_no', '=', $_GET['case_no'])
                ->orWhere('manual_case_no', '=', $_GET['case_no'])->paginate(10);
        }

        foreach ($results as $key => $result) {
            $applicantcitizen_name = DB::table('em_appeals')
                ->join('em_appeal_citizens', 'em_appeals.id', '=', 'em_appeal_citizens.appeal_id')
                ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
                ->where('em_appeals.id', '=', $result->id)
                ->where('em_appeal_citizens.citizen_type_id', '=', 1)
                ->select('em_citizens.citizen_name')
                ->first()->citizen_name;
            $result['applicantcitizen_name'] = $applicantcitizen_name;
        }
        return $results;
    }

    public function closed_list_search(Request $request)
    {
        // return 'come from Emc'; 
        $data_get = $request->getContent();

        $json_data = json_decode($data_get, true);

        $datas= $json_data['body_data'];

        $date_start = $datas['date_start'];
        $date_end = $datas['date_end'];
        $case_no = $datas['case_no'];
        $results = EmAppeal::orderby('id', 'desc')
            ->whereIn('appeal_status', ['CLOSED']);
        if (!empty($date_start) && !empty($date_end)) {
            // dd(1);
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $date_start)));
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $date_end)));
            $results = $results
                ->whereBetween('case_date', [$dateFrom, $dateTo]);
        }
        if (!empty($case_no)) {
            $results = $results
                ->where('case_no', 'LIKE', '%'.$case_no.'%');
               
        }
        $results=$results->get();

        foreach ($results as $key => $result) {
            $applicantcitizen_name = DB::table('em_appeals')
                ->join('em_appeal_citizens', 'em_appeals.id', '=', 'em_appeal_citizens.appeal_id')
                ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
                ->where('em_appeals.id', '=', $result->id)
                ->where('em_appeal_citizens.citizen_type_id', '=', 1)
                ->select('em_citizens.citizen_name')
                ->first()->citizen_name;
            $result['applicantcitizen_name'] = $applicantcitizen_name;
        }
        // return $results;
        return $this->sendResponse($results, null);
    }
    public function old_closed_list()
    {
        $page_title = 'পুরাতন নিষ্পত্তিকৃত মামলা';
        $results = DB::table('archive_case')->orderby('id', 'desc')->get();
        foreach ($results as $key => $result) {
            $court_name = DB::table('court')
                ->where('id', $result->court_id)
                ->first()->court_name;
            $result->court_name = $court_name;
        } 
        return ['message' => 'success', 'page_title' => $page_title, "data" => $results];
    }

    public function old_closed_list_search(Request $request)
    {
        // return 'come from emc';
        $data_get = $request->getContent();

        $json_data = json_decode($data_get, true);

        $datas= $json_data['body_data'];

        $date_start = $datas['date_start'];
        $date_end = $datas['date_end'];
        $case_no = $datas['case_no'];

        $results = DB::table('archive_case')->orderby('id', 'desc');
      

        if (!empty($date_start) && !empty($date_end)) {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $date_start)));
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $date_end)));
            $results = $results->whereBetween('appeal_date', [$dateFrom, $dateTo]);
        }
        if (!empty($case_no)) {
            $results = $results->where('case_no', 'LIKE', '%'.$case_no.'%');
        }
        $result_get= $results->get();
        foreach ($result_get as $key => $result) {
            $court_name = DB::table('court')
                ->where('id', $result->court_id)
                ->first()->court_name;
            $result->court_name = $court_name;
        } 
   
        return $this->sendResponse($result_get, null);
    }
    
    public function showAppealViewPage(Request $request, $id = '')
    {
         
      
        $results['details'] = ArchiveRepository::old_dismiss_case_details($id);
       
        $results['crpc_name']=DB::table('crpc_sections')->where('id',$results['details']->related_act)->first()->crpc_name;
   
        $results['url']=url('/');
        $page_title = 'মামলার বিস্তারিত তথ্য ';

     
        $results['all_dis_div_upa']= DB::table('archive_case as A')->where('A.id', $results['details']->id)
                    ->join('division as B', 'A.div_id', 'B.id')
                    ->join('district as C', 'A.dis_id', 'C.id')
                    ->join('upazila as D', 'A.upa_id', 'D.id')
                    ->select('B.division_name_bn as div_name','C.district_name_bn as dis_name','D.upazila_name_bn as upa_name')
                    ->first();

        return ['message' => 'success', 'page_title' =>$page_title, "data" =>$results];

        
    }


    public function generate_pdf($id){


        $id = $id;
        $case_details = ArchiveRepository::old_dismiss_case_details($id);
        if ($case_details->id) {
            $results['attachmentList']=ArchiveRepository::old_dismiss_case_attach_file($case_details->id);
        }
        $results['url']=url('/');
      
        return ['message' => 'success',"data" =>$results];
        // $data['attachmentList']=$attachmentList;
        // $data['page_title'] = 'পুরাতন নিষ্পত্তিকৃত মামলার বিবরণ';
        // $data['case_details']=$case_details;
        // return view('archive.generate_pdf')->with($data);
    }


    public function closed_list_details(Request $request)
    {
        // return 'come from Emc'; 
        $data_get = $request->getContent();

        $json_data = json_decode($data_get, true);

        $datas= $json_data['body_data'];

        $case_id = $datas['case_id'];
        $auth_user_info = $datas['auth_user_info'];

        $appeal = EmAppeal::findOrFail($case_id);
        $data = AppealRepository::getAllAppealInfo($case_id);
        $data['appeal']  = $appeal;
        $data["notes"] = $appeal->appealNotes;
        $data["gcoList"] = User::where('office_id', $auth_user_info['office_id'])->where('id', '!=', $auth_user_info['id'])->get();
        $data["appeal_id"]=$case_id;
        $data['page_title'] = 'মামলার বিস্তারিত তথ্য';
        // return $data;
        return $this->sendResponse($data, null);
    }

    public function closed_list_case_traking(Request $request)
    {
        // return 'come from Emc'; 
        $data_get = $request->getContent();

        $json_data = json_decode($data_get, true);

        $datas= $json_data['body_data'];

        $case_id = $datas['case_id'];

        $appeal = EmAppeal::findOrFail($case_id);
        $data = AppealRepository::getAllAppealInfo($case_id);
        $data['appeal']  = $appeal;

        $data['shortOrderNameList'] = PeshkarNoteRepository::get_order_list($appeal->id);

        $data['page_title'] = 'মামলা ট্র্যাকিং';
        // return $data;
        return $this->sendResponse($data, null);
    }

    public function closed_list_case_nothiView(Request $request)
    {
        // return 'come from Emc'; 
        $data_get = $request->getContent();

        $json_data = json_decode($data_get, true);

        $datas= $json_data['body_data'];

        $case_id = $datas['case_id'];
        $auth_user_info = $datas['auth_user_info'];

        $data['caseNumber'] = EmAppeal::find($case_id)->case_no;
        $data['appealId'] = $case_id;
        $data['nothiData'] = AppealRepository::getNothiListFromAppeal($case_id);
        $data['citizenAttendanceList'] = CitizenAttendanceRepository::getCitizenAttendanceByAppealId($case_id);

        $data['shortOrderTemplateList'] = ShortOrderTemplateServiceUpdated::getShortOrderTemplateListByAppealId($case_id);

        //$paymentAttachment = PaymentService::getPaymentAttachmentByAppealId($id);
        $data['page_title']  = 'বিস্তারিত নথি | ' . $data['caseNumber'];
        // return $data;
        return $this->sendResponse($data, null);
    }

    public function closed_list_case_orderSheetDetails(Request $request)
    {
        // return 'come from Emc'; 
        $data_get = $request->getContent();

        $json_data = json_decode($data_get, true);

        $datas= $json_data['body_data'];

        $case_id = $datas['case_id'];

        $data_to_qr_codded=url()->full();
        $imageName = 'QR_'.$case_id;
        $appealId = $case_id;
        
        $data['data_image_path']='/QRCodes/'.$imageName;

        $data['appealOrderLists'] = PeshkarNoteRepository::generate_order_shit($appealId);
        $data['nothi_id'] = $case_id;
        $data['page_title'] = 'আদেশ নামা';

        // return $data;
        return $this->sendResponse($data, null);
    }

    public function closed_list_case_shortOrderSheets(Request $request)
    {
        // return 'come from Emc'; 
        $data_get = $request->getContent();

        $json_data = json_decode($data_get, true);

        $datas= $json_data['body_data'];

        $case_id = $datas['case_id'];


        $imageName = 'QR_short_decision_template'.$case_id;
        
        $data['data_image_path']='/QRCodes/'.$imageName;

        $data['appealShortOrderLists']=EmCaseShortdecisionTemplates::where('id',$case_id)->get();
        // return $data['appealShortOrderLists'];
        $data['page_title']='সংক্ষিপ্ত আদেশ';
        $data['nothi_id'] = $case_id;

        // return $data;
        return $this->sendResponse($data, null);
    }


}