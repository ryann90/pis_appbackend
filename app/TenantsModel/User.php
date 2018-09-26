<?php

namespace App\TenantsModel;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $connection = 'client';
    protected $guarded = [];
    protected $hidden = ['updated_at'];

    public function employee_details(){
        return $this->hasOne('App\TenantsModel\Employee_details', 'user_id', 'user_id');
    }
}
