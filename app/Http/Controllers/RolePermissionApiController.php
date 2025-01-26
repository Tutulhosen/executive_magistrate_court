<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $bodyData = json_decode($request->input('body_data'), true);
            $permission_arr = $bodyData['permission_arr'];
            $roleId = $bodyData['role_id'];
            // return $roleId;
            $if_already_has = DB::table('role_permission')->where('role_id', $roleId)->get();
            if ($if_already_has->count() > 0) {
                
                if (!empty($permission_arr)) {
                    $delete_first = DB::table('role_permission')->where('role_id', $roleId)->delete();
                    foreach ($permission_arr as $role_permisson) {
                        // return $roleId;
                        DB::table('role_permission')->insert([
                            'role_id' => $roleId,
                            'permission_id' => $role_permisson,
                            'status' => 1,
                        ]);
                    }
                } else {
                    $delete_first = DB::table('role_permission')->where('role_id', $roleId)->delete();
                    DB::table('role_permission')->insert([
                        'role_id' => $request->role_id,
                        'permission_id' => null,
                        'status' => 1,
                    ]);
                }
            } else {
                if (!empty($permission_arr)) {
                    foreach ($permission_arr as $role_permisson) {
                        DB::table('role_permission')->insert([
                            'role_id' =>  $roleId,
                            'permission_id' => $role_permisson,
                            'status' => 1,
                        ]);
                    }
                } else {
                    DB::table('role_permission')->insert([
                        'role_id' => $roleId,
                        'permission_id' => null,
                        'status' => 1,
                    ]);
                }
            }

            return ['status' => 'success'];
        } catch (\Throwable $th) {
            //throw $th;
            return ['status' => 'failed', "error" => $th];
        }
    }
}