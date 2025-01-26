<?php

namespace App\Http\Controllers\EmcApi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Nette\Utils\Json;
use App\Http\Controllers\API\BaseController as BaseController;

class EmcReportController extends BaseController
{
    //

    public function pdf_generate(Request $request)
    {
        
         $body_data = $request->all();
         $date_start= $body_data['date_start'];
         $date_end  = $body_data['date_end'];
         $division  = $body_data['division'];
         $district  = $body_data['district'];
         
         $btnsubmit = $body_data['btnsubmit'];
         $role = $body_data["role"];
         $div_name = $body_data['div_name'];
         $division_id = $body_data['division_id'];
   
         

        if ( $btnsubmit == "pdf_num_division") {
        
            $data['page_title'] = 'বিভাগ ভিত্তিক রিপোর্ট'; //exit;
            $data['report_name'] = 'বিভাগ ভিত্তিক';

            $data['divisions'] = DB::table('division')->select('id', 'division_name_bn')->get();
            $data['date_start'] = $date_start;
            $data['date_end'] =  $date_end;

            foreach ($data['divisions'] as $key => $value) {
                $data['results'][$key]['id'] = $value->id;
                $data['results'][$key]['division_name_bn'] = $value->division_name_bn;
                // $data['results'][$key]['num'] = $this->case_count_by_division($value->id, $data)->count();
                $data['results'][$key]['on_trial_em'] = $this->case_count_status_by_division('ON_TRIAL', $value->id, $data);
                $data['results'][$key]['on_trial_dm'] = $this->case_count_status_by_division('ON_TRIAL_DM', $value->id, $data);
                $data['results'][$key]['send_to_em'] = $this->case_count_status_by_division('SEND_TO_EM', $value->id, $data);
                $data['results'][$key]['send_to_adm'] = $this->case_count_status_by_division('SEND_TO_ADM', $value->id, $data);
                $data['results'][$key]['colsed'] = $this->case_count_status_by_division('CLOSED', $value->id, $data);
                $data['results'][$key]['rejected'] = $this->case_count_status_by_division('REJECTED', $value->id, $data);
            }

           return  $this->sendResponse($data, 'success');
        
        }

        // District Number
        if ($btnsubmit == "pdf_num_district") {
            $data['page_title'] = 'জেলা ভিত্তিক রিপোর্ট';
            $data['report_name'] = 'জেলা ভিত্তিক';
       

            $data['date_start'] = $date_start;
            $data['date_end'] = $date_end;
            $data['division'] = DB::table('division')->find($division);
            // Get Division
            if ($role==34) {
                $data['district_list'] = DB::table('district')->select('id', 'district_name_bn')->where('division_id', $division_id)->get();
                $data['div_name'] = $div_name;
            } else {
                $data['district_list'] = DB::table('district')->select('id', 'district_name_bn')->where('division_id', $division)->get();
                $div_data = DB::table('division')->where('id', $division)->first();
                $data['div_name'] = $div_data->division_name_bn;
            }
            
            

            


            foreach ($data['district_list'] as $key => $value) {
                $data['results'][$key]['id'] = $value->id;
                $data['results'][$key]['district_name_bn'] = $value->district_name_bn;
                // $data['results'][$key]['num'] = $this->case_count_by_division($value->id, $data)->count();
                $data['results'][$key]['on_trial_em'] = $this->case_count_status_by_district('ON_TRIAL', $value->id, $data);
                $data['results'][$key]['on_trial_dm'] = $this->case_count_status_by_district('ON_TRIAL_DM', $value->id, $data);
                $data['results'][$key]['send_to_em'] = $this->case_count_status_by_district('SEND_TO_EM', $value->id, $data);
                $data['results'][$key]['send_to_adm'] = $this->case_count_status_by_district('SEND_TO_ADM', $value->id, $data);
                $data['results'][$key]['colsed'] = $this->case_count_status_by_district('CLOSED', $value->id, $data);
                $data['results'][$key]['rejected'] = $this->case_count_status_by_district('REJECTED', $value->id, $data);
            }

            return  $this->sendResponse($data, 'success');
 
        }

        if ($btnsubmit == "pdf_num_upazila") {
            $data['page_title'] = 'উপজেলা ভিত্তিক রিপোর্ট'; //exit;
            $data['report_name'] = 'উপজেলা ভিত্তিক'; //exit;
             
            $data['date_start'] = $date_start;
            $data['date_end'] = $date_end;
           
            // Get Division
           
            $data['upazila_list'] = DB::table('upazila')->select('id', 'district_id', 'upazila_name_bn')->where('district_id', $district)->get();
            $data['division'] = DB::table('division')->find($division);
            $data['district'] = DB::table('district')->find($district);
            if ($role==34) {
                $div_data = DB::table('division')->where('id', $division_id)->first();
                $data['div_data'] = $div_data->division_name_bn;
                $data['dis_data'] = $data['district']->district_name_bn;
            } else {
                $data['div_data'] = $data['division']->division_name_bn;
                $data['dis_data'] = $data['district']->district_name_bn;
            }
            
            
            
            foreach ($data['upazila_list'] as $key => $value) {
                $data['results'][$key]['id'] = $value->id;
                $data['results'][$key]['district_name_bn'] = $value->upazila_name_bn;
                // $data['results'][$key]['num'] = $this->case_count_by_division($value->id, $data)->count();
                $data['results'][$key]['on_trial_em'] = $this->case_count_status_by_upazila('ON_TRIAL', $value->id, $data);
                $data['results'][$key]['on_trial_dm'] = $this->case_count_status_by_upazila('ON_TRIAL_DM', $value->id, $data);
                $data['results'][$key]['send_to_em'] = $this->case_count_status_by_upazila('SEND_TO_EM', $value->id, $data);
                $data['results'][$key]['send_to_adm'] = $this->case_count_status_by_upazila('SEND_TO_ADM', $value->id, $data);
                $data['results'][$key]['colsed'] = $this->case_count_status_by_upazila('CLOSED', $value->id, $data);
                $data['results'][$key]['rejected'] = $this->case_count_status_by_upazila('REJECTED', $value->id, $data);
            }

            return  $this->sendResponse($data, 'success');

        }

        if ($btnsubmit  == "pdf_crpc_division") {
            $data['page_title'] = 'বিভাগ ভিত্তিক রিপোর্ট'; //exit;
            $data['report_name'] = 'বিভাগ ভিত্তিক';

            // Get Division
            
            $data['divisions'] = DB::table('division')->select('id', 'division_name_bn')->get();
 


            $data['crpc'] = DB::table('crpc_sections')->select('id', 'crpc_id', 'crpc_name')->get();

            //dd ($data['crpc']);

            $data['date_start'] = $date_start;
            $data['date_end'] = $date_end;
   
            foreach ($data['divisions'] as $key => $value) {
                $data['results'][$key]['id'] = $value->id;
                $data['results'][$key]['division_name_bn'] = $value->division_name_bn;
               
                foreach ($data['crpc'] as $crpc) {
                    $data['results'][$key][$crpc->crpc_id] = $this->case_count_status_by_crpc_division($value->id, $data, $crpc->id);
                }
            }
       
            return  $this->sendResponse($data, 'success');

        }

        if ($btnsubmit == "pdf_crpc_district") {
            $data['page_title'] = 'জেলা ভিত্তিক রিপোর্ট';
            $data['report_name'] = 'জেলা ভিত্তিক';
      

            $data['date_start'] = $date_start;
            $data['date_end'] = $date_end;

           
            // dd($request->division);->count()
            $data['crpc'] = DB::table('crpc_sections')->select('id', 'crpc_id',)->get();
            $data['division'] = DB::table('division')->find($division);

            if ($role==34) {
                $data['district_list'] = DB::table('district')->select('id', 'district_name_bn')->where('division_id', $division_id)->get();
                $data['div_name'] = $div_name;
            } else {
                $data['district_list'] = DB::table('district')->select('id', 'district_name_bn')->where('division_id', $division)->get();
                $div_data = DB::table('division')->where('id', $division)->first();
                $data['div_name'] = $div_data->division_name_bn;
            }


            foreach ($data['district_list'] as $key => $value) {
                $data['results'][$key]['id'] = $value->id;
                $data['results'][$key]['district_name_bn'] = $value->district_name_bn;
                // $data['results'][$key]['num'] = $this->case_count_by_division($value->id, $data)->count();
                foreach ($data['crpc'] as $crpc) {
                    $data['results'][$key][$crpc->crpc_id] = $this->case_count_status_by_crpc_district($value->id, $data, $crpc->id);
                }
            }
            return  $this->sendResponse($data, 'success');
           
        }

        if ($btnsubmit == 'pdf_crpc_upazila') {
            $data['page_title'] = 'উপজেলা ভিত্তিক রিপোর্ট'; //exit;
            $data['report_name'] = 'উপজেলা ভিত্তিক';
           
            $data['date_start'] = $date_start;
            $data['date_end'] = $date_end;

            
            $data['upazila_list'] = DB::table('upazila')->select('id', 'district_id', 'upazila_name_bn')->where('district_id', $district)->get();
            $data['division'] = DB::table('division')->find($division);
            $data['district'] = DB::table('district')->find($district);
            $data['crpc'] = DB::table('crpc_sections')->select('id', 'crpc_id',)->get();
            if ($role==34) {
                $div_data = DB::table('division')->where('id', $division_id)->first();
                $data['div_data'] = $div_data->division_name_bn;
                $data['dis_data'] = $data['district']->district_name_bn;
            } else {
                $data['div_data'] = $data['division']->division_name_bn;
                $data['dis_data'] = $data['district']->district_name_bn;
            }
            //dd ($data['crpc']);

            foreach ($data['upazila_list'] as $key => $value) {
                $data['results'][$key]['id'] = $value->id;
                $data['results'][$key]['upazila_name_bn'] = $value->upazila_name_bn;
                // $data['results'][$key]['num'] = $this->case_count_by_division($value->id, $data)->count();
                foreach ($data['crpc'] as $crpc) {
                    $data['results'][$key][$crpc->crpc_id] = $this->case_count_status_by_crpc_upazila($value->id, $data, $crpc->id);
                }
            }

            return  $this->sendResponse($data, 'success');
        }

        if ($btnsubmit == 'pdf_case') {
            $data['page_title'] = 'এক্সিকিউটিভ ম্যাজিস্ট্রেট আদালতের চলমান মামলার তথ্য'; //exit;
            $data['date_start'] = $date_start;
            $data['date_end'] = $date_end;
            $data['division'] = $division;
            $data['district'] =$district;
            $data['division_id'] =$division_id;
            $data['role'] =$role;
            $data['upazila'] =''; //$upazila; //upazila;


            $data['results'] = $this->case_list_filter($data);

            return  $this->sendResponse($data, 'success');
        }
       
    }     

