<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tenants;
use App\Traits\TenantTraits;
use App\TenantsModel\User;
use Validator;
use Session;

class TenantLoginController extends Controller
{
    use TenantTraits;

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username' => 'required',
            'link' => 'required',
            'password' => 'required',
        ]);

        if (!$validation->fails()) {
            // finds the tenant info in the database tenants for validation
            $tenant = Tenants::where('link', $request->post('link'))->first();
            // if the tenant access wrong link or null link redirect the tenant back
            if ($tenant['link'] == null || $tenant['link'] != $request->post('link')) {
                return response()->json([
                    'status' => 'failed',
                    'msg' => 'link not found'
                ]);

            } else {

                //sets the db connection base on the users login
                clientConnect('127.0.0.1', $tenant['database'], 'root');

                //sets the table of specific user to its correct table
                //always add new User(); to be able to work the code properly
                $users = new User();
                $user_model = $users->setTable($tenant['tbl'] . "_users");
                // checks if the user email or password exist on the user table
                if ($user = $user_model->where(['email' => $request->username, 'password' => $request->password])->first()) {
                    //if true: return status success and msg array
                    return response()->json([
                        'status' => 'success',
                        'msg' => [
                            'user_table' => $user,
                            'tenant_info' => [
                                'company_id' => $tenant['company_id'],
                                'subs_type' => $tenant['subscription_type'],
                                'company_name' => $tenant['company_name'],
                                'd' => $tenant['database'],
                                't' => $tenant['tbl'],
                                'link' => $tenant['link'],
                                'expires_at' => $tenant['expires_at']
                            ],
                        ]
                    ]);
                } else {
                    // else: return status failed and msg incorrect username or password
                    return response()->json([
                        'status' => 'failed',
                        'msg' => 'Incorrect username or password'
                    ]);
                }
            }
        } else {
            return response()->json(['msg' => $validation->errors(), 'status' => 'failed']);
        }
    }
}
