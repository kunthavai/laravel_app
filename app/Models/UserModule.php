<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserModule extends Model
{
    use SoftDeletes;
    protected $connection = 'mongodb';
    protected $collection = 'user_modules';

    protected $fillable = [
        'user_id',
        'course_id',
        'module_id',
        'module_order',
        'status',
        'score',
    ];

    public $timestamps = true;
}
