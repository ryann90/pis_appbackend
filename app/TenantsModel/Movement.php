<?php

namespace App\TenantsModel;

use Illuminate\Database\Eloquent\Model;
use App\TenantsModel\EmployeeDetails;
use App\TenantsModel\Department;

class Movement extends Model
{
    protected $connection = 'client';
    protected $guarded = [];
    protected $hidden = ['updated_at'];

    /* public function employeeDetails(){
        return $this->hasOne('App\TenantsModel\EmployeeDetails', 'user_id', 'user_id');
    } */

    // ini once this table was called this function will automatically run
    public function __construct($tbl)
    {
        $this->table = $tbl;
    }
    
    public function scopeGetEmployeeDetailsBy($query, $tbl, $id)
    {
        $employee_details = new EmployeeDetails($tbl."_employee_details");
        //this will specify what column should be get
        return $employee_details->where('user_id', $id)->first(['fname', 'mname', 'lname', 'work_location']);
        //or
        // this will get specific details only
        /* return $employee_details->where('user_id', $id)->first(); */
    }

    public function scopeGetEmployeeDetailsByDepartment($query, $tbl, $id)
    {
        // first method = object while get method is collection
        // use collect so that you can use
        $department_details = new Department($tbl."_department");
        return $department_details->where('department_id', $id)->limit(1)->get()->map(function($dh) use ($tbl){
            //this will specify what column should be get
            $dh['department_head'] = $this->getEmployeeDetailsBy($tbl, $dh['department_head']);
            return $dh;
        });
    }
}
