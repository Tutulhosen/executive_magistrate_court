<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\EmAppeal;
use App\Repositories\AppealRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\LogManagementRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\BaseController as BaseController;

class InvestigationApiController extends BaseController
{
    public function investigator_verify_form_submit(Request $request)
    {
        $bodyData = json_decode($request->input('body_data'), true);

        $appeal_id = DB::table('em_appeals')
            ->select('id')
            ->where('investigation_tracking_code', '=', $bodyData['investigation_tracking_code'])
            ->where('case_no', '=', $bodyData['case_no'])
            ->first();
        if (empty($appeal_id)) {
            return ['status' => 'failed'];
        }
        $investigator = DB::table('em_investigators')
            ->where('appeal_id', '=', $appeal_id->id)
            ->where('mobile', '=', $bodyData['mobile_number'])
            ->orderBy('id', 'desc')
            ->first();
        if (empty($investigator)) {
            return ['status' => 'failed'];
        } else {
            $data = AppealRepository::getAllAppealInfoInvestigator($appeal_id->id);
            $data['division_info'] =  $data['appeal']->division;
            $data['district_info'] = $data['appeal']->district;
            $data['upazila_info'] = $data['appeal']->upazila;
            $data['law_section_info'] = DB::table('crpc_sections')->leftJoin('crpc_section_details', 'crpc_section_details.crpc_id', '=', 'crpc_sections.crpc_id')->select('crpc_section_details.crpc_details', 'crpc_sections.crpc_name')->where('crpc_sections.id', '=', $data['appeal']->law_section)->first();

            return ['status' => "success", 'investigate_data' => $data, 'appeal_id' => $appeal_id, 'investigator' => $investigator];
        }
    }

    public function sumbitFromData_old(Request $request)
    {
        $bodyData = json_decode($request->input('body_data'), true);
        $investigator_mobile = $bodyData['investigator_mobile'];
        $investigation_attachment = $bodyData['investigation_attachment'];
        $investigation_attachment_main = $bodyData['investigation_attachment_main'];
      
        unset($bodyData['investigator_mobile']);

        // $inserted_id = DB::table('em_investigation_report')->insertGetId($bodyData);
        $inserted_id=151;

        if ($bodyData['investigation_attachment_main']) {
            $captions_main_investigation_report = 'তদন্ত প্রতিবেদন ' . $investigator_mobile . ' ' . $bodyData['investigator_name'] . ' ' . date('Y-m-d');
            // return ['msf' => json_decode($bodyData['investigation_attachment_main'])];
            $result = AttachmentRepository::storeInvestirationMainAttachment('Investrigation', $bodyData['appeal_id'], $captions_main_investigation_report, json_decode($bodyData['investigation_attachment_main']));
       
        }
        // return [$bodyData, $inserted_id];
        $investigator = DB::table('em_investigators')
            ->where('appeal_id', '=', $bodyData['appeal_id'])
            ->where('id', '=', $bodyData['investigator_id'])
            ->orderBy('id', 'desc')
            ->first();

        $image = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
        $document = array(".doc", ".docx");
        $pdf = array(".pdf");
        $excel = array(".xlsx", ".xlsm", ".xltx", ".xltm");
        $text = array(".txt");
        $i = 0;

        //$_FILES['file_name']["name"] 
        if (isset($bodyData['others_files'])) {
            foreach ($bodyData['others_files'] as $key => $file) {


                $tmp_name = $file["tmp_name"][$key];

                $fileName = $file["name"][$key];

                $captions_others_investigation_report = 'তদন্ত প্রতিবেদনের অন্যান্য ' . $investigator_mobile . ' ' . $bodyData['investigator_name'] . ' ' . date('Y-m-d');
                $fileCategory = $captions_others_investigation_report . ' ' . $file[$key];

                // dd($tmp_name.$fileName.$fileCategory);

                if ($fileName != "" && $fileCategory != null) {
                    $fileName = strtolower($fileName);
                    $fileExtension = '.' . pathinfo($fileName, PATHINFO_EXTENSION);

                    $fileContentType = "";
                    if (in_array($fileExtension, $image)) {
                        $fileContentType = 'IMAGE';
                    }
                    if (in_array($fileExtension, $document)) {
                        $fileContentType = 'DOCUMENT';
                    }
                    if (in_array($fileExtension, $pdf)) {
                        $fileContentType = 'PDF';
                    }
                    if (in_array($fileExtension, $excel)) {
                        $fileContentType = 'EXCEL';
                    }
                    if (in_array($fileExtension, $text)) {
                        $fileContentType = 'TEXT';
                    }

                    $fileName = AttachmentRepository::getGUID() . $fileExtension;
                    if ($fileContentType != "") {
                        $appealYear = 'APPEAL - ' . date('Y');
                        $appealID = 'AppealID - ' . $bodyData['appeal_id'];


                        $attachmentUrl = config('app.attachmentUrl');
                        $appName = 'Investrigation';
                        $filePath = $attachmentUrl . $appName . '/' . $appealYear  . '/' . $appealID . '/';
                        // dd($filePath);
                        if (!is_dir($filePath)) {
                            mkdir($filePath, 0777, true);
                        }

                        move_uploaded_file($tmp_name, $filePath . $fileName);
                        $file_in_log = [

                            // 'file_id'=>$attachment->id,
                            'file_category' => $fileCategory,
                            'file_name' => $fileName,
                            'file_path' => $appName . '/' . $appealYear . '/' . $appealID . '/'
                        ];
                    }

                    $data = array(
                        'investigator_id' => $request->investigator_id,
                        'investigator_report_id' => $inserted_id,
                        'single_comment' => $request->single_comment[$key],
                        'file_text' => $request->file_type[$key],
                        'investigation_attachment' => json_encode($file_in_log)
                    );
                    //  tt($data);
                    // dd($log_file_data );
                    DB::table('em_investigation_report_details')->insert($data);
                }
                return ['message' => $bodyData, 'id' => $inserted_id];
            }
        }

        if ($inserted_id) {
            $data = ['investigation_report_is_submitted' => 1];

            DB::table('em_appeals')
                ->where('id', $bodyData['appeal_id'])
                ->update($data);
            LogManagementRepository::investigationreportsubmit($bodyData, $investigator);
            return ['status' => "success"];
        }
    }

