<?php

namespace App\TenantsModel;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $connection = 'client';
    protected $guarded = [];
}
