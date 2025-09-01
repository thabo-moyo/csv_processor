<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'initial',
        'last_name'
    ];
}
