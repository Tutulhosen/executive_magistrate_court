<?php

namespace App\Repositories;


use App\Models\Appeal;
use App\Models\EmAppealCitizen;
use App\Models\EmCitizen;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class CitizenRepository
{
    public static function storeCitizen($appealInfo,$appealId){
        $auth_user_and_necessary_data=$appealInfo['auth_user_and_necessary_data'];
       
        
        $citizenList=$appealInfo['citizen_data'];
     
        $i=1;
        foreach ($citizenList as $reqCitizen) {
            if ($reqCitizen['type']==1) {
                 $user_exit=User::where('common_login_user_id',$reqCitizen['created_by'])->first();
                if (!$user_exit) {
                    DB::table('users')->insert([
                        'common_login_user_id' =>$reqCitizen['created_by'],
                        'name' =>$reqCitizen['citizen_name'],
                        'username' =>$reqCitizen['citizen_phone_no'],
                        'is_citizen' =>1,
                        'citizen_type_id' =>1,
                        'mobile_no' =>$reqCitizen['citizen_phone_no'],
                        'citizen_id' =>$reqCitizen['id'],
                        'citizen_nid' =>$reqCitizen['citizen_NID'],
                        'email' =>$reqCitizen['email'],
                        'password' =>Hash::make('12345678'),
                    ]);
                }
            }
            $citizen = CitizenRepository::checkCitizenExist($reqCitizen['citizen_phone_no'],$reqCitizen['citizen_NID']);
            // dd($reqCitizen['type']);
           

            $citizen->id = $reqCitizen['id'];
            $citizen->citizen_name = $reqCitizen['citizen_name'];
            $citizen->citizen_phone_no = $reqCitizen['citizen_phone_no'];
            $citizen->citizen_NID = $reqCitizen['citizen_NID'];
            $citizen->citizen_gender = $reqCitizen['citizen_gender'];
            $citizen->father = $reqCitizen['father'];
            $citizen->mother = $reqCitizen['mother'];
            $citizen->designation = $reqCitizen['designation'];
            $citizen->organization = $reqCitizen['organization'];
            $citizen->organization_id = $reqCitizen['organization_id'];
            $citizen->present_address = $reqCitizen['present_address'];
            $citizen->email = $reqCitizen['email'];
            $citizen->thana = $reqCitizen['thana'];
            $citizen->upazilla = $reqCitizen['upazilla'];
            $citizen->age = $reqCitizen['age'];
            $citizen->created_at = date('Y-m-d H:i:s');
            $citizen->updated_at = date('Y-m-d H:i:s');
            $citizen->created_by = $reqCitizen['created_by'];
            $citizen->updated_by = $reqCitizen['updated_by'];
            // $citizen->save();
            
            if ($citizen->save()) {
                $storeId[$i] = $citizen;
                $i++;
                $transactionStatus = AppealCitizenRepository::storeAppealCitizen($citizen->id, $appealId, $reqCitizen['type'],$auth_user_and_necessary_data['auth_user_id']);
                if (!$transactionStatus) {
                    $transactionStatus = false;
                    break;
                }
            } else {
                $transactionStatus = false;
                break;
            }
            if($transactionStatus == false)
                break;
        }
      
      

        // if($auth_user_and_necessary_data['caseEntryType'] == 'own'){
        //     if($user->citizen_id != null){
        //         $userCtgId = $user->citizen_id;
        //     } else{
        //         $AuthCtg=new EmCitizen();
        //         $AuthCtg->citizen_name = $user->name;
        //         $AuthCtg->citizen_phone_no = $user->mobile_no;
        //         // $AuthCtg->citizen_NID = $user->nid;
        //         // $AuthCtg->citizen_gender = $user->gender;
        //         // $AuthCtg->father = $user->father;
        //         // $AuthCtg->mother = $user->mother;
        //         // $AuthCtg->designation = $user->designation;
        //         // $AuthCtg->organization = $user->organization;
        //         // $AuthCtg->present_address = $user->presentAddress;
        //         $AuthCtg->email = $user->email;
        //         // $AuthCtg->thana = $user->thana;
        //         // $AuthCtg->upazilla = $user->upazilla;
        //         // $AuthCtg->age = $user->age;
        //         $AuthCtg->created_at = date('Y-m-d H:i:s');
        //         $AuthCtg->updated_at = date('Y-m-d H:i:s');
        //         $AuthCtg->created_by = $user->id;
        //         $AuthCtg->updated_by = $user->id;
        //         $AuthCtg->save();
        //         $userCtgId = $AuthCtg->id;
        //     }

        //     $transactionStatus = AppealCitizenRepository::storeAppealCitizen($userCtgId, $appealId, 1);
        //     if (!$transactionStatus) {
        //         $transactionStatus = false;
        //     }
        // }

        $multiCtz=$appealInfo['multiCtz_data'];
        
        foreach($multiCtz as $nominees){
            // for ($i=0; $i<sizeof($nominees['citizen_name']); $i++) {
            //     $citizen = CitizenRepository::checkCitizenExist($nominees['id'][$i],$nominees['citizen_NID'][$i]);
       
            //     // $citizen = new EmCitizen();

            //     $citizen->id = isset($nominees['id'][$i]) ? $nominees['id'][$i] : NULL ;
            //     $citizen->citizen_name = isset($nominees['citizen_name'][$i]) ? $nominees['citizen_name'][$i] : NULL ;
            //     $citizen->citizen_phone_no = isset($nominees['citizen_phone_no'][$i]) ? $nominees['citizen_phone_no'][$i] : NULL;
            //     $citizen->citizen_NID = isset($nominees['citizen_NID'][$i]) ? $nominees['citizen_NID'][$i] : NULL;
            //     $citizen->citizen_gender = isset($nominees['citizen_gender'][$i]) ? $nominees['citizen_gender'][$i] : NULL;
            //     $citizen->father = isset($nominees['father'][$i]) ? $nominees['father'][$i] : NULL;
            //     $citizen->mother = isset($nominees['mother'][$i]) ? $nominees['mother'][$i] : NULL;
            //     // $citizen->designation = isset($nominees['designation'][$i]);
            //     // $citizen->organization = isset($nominees['organization'][$i]);
            //     $citizen->present_address = isset($nominees['present_address'][$i]) ? $nominees['present_address'][$i] : NULL;
            //     $citizen->email = isset($nominees['email'][$i]) ? $nominees['email'][$i] : NULL;
            //     $citizen->thana = isset($nominees['thana'][$i]) ? $nominees['thana'][$i] : NULL;
            //     $citizen->upazilla = isset($nominees['upazilla'][$i]) ? $nominees['upazilla'][$i] : NULL;
            //     $citizen->age = isset($nominees['age'][$i]) ? $nominees['age'][$i] : NULL;

            //     $citizen->created_at = date('Y-m-d H:i:s');
            //     $citizen->updated_at = date('Y-m-d H:i:s');
            //     // $citizen->created_by = Session::get('userInfo')->username;
            //     $citizen->created_by = isset($nominees['created_by'][$i]) ? $nominees['created_by'][$i] : NULL;
            //     // $citizen->updated_by = Session::get('userInfo')->username;
            //     $citizen->updated_by = isset($nominees['updated_by'][$i]) ? $nominees['updated_by'][$i] : NULL;


            //         // dd($citizen);
            //     if ($citizen->save()) {
            //         $storeId[$i.'1'] = $citizen;
            //         $transactionStatus = AppealCitizenRepository::storeAppealCitizen($citizen->id, $appealId, $nominees['type'][$i], $auth_user_and_necessary_data['auth_user_id']);
            //         if (!$transactionStatus) {
            //             $transactionStatus = false;
            //             break;
            //         }
            //     } else {
            //         $transactionStatus = false;
            //         break;
            //     }

            //     if($transactionStatus == false)
            //         break;

            // }

            $citizen = CitizenRepository::checkCitizenExist($nominees['citizen_phone_no'],$nominees['citizen_NID']);
            // dd($reqCitizen['type']);
            // dd($citizen);
            
            $citizen->id = $nominees['id'];
            $citizen->citizen_name = $nominees['citizen_name'];
            $citizen->citizen_phone_no = $nominees['citizen_phone_no'];
            $citizen->citizen_NID = $nominees['citizen_NID'];
            $citizen->citizen_gender = $nominees['citizen_gender'];
            $citizen->father = $nominees['father'];
            $citizen->mother = $nominees['mother'];
            $citizen->present_address = $nominees['present_address'];
            $citizen->email = $nominees['email'];
            $citizen->thana = $nominees['thana'];
            $citizen->upazilla = $nominees['upazilla'];
            $citizen->age = $nominees['age'];
            $citizen->created_at = date('Y-m-d H:i:s');
            $citizen->updated_at = date('Y-m-d H:i:s');
            $citizen->created_by = $nominees['created_by'];
            $citizen->updated_by = $nominees['updated_by'];
            // $citizen->save();
            // echo "<pre>";
            // print_r($citizen);
            // echo "</pre>";
            if ($citizen->save()) {
                $storeId[$i] = $citizen;
                $i++;
                $transactionStatus = AppealCitizenRepository::storeAppealCitizen($citizen->id, $appealId, $nominees['type'],$auth_user_and_necessary_data['auth_user_id']);
                if (!$transactionStatus) {
                    $transactionStatus = false;
                    break;
                }
            } else {
                $transactionStatus = false;
                break;
            }
            if($transactionStatus == false)
                break;
        }

        // // dd($storeId);

        return $transactionStatus;
    }

    public static function getCitizenByCitizenId($citizenId){
        $citizen=EmCitizen::find($citizenId);
        return $citizen;
    }
    public static function getAppealCitizenByCitizenId($citizenId){
        $appealCitizen=EmAppealCitizen::find($citizenId);
        return $appealCitizen;
    }
    public static function getCitizenByAppealId($appealId){

        // $citizen=DB::connection('appeal')
        //     ->select(DB::raw(
        //         "SELECT * FROM citizens
        //          JOIN appeal_citizens ac ON ac.citizen_id=citizens.id
        //          WHERE ac.appeal_id =$appealId"
        //     ));

        $citizens = DB::table('em_citizens')
        ->join('em_appeal_citizens as ac', 'ac.citizen_id', '=', 'em_citizens.id')
        ->where('ac.appeal_id', $appealId)
        ->get();

        return $citizens;
    }

    public static function destroyCitizen($citizenIds){

        foreach ($citizenIds as $citizenId){
            $citizen=EmCitizen::where('id',$citizenId);
            $citizen->delete();
        }

        return;
    }

    public static function getOffenderLawyerCitizen($appealId){
        $lawerCitizen=[];
        $offenderCitizen=[];

        $appeal = Appeal::find($appealId);
        //prepare applicant citizen,lawyer citizen,offender citizen
        $citizens=$appeal->appealCitizens;
        foreach ($citizens as $citizen){
            $citizenTypes = $citizen->citizenType;
            foreach ($citizenTypes as $citizenType){
                if($citizenType->id==1){
                    $offenderCitizen=$citizen;
                }
                if($citizenType->id==4){
                    $lawerCitizen=$citizen;
                }
            }
        }

        return ['offenderCitizen'=>$offenderCitizen,
                'lawerCitizen'=>$lawerCitizen ];

    }
    public static function checkCitizenExist($citizen_id,$nid){
        
        if(isset($citizen_id)){
            $citizen=EmCitizen::where('citizen_phone_no',$citizen_id)->first();
        }
        elseif(isset($nid))
        {
            $citizen=EmCitizen::where('citizen_NID',$nid)->first();
            
        }

        if(isset($citizen))
        {
            return $citizen;
        } 
        else{
            $citizen=new EmCitizen();
            return $citizen;
        }
         //dd($citizen);
    }

    

}