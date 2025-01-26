<?php

namespace App\Http\Controllers;

use App\Models\EmAppeal;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmcRegisterApiController extends Controller
{
    public function index(Request $request)
    {
        $requestData = $request->all();
        $allInfo = $requestData['body_data'];
        $results = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->orderby('id', 'desc');

        if (!empty($allInfo['dateFrom'])  && !empty($allInfo['dateTo'])) {
            // // dd(1);
            // $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_start'])));
            // $dateTo =  date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_end'])));
            $results  = $results->whereBetween('case_date', [$allInfo['dateFrom'], $allInfo['dateTo']]);
        }
        if (!empty($allInfo['case_no'])) {
            $results = $results->where('case_no', '=', $allInfo['case_no']);
        }
        if (!empty($allInfo['caseStatus'])) {
            if ($allInfo['caseStatus'] == 'ON_TRIAL') {
                $caseStatus_array = ['ON_TRIAL', 'ON_TRIAL_DM'];
            } elseif ($allInfo['caseStatus'] == 'CLOSED') {
                $caseStatus_array = ['CLOSED'];
            }
            $results = $results->whereIN('appeal_status', $caseStatus_array);
        }
        $results = $results->get();

        foreach ($results as $key => $result) {
            $applicantcitizen_name = DB::table('em_appeals')
                ->join('em_appeal_citizens', 'em_appeals.id', '=', 'em_appeal_citizens.appeal_id')
                ->join('em_citizens', 'em_citizens.id', '=', 'em_appeal_citizens.citizen_id')
                ->where('em_appeals.id', '=', $result->id)
                ->select('em_citizens.citizen_name')
                ->first();
            if ($applicantcitizen_name) {
                $result['applicant_citizen_name'] = $applicantcitizen_name->citizen_name;
            } else {
                $result['applicant_citizen_name'] = null;
            }
            $court_name = DB::table('court')
                ->where('id', $result->court_id)
                ->first()->court_name;
            $result['court_name'] = $court_name;
        }

        $page_title  = 'emc- রেজিস্টার ';
        return ['success' => true, 'page_title' => $page_title, "data" => $results, 'ess' => $allInfo];
    }
} 