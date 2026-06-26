<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['name', 'position', 'ad_code', 'status'];

    protected $casts = ['status' => 'boolean'];
}