    public function caselist()
    {
        // Dropdown List
        $data['courts'] = DB::table('court')->select('id', 'court_name')->get();
        $data['roles'] = DB::table('role')->select('id', 'role_name')->where('in_action', 1)->get();
        $data['divisions'] = DB::table('division')->select('id', 'division_name_bn')->get();

        $data['getMonth'] = date('M', mktime(0, 0, 0));

        $data['page_title'] = 'মামলার রিপোর্ট ফরম'; //exit;
        // return view('case.case_add', compact('page_title', 'case_type'));
        return view('report.caselist')->with($data);
    }
    public function case_count_status_by_district($status, $id, $data)
    {
        // dd($data);
        if (isset($data['date_start']) && isset($data['date_end'])) {

            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_start'])));
            $dateTo =  date('Y-m-d', strtotime(str_replace('/', '-', $data['date_end'])));
        } else {
            $dateFrom = 0;
            $dateTo = 0;
        }
        $query = DB::table('em_appeals')->where('district_id', $id)->where('appeal_status', $status);
        if ($dateFrom != 0 && $dateTo != 0) {
            $query->whereBetween('em_appeals.case_date', [$dateFrom, $dateTo]);
        }
        return $query->count();
        // return $query;
    }

    public function case_count_status_by_upazila($status, $id, $data)
    {
        // dd($data);
        if (isset($data['date_start']) && isset($data['date_end'])) {

            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_start'])));
            $dateTo =  date('Y-m-d', strtotime(str_replace('/', '-', $data['date_end'])));
        } else {
            $dateFrom = 0;
            $dateTo = 0;
        }
        $query = DB::table('em_appeals')->where('upazila_id', $id)->where('appeal_status', $status);
        if ($dateFrom != 0 && $dateTo != 0) {
            $query->whereBetween('em_appeals.case_date', [$dateFrom, $dateTo]);
        }
        return $query->count();
        // return $query;
    }
    public function case_count_status_by_division($status, $id, $data)
    {
        // dd($data);
        if (isset($data['date_start']) && isset($data['date_end'])) {

            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_start'])));
            $dateTo =  date('Y-m-d', strtotime(str_replace('/', '-', $data['date_end'])));
        } else {
            $dateFrom = 0;
            $dateTo = 0;
        }

        $query = DB::table('em_appeals')->where('division_id', $id)->where('appeal_status', $status);
        if ($dateFrom != 0 && $dateTo != 0) {
            // dd($dateFrom);
            $query->whereBetween('em_appeals.case_date', [$dateFrom, $dateTo]);
        }

        return $query->count();
        // return $query;
    }

