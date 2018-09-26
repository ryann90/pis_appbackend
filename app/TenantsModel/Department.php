<?php

namespace App\TenantsModel;

use Illuminate\Database\Eloquent\Model;
use App\TenantsModel\EmployeeDetails;

class Department extends Model
{
    protected $connection = 'client';
    protected $guarded = [];

    // initialize, once this model was called this function will automaticaaly run
    public function __construct($tbl)
    {
        $this->table = $tbl;
    }

    public function scopeGetEmployeeDetailsBy($query, $tbl, $id)
    {
        //this will set the table
        $employee_details = new EmployeeDetails($tbl."_employee_details");
        //specifies the table
        return $employee_details->where('user_id', $id)->first(['fname','mname','lname','work_location']);
    }
}
