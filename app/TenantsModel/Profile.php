<?php

namespace App\TenantsModel;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $connection = 'client';
    protected $guarded = [];
}
