<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigModule extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'config_modules';

    protected $fillable = [
        '_id',
        'course_id',
        'title',
        'module_order',
        'slug',
    ];
    public $timestamps = true;
}
