<?php

namespace App\TenantsModel;

use Illuminate\Database\Eloquent\Model;

class EmployeeDetails extends Model
{
    protected $connection = 'client';
    protected $guarded = [];
    protected $hidden = ['updated_at'];

    /* function getFullname(){
        return $this->fname. ' ' . $this->mname.' '.$this->lname;
    } */
    
    public function __construct($tbl)
    {
        $this->table = $tbl;
    }
}
