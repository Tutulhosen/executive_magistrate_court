<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CdapUserManagementController;


class LandingPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->where('status',1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->where('status',1)->get();
        // return $data;
        return view('publicHomeH')->with($data);   
    }

    public function policy(){
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->where('status',1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->where('status',1)->get();
        // return $data;
        return view('policy')->with($data); 
    }
    public function show_log_in_page()
    {
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->where('status',1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->where('status',1)->get();
        // return $data;
        return view('loginPage')->with($data);
    }
    public function show_log_in_page_test()
    {
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->where('status',1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->where('status',1)->get();
        // return $data;
        return view('loginPageTest')->with($data);
    }
    public function cprc_home_page()
    {
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->where('status',1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->where('status',1)->get();
        $crpc = DB::table('crpc_sections')
        ->join('crpc_section_details','crpc_sections.crpc_id','=','crpc_section_details.crpc_id')
        ->where('crpc_sections.status','=',1)
        ->orderBy('crpc_section_details.crpc_id')
        ->select('crpc_sections.crpc_name','crpc_section_details.crpc_details','crpc_sections.crpc_id')
        ->get(); 

         // $crpc;
        $data['crpc']=$crpc;
        return view('crpc_home_page_details')->with($data);
    }
    public function process_map_view()
    {
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->where('status',1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->where('status',1)->get();
        return view('process_map_view')->with($data);
    }


    
    public function logout()
    {
        // if( Auth::check())
        // {
        //     if(!empty(globalUserInfo()->is_cdap_user) &&  globalUserInfo()->is_cdap_user == 1)
        //     {
        //         CdapUserManagementController::logout();
        //     }
        //     if(!empty(globalUserInfo()->doptor_user_flag) &&  globalUserInfo()->doptor_user_flag == 1)
        //     {
        //         Auth::logout();
        //         $callbackurl = url('/');
        //         $zoom_join_url = DOPTOR_ENDPOINT().'/logout?' . 'referer=' . base64_encode($callbackurl);
        //         return redirect()->away($zoom_join_url);
        //     }
            
        // }
        
        // Auth::logout();
        // return redirect()->route('home');
        Auth::logout();
        $url=getCommonBaseUrl();

        $callbackurl = url($url);
        $zoom_join_url = DOPTOR_ENDPOINT() . '/logout?' . 'referer=' . base64_encode($url."/custom-logout");
        return redirect($zoom_join_url); 
    }
    
    public function cslogout()
    {
        

        // if( Auth::check())
        // {
        //     if(!empty(globalUserInfo()->is_cdap_user) &&  globalUserInfo()->is_cdap_user == 1)
        //     {
        //         CdapUserManagementController::logout();
        //     }
        //     if(!empty(globalUserInfo()->doptor_user_flag) &&  globalUserInfo()->doptor_user_flag == 1)
        //     {
        //         Auth::logout();
        //         $callbackurl = url('/');
        //         $zoom_join_url = DOPTOR_ENDPOINT().'/logout?' . 'referer=' . base64_encode($callbackurl);
        //         return redirect()->away($zoom_join_url);
        //     }
            
        // }
        
        // Auth::logout();
        // return redirect()->route('home');
        Auth::logout();
        $url=getCommonBaseUrl();

        $callbackurl = url($url);
        $zoom_join_url = DOPTOR_ENDPOINT() . '/logout?' . 'referer=' . base64_encode($url."/custom-logout");
        return redirect($zoom_join_url); 
    }
    

    public function crawling()
    {  
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->where('status',1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->where('status',1)->get();
        $data['link']='https://beta-idp.stage.mygov.bd/profile';   
        return view('cdap_nid_error')->with($data);
    }

    public function home_redirct(){
        $url=getCommonBaseUrl();
        $callbackurl = url($url.'/doptor/court');
        $zoom_join_url = DOPTOR_ENDPOINT() . '/logout?' . 'referer=' . base64_encode($callbackurl);
        // return redirect($zoom_join_url); 
        Auth::logout();
        return redirect( $callbackurl);
        

    }
}
