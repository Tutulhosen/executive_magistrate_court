<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmcShortDecisionController extends Controller
{
    public function short_decision_store(Request $request)
    {

        try {
            $data_get = $request->getContent();
            $json_data = json_decode($data_get, true);
            $data = $json_data['body_data'];
            $result = DB::table('em_case_shortdecisions')->insert($data);
            return response()->json(['status' => true, 'data' => $data, 'result' => $result]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th]);
        }
    }

    public function short_decision_update(Request $request, $id = '')
    {

        try {
            $data_get = $request->getContent();
            $json_data = json_decode($data_get, true);
            $data = $json_data['body_data'];
            $result = DB::table('em_case_shortdecisions')->where('id', $id)->update($data);
            return response()->json(['status' => true, 'data' => $data, 'result' => $result]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th]);
        }
    }

    public function peskar_short_decision_store(Request $request)
    {

        try {
            $data_get = $request->getContent();
            $json_data = json_decode($data_get, true);
            $data = $json_data['body_data'];
            $result = DB::table('peshkar_case_shortdecisions')->insert($data);
            return response()->json(['status' => true, 'data' => $data, 'result' => $result]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th]);
        }
    }
    public function peskar_short_decision_update(Request $request, $id = '')
    {

        try {
            $data_get = $request->getContent();
            $json_data = json_decode($data_get, true);
            $data = $json_data['body_data'];
            $result = DB::table('peshkar_case_shortdecisions')->where('id', $id)->update($data);
            return response()->json(['status' => true, 'data' => $data, 'result' => $result]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th]);
        }
    }
}
