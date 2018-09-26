<?php

namespace App\Traits;

use App\Tenants;

trait TenantTraits{

	function subscriberChecker($company_link)
	{
		if ($t = Tenants::where('link', $company_link)->first(['subscription_type', 'expires_at', 'link', 'created_at'])) {
			//if the has the appropriate link
			return response()->json(['msg' => $t, 'status' => 'success']);
		}
		else {
			//if the user does not have the appropriate link
			return response()->json(['msg' => 'please validate your link', 'status' => 'failed']);
		}
	}

	function clientInsertion($request, $database){

		$owner_id = randomNumber();
		$company_id = randomNumber(10);
		$department_id = randomNumber();

		Tenants::insert([
			'company_id'=>$company_id,
			'owner_id'=>$owner_id,
			'company_name'=>$request->post('companyname'),
			'owner_email'=>$request->post('email'),
			'subscription_type'=>$request->post('subscription_type'),
			'database'=>encrypt($database),
			'tbl'=>encrypt($request->post('link').$company_id),
			'link'=>$request->post('link')
		]);

		$data = [
			'company' => ['company_id' => $company_id, 'name' => $request->post('companyname'), 'email' => $request->post('email')],
			'users' => ['user_id' => $owner_id, 'company_id' => $company_id, 'email' => $request->post('email'), 'password' => encrypt($request->post('password_confirmation'))],
			'employee_details' => ['user_id' => $owner_id, 'fname' => $request->post('fname'), 'mname' => $request->post('mname'), 'lname' => $request->post('lname'), 'email' => $request->post('email')],
			'movement' => ['user_id' => $owner_id, 'company_id' => $company_id, 'position' => 'owner', 'department_id' => $department_id, 'added_by' => $owner_id],
			'deparment' => ['department_id' => $department_id, 'department_name' => $request->post('companyname'), 'department_head' => $owner_id],
		];
		
		return $data;
	}
}