    public function sumbitFromData(Request $request)
    {
        $bodyData = json_decode($request->input('body_data'), true);
        $investigator_mobile = $bodyData['investigator_mobile'];
        $appeal_id = $bodyData['appeal_id'];
        $investigator_id = $bodyData['investigator_id'];
        $investigation_subject = $bodyData['investigation_subject'];
        $investigator_name = $bodyData['investigator_name'];
        $case_no = $bodyData['case_no'];
        $memorial_no = $bodyData['memorial_no'];
        $investigation_comments = $bodyData['investigation_comments'];
        $investigation_date = $bodyData['investigation_date'];
        $investigation_attachment = $bodyData['investigation_attachment'];
        $investigation_attachment_main = $bodyData['investigation_attachment_main'];
    
        $log_file_data = null;
        $log_file_data_main = null;

        $investigator_details = DB::table('em_investigators')
            ->where('id', '=', $investigator_id)
            ->first();
     

        if ($investigation_attachment_main) {
            $captions_main_investigation_report='তদন্ত প্রতিবেদন প্রধান রিপোর্ট '.$investigator_details->mobile.' '.$investigator_details->name.' '.date('Y-m-d');
       
            $log_file_data_main = AttachmentRepository::storeInvestirationMainAttachment_new('Investrigation', $appeal_id,$captions_main_investigation_report, $investigation_attachment_main);
        }
        // return $log_file_data_main;
        if ($investigation_attachment) {
            $captions_others_investigation_report='তদন্ত প্রতিবেদন অন্যান্য '.$investigator_details->mobile.' '.$investigator_details->name.' '.date('Y-m-d');
            $log_file_data = AttachmentRepository::storeInvestirationMainAttachment_new('others', $appeal_id, $request->file_type,$investigation_attachment);
        }

        // return $log_file_data;
        
        $investigator_details_array = [
            'id' => $investigator_details->id,
            'appeal_id' => $investigator_details->appeal_id,
            'type_id' => $investigator_details->type_id,
            'nothi_id' => $investigator_details->nothi_id,
            'name' => $investigator_details->name,
            'organization' => $investigator_details->organization,
            'designation' => $investigator_details->designation,
            'mobile' => $investigator_details->mobile,
            'email' => $investigator_details->email,
        ];

        

        $insert_data = [
            'appeal_id' => $appeal_id,
            'investigator_id'=>$investigator_id,
            'investigator_name' => $investigator_name,
            'investigator_organization' => $investigator_details->organization,
            'investigation_subject' => $investigation_subject,
            'case_no' => $case_no,
            'memorial_no' => $memorial_no,
            'investigation_comments' => $investigation_comments,
            'investigation_date' => $investigation_date,
            'investigation_attachment' => $log_file_data,
            'investigation_attachment_main' => $log_file_data_main,
        ];
 
        $inserted = DB::table('em_investigation_report')->insert($insert_data);

        if ($inserted) {
            $data = ['investigation_report_is_submitted' => 1];

            DB::table('em_appeals')
                ->where('id', $request->appeal_id)
                ->update($data);
            LogManagementRepository::investigationreportsubmit($insert_data,$investigator_details_array );
           
            return $this->sendResponse(null, null);
        }else {
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function investigation_delete(Request $request)
    {
        $origin_id = explode('_', $request->id);


        $investigation_report = DB::table('em_investigation_report')
            ->where('id', '=', end($origin_id))
            ->first();

        $investigator_details = DB::table('em_investigators')
            ->where('id', '=', $investigation_report->investigator_id)->first();

        // var_dump($investigator_details);
        // exit();

        $investigator_details_array = [
            'id' => $investigator_details->id,
            'appeal_id' => $investigator_details->appeal_id,
            'type_id' => $investigator_details->type_id,
            'nothi_id' => $investigator_details->nothi_id,
            'name' => $investigator_details->name,
            'organization' => $investigator_details->organization,
            'designation' => $investigator_details->designation,
            'mobile' => $investigator_details->mobile,
            'email' => $investigator_details->email,
        ];



        $investigation_report_array = [
            'appeal_id' => $investigation_report->appeal_id,
            'investigator_name' => $investigation_report->investigator_name,
            'investigator_organization' => $investigation_report->investigator_organization,
            'investigation_subject' => $investigation_report->investigation_subject,
            'case_no' => $investigation_report->case_no,
            'memorial_no' => $investigation_report->memorial_no,
            'investigation_comments' => $investigation_report->investigation_comments,
            'investigation_date' => $investigation_report->investigation_date,
            'investigation_attachment_delete' => json_decode($investigation_report->investigation_attachment),
            'investigation_attachment_main_delete' => json_decode($investigation_report->investigation_attachment_main),

        ];

        LogManagementRepository::investigationreportDelete($investigation_report_array, $investigator_details_array, $investigation_report->appeal_id);
        // var_dump($investigation_report_array);
        // exit();

        //dd($investigation_report->investigation_attachment);

        if (!empty($investigation_report->investigation_attachment)) {
            $investigation_attachment_others_files = json_decode($investigation_report->investigation_attachment);
            foreach ($investigation_attachment_others_files as $value) {

                $fileName = $value->file_name;

                $attachmentUrl = config('app.attachmentUrl');
                $filePath = $attachmentUrl . $value->file_path;

                unlink($filePath . $fileName);
            }
        }

        if (!empty($investigation_report->investigation_attachment_main)) {
            $investigation_attachment_main_files = json_decode($investigation_report->investigation_attachment_main);
            foreach ($investigation_attachment_main_files as $value) {
                $fileName = $value->file_name;

                $attachmentUrl = config('app.attachmentUrl');
                $filePath = $attachmentUrl . $value->file_path;

                unlink($filePath . $fileName);
            }
        }

        $investigation_deleted = DB::table('em_investigation_report')
            ->where('id', '=', end($origin_id))
            ->delete();

        if ($investigation_deleted) {
            return response()->json([
                'success' => 'success',
            ]);
        }
    }
    public function investigation_approve(Request $request)
    {
        $origin_id = explode('_', $request->id);
        $data = ['is_investigation_report_accepted' => 1];
        //em_investigation_report
        $investigation_report = DB::table('em_investigation_report')
            ->where('id', '=', end($origin_id))
            ->first();

        // var_dump($investigation_report);
        // exit();
        $investigator_details = DB::table('em_investigators')
            ->where('id', '=', $investigation_report->investigator_id)->first();

        $investigator_details_array = [
            'id' => $investigator_details->id,
            'appeal_id' => $investigator_details->appeal_id,
            'type_id' => $investigator_details->type_id,
            'nothi_id' => $investigator_details->nothi_id,
            'name' => $investigator_details->name,
            'organization' => $investigator_details->organization,
            'designation' => $investigator_details->designation,
            'mobile' => $investigator_details->mobile,
            'email' => $investigator_details->email,
        ];

        
        $investigation_report_array = [
            'appeal_id' => $investigation_report->appeal_id,
            'investigator_name' => $investigation_report->investigator_name,
            'investigator_organization' => $investigation_report->investigator_organization,
            'investigation_subject' => $investigation_report->investigation_subject,
            'case_no' => $investigation_report->case_no,
            'memorial_no' => $investigation_report->memorial_no,
            'investigation_comments' => $investigation_report->investigation_comments,
            'investigation_date' => $investigation_report->investigation_date,
            'investigation_attachment' => json_decode($investigation_report->investigation_attachment),
            'investigation_attachment_main' => json_decode($investigation_report->investigation_attachment_main),

        ];


        LogManagementRepository::investigationreportApprove($investigation_report_array, $investigator_details_array, $investigation_report->appeal_id);


        $updated = DB::table('em_investigation_report')
            ->where('id', end($origin_id))
            ->update($data);

        if ($updated) {
            return response()->json([
                'success' => 'success',
            ]);
        }
    }
}