    public function case_list_filter($data)
    {
        // Convert DB date formate
        $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_start'])));
        $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_end'])));

        // Query
        $query = DB::table('em_appeals')
            ->select('em_appeals.id', 'em_appeals.case_no', 'em_appeals.case_date', 'division.division_name_bn', 'district.district_name_bn', 'upazila.upazila_name_bn', 'em_appeals.case_date')
            ->join('district', 'em_appeals.district_id', '=', 'district.id')
            ->join('upazila', 'em_appeals.upazila_id', '=', 'upazila.id')
            ->join('division', 'em_appeals.division_id', '=', 'division.id')
            ->orderBy('id', 'DESC');
        // ->where('em_appeals.id', '=', 29);
        if ($dateFrom != 0 && $dateTo != 0) {
            $query->whereBetween('em_appeals.case_date', [$dateFrom, $dateTo]);
        }
        if ($data['role']==34) {
            if (!empty($data['division_id'])) {
                $query->where('em_appeals.division_id', $data['division_id']);
            }
            
        } else {
            if (!empty($data['division'])) {
                $query->where('em_appeals.division_id', $data['division']);
            }
            
        }
        
        if (!empty($data['district'])) {
            $query->where('em_appeals.district_id', $data['district']);
        }
        if (!empty($data['upazila'])) {
            $query->where('em_appeals.upazila_id', $data['upazila']);
        }
        $result = $query->get();
        // $result = $query->toSql();
        // dd($result);
        return $result;
    }


