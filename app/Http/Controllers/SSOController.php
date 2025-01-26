<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SSOController extends Controller
{
    public function getLogin(Request $request) {
        // Generate a random state parameter and store it in the session
        $state = Str::random(40);
        $request->session()->put("state", $state);
       
        // Build the query string with the correct state parameter
        $query = http_build_query([
            "client_id" =>17,
            "redirect_uri" => getEmcBaseUrl().'/callback',
            "response_type" => "code",
            "scope" => "view-user",
            "state" => ''
        ]);
   
        // Redirect to the OAuth authorization endpoint
        return redirect(getCommonBaseUrl()."/oauth/authorize?" . $query);
    }
    public function getCallback(Request $request){
        
       
        $state = $request->session()->pull("state");
     
        // throw_unless(strlen($state) > 0 && $state == $request->start,
        // InvalidArgumentException::class);
        $response = Http::asForm()->post(
            getCommonBaseUrl()."/oauth/token",
            [
            "grant_type" => "authorization_code",
            "client_id" =>17,
            "client_secret" => "lHkAkmq4BAX7HYn3Deimg0Qpwez839RnZlHDeM3M",
            "redirect_uri" => getEmcBaseUrl().'/callback',
            "code" =>$request->code
        ]);

     
        // $request->session()->put($response->json());
        // return redirect(route("sso.connect"));
        $token = $response->json()['access_token']; 
        session(['access_token' => $token]);
        // Log::info('Access token stored in session: ' . session('access_token'));
        return redirect(route('sso.connect', ['token' => $token]));
    }
    public function connectUser(Request $request){
        // $access_token =$request->session()->get("access_token");
        $access_token = $request->query('token');
      
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer " . $access_token
        ])->get(getCommonBaseUrl()."/api/getuser");
        $userAray = $response->json();

      
        $username =$userAray['user_info']['username'];
        
        $user = User::where("username",  $username)->first();

        //office data setup
        $office_data = [
            'id'    =>$userAray['office_data']['id'],
            'level' => $userAray['office_data']['level'],
            'parent' => $userAray['office_data']['parent'],
            'parent_name' => $userAray['office_data']['parent_name'],
            'office_name_bn' => $userAray['office_data']['office_name_bn'],
            'office_name_en' => $userAray['office_data']['office_name_en'],
            'unit_name_bn' => $userAray['office_data']['unit_name_bn'],
            'unit_name_en' => $userAray['office_data']['unit_name_en'],
            'division_id' => $userAray['office_data']['division_id'],
            'div_name_bn' => $userAray['office_data']['div_name_bn'],
            'div_name_en' => $userAray['office_data']['div_name_en'],
            'office_unit_organogram_id' => $userAray['office_data']['office_unit_organogram_id'],
            'is_gcc' => $userAray['office_data']['is_gcc'],
            'is_organization' => $userAray['office_data']['is_organization'],
            'status' => $userAray['office_data']['status'],

            'upazila_id' => null,
            'upa_name_bn' => null,
            'upa_name_en' => null,
            'district_id' => $userAray['office_data']['district_id'],
            'dis_name_bn' => $userAray['office_data']['dis_name_bn'],
            'dis_name_en' => $userAray['office_data']['dis_name_en'],
            'district_bbs_code' =>$userAray['office_data']['district_bbs_code'],
            'upazila_bbs_code' => null,
        ];
         
       
        self::OfficeExitsCheck($office_data);
      
        //user data setup
        // dd($user);
        if(!$user){
            $user =new User;
            $user->common_login_user_id=$userAray['user_info']['id'];
            $user->name=$userAray['user_info']['name'];
            $user->username=$userAray['user_info']['username'];
            $user->office_id=$userAray['user_info']['office_id'];
            $user->doptor_office_id=$userAray['user_info']['doptor_office_id'];
            $user->doptor_user_flag=$userAray['user_info']['doptor_user_flag'];
            $user->doptor_user_active=$userAray['user_info']['doptor_user_active'];
            $user->peshkar_active=$userAray['user_info']['peshkar_active'];
            $user->court_id=$userAray['user_info']['court_id'];
            $user->mobile_no=$userAray['user_info']['mobile_no'];
            $user->profile_image=$userAray['user_info']['profile_image'];
            $user->signature=$userAray['user_info']['signature'];
            $user->designation=$userAray['user_info']['designation'];
            $user->email=$userAray['user_info']['email'];
            $user->is_verified_account=$userAray['user_info']['is_verified_account'];
            $user->email_verified_at=$userAray['user_info']['email_verified_at'];
            $user->profile_pic=$userAray['user_info']['profile_pic'];
            $user->common_login_user_created_at=$userAray['user_info']['created_at'];
            $user->common_login_user_updated_at=$userAray['user_info']['updated_at'];
            $user->save();

            DB::table('doptor_user_access_info')->insert([
                'user_id' =>$user->id,
                'common_login_user_id' =>$userAray['user_info']['id'],
                'court_type_id' =>$userAray['user_info']['user_access_court_type_id'],
                'role_id' =>$userAray['user_info']['user_access_role_id'],
                'court_id' =>$userAray['user_info']['user_access_court_id'],
            ]);

            
        }else {
            DB::table('users')->where('username',$userAray['user_info']['username'])->update([
                'name' =>$userAray['user_info']['name'],
                'office_id' =>$userAray['user_info']['office_id'],
                'doptor_office_id' =>$userAray['user_info']['doptor_office_id'],
                'doptor_user_flag' =>$userAray['user_info']['doptor_user_flag'],
                'doptor_user_active' =>$userAray['user_info']['doptor_user_active'],
                'peshkar_active' =>$userAray['user_info']['peshkar_active'],
                'mobile_no' =>$userAray['user_info']['mobile_no'],
                'profile_image' =>$userAray['user_info']['profile_image'],
                'signature' =>$userAray['user_info']['signature'],
                'designation' =>$userAray['user_info']['designation'],
                'email' =>$userAray['user_info']['email'],
                'is_verified_account' =>$userAray['user_info']['is_verified_account'],
                'email_verified_at' =>$userAray['user_info']['email_verified_at'],
                'profile_pic' =>$userAray['user_info']['profile_pic'],
                'common_login_user_created_at' =>$userAray['user_info']['created_at'],
            ]);

            DB::table('doptor_user_access_info')->where('common_login_user_id',$userAray['user_info']['id'])->update([
                'court_type_id' =>$userAray['user_info']['user_access_court_type_id'],
                'role_id' =>$userAray['user_info']['user_access_role_id'],
                'court_id' =>$userAray['user_info']['user_access_court_id'],
            ]);
        }


        
        Auth::login($user);
        return redirect(route("dashboard"));
    }


    //office check
    public static function OfficeExitsCheck($office_data)
    {
        
         $office_id=DB::table('office')
                    ->where('id','=',$office_data['id'])
                     ->first();
        
         if(empty($office_id))
         {
            $office_id = DB::table('office')->insertGetId($office_data);

            return $office_id;
         }
         else
         {
          
            $office_id = DB::table('office')->where('id',$office_id->id)->update($office_data);
           
            return $office_id;
         }            
    }
}