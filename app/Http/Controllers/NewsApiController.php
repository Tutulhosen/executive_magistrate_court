<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    public function index()
    {
        $data['short_news'] = News::orderby('id', 'desc')->where('news_type', 1)->get();
        $data['big_news'] = News::orderby('id', 'desc')->where('news_type', 2)->get();
        $data['page_title'] = 'এক্সিকিউটিভ ম্যাজিস্ট্রেট কোর্টের জনপ্রিয় সংবাদ ও খবরের তালিকা';
        return ['success' => true,  "data" => $data];
    }
}