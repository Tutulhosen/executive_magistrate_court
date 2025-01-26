<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MyProfileController extends Controller
{
    public function index($user_id)
    {
      
        // $userInfo= DB::table('doptor_user_access_info')
        // ->join('users', 'doptor_user_access_info.common_login_user_id', '=', 'users.common_login_user_id')
        // ->where('users.common_login_user_id', Auth::user()->common_login_user_id)
        // ->select('users.*','doptor_user_access_info.*')
        // ->first();
        $userManagement = DB::table('doptor_user_access_info')
        ->join('users', 'doptor_user_access_info.user_id', '=', 'users.id')
        ->leftJoin('role', 'doptor_user_access_info.role_id', '=', 'role.id')
        ->leftJoin('office', 'users.office_id', '=', 'office.id')
        ->leftJoin('district', 'office.district_id', '=', 'district.id')
        ->leftJoin('upazila', 'office.upazila_id', '=', 'upazila.id')
        ->leftJoin('em_citizens', 'users.citizen_id', '=', 'em_citizens.id')
        ->select('users.email','users.mobile_no','users.username','users.name','users.profile_pic','users.signature','doptor_user_access_info.court_type_id','doptor_user_access_info.role_id','doptor_user_access_info.common_login_user_id','role.role_name','office.office_name_bn','district.district_name_bn', 'upazila.upazila_name_bn', 'em_citizens.present_address', 'em_citizens.permanent_address')
        ->where('users.id',$user_id)
        ->first();
     
        // $userManagement = DB::table('users')
        // ->join('role', 'users.role_id', '=', 'role.id')
        // ->leftJoin('office', 'users.office_id', '=', 'office.id')
        // ->leftJoin('district', 'office.district_id', '=', 'district.id')
        // ->leftJoin('upazila', 'office.upazila_id', '=', 'upazila.id')
        // ->leftJoin('em_citizens', 'users.citizen_id', '=', 'em_citizens.id')
        // ->select('users.*', 'role.role_name', 'office.office_name_bn', 
        //     'district.district_name_bn', 'upazila.upazila_name_bn', 'em_citizens.present_address', 'em_citizens.permanent_address')
        // ->where('users.id',$user_id)
        // ->first();

 
         $office_name = $userManagement->office_name_bn.', '.$userManagement->upazila_name_bn.', '.$userManagement->district_name_bn;
        
        if($userManagement->profile_pic != NULL)
        {
            if($userManagement->doptor_user_flag == 0)
            {
                 $profile_picture=url('/').'/uploads/profile/'.$userManagement->profile_pic;
            }
            else
            {
                $profile_picture=$userManagement->profile_pic;
                
            }
        }
        else
        {
            $profile_picture=url('/').'/uploads/profile/default.jpg';
        }
        
        if($userManagement->signature !=Null && $userManagement->doptor_user_flag == 1)
        {
            $signature=$userManagement->signature;
        }
        else
        {
            $signature = null;
        }
        // return response()->json( $userMa/nagement);

        return response()->json([
          'success'=>true,
          'data'=>[
            'name'=>$userManagement->name,
            'username'=>$userManagement->username,
            'role_name'=>$userManagement->role_name,
            'mobile_no'=>$userManagement->mobile_no,
            'office_name'=>$office_name,
            'email'=>$userManagement->email,
            'profile_picture'=>$profile_picture,
            'signature'=>$signature,
            'present_address'=>$userManagement->present_address,
            'permanent_address'=>$userManagement->permanent_address
          ]  
          
        ]);
    }
}