    public function generatePDF($html)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 12,
            'default_font' => 'kalpurush',
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if ($request->btnsubmit == 'pdf_division') {
            $data['page_title'] = 'বিভাগ ভিত্তিক রিপোর্ট'; //exit;
            $html = view('report.pdf_division')->with($data);
            // echo 'hello';

            $mpdf = new \Mpdf\Mpdf([
                'default_font_size' => 12,
                'default_font' => 'kalpurush',
            ]);
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
    }

    public function case_count_status_by_crpc_division($id, $data, $law_section)
    {

        if (isset($data['date_start']) && isset($data['date_end'])) {

            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_start'])));
            $dateTo =  date('Y-m-d', strtotime(str_replace('/', '-', $data['date_end'])));
        } else {
            $dateFrom = 0;
            $dateTo = 0;
        }
        $query = DB::table('em_appeals')
            ->where('division_id', $id)->where('law_section', $law_section);
        if ($dateFrom != 0 && $dateTo != 0) {
            $query->whereBetween('em_appeals.case_date', [$dateFrom, $dateTo]);
        }
        return $query->count();
    }

    public function case_count_status_by_crpc_district($id, $data, $law_section)
    {
        if (isset($data['date_start']) && isset($data['date_end'])) {

            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_start'])));
            $dateTo =  date('Y-m-d', strtotime(str_replace('/', '-', $data['date_end'])));
        } else {
            $dateFrom = 0;
            $dateTo = 0;
        }
        $query = DB::table('em_appeals')
            ->where('district_id', $id)->where('law_section', $law_section);
        if ($dateFrom != 0 && $dateTo != 0) {
            $query->whereBetween('em_appeals.case_date', [$dateFrom, $dateTo]);
        }

        return $query->count();
    }
    public function case_count_status_by_crpc_upazila($id, $data, $law_section)
    {
        if (isset($data['date_start']) && isset($data['date_end'])) {

            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $data['date_start'])));
            $dateTo =  date('Y-m-d', strtotime(str_replace('/', '-', $data['date_end'])));
        } else {
            $dateFrom = 0;
            $dateTo = 0;
        }
        $query = DB::table('em_appeals')
            ->where('upazila_id', $id)->where('law_section', $law_section);
        if ($dateFrom != 0 && $dateTo != 0) {
            $query->whereBetween('em_appeals.case_date', [$dateFrom, $dateTo]);
        }

        return $query->count();
    }
}
