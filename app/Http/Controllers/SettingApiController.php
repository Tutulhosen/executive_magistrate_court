<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingApiController extends Controller
{
    public function crpcSectionsSave(Request $request)
    {
        $requestData = $request->all();
        $allInfo = $requestData['body_data'];
        DB::table('crpc_sections')->insert([
            'crpc_id' => $allInfo['crpc_id'],
            'crpc_name' => $allInfo['crpc_name']
        ]);

        DB::table('crpc_section_details')->insert([
            'crpc_id' => $allInfo['crpc_id'],
            'crpc_details' => $allInfo['crpc_details']
        ]);

        return ['success' => true, 'message' => 'Data save successfully!'];
    }
    public function crpcSectionsUpdate(Request $request, $id=null)
    {
        $requestData = $request->all();
        $allInfo = $requestData['body_data'];
        $data = [
            'crpc_id' => $allInfo['crpc_id'],
            'crpc_name' => $allInfo['crpc_name'],
            'status' => $allInfo['status'],
        ];

        $details = [
            'crpc_details' => $allInfo['crpc_details']
        ];
        $ID = DB::table('crpc_sections')->where('id', $id)->update($data);
        DB::table('crpc_section_details')->where('crpc_id', $allInfo['crpc_id'])->update($details);
        return ['success' => true, 'message' => 'Data updated successfully!'];
    }
}