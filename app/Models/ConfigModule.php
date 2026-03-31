<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigModule extends Model
{
    use SoftDeletes;
    protected $connection = 'mongodb';
    protected $collection = 'config_modules';

    protected $fillable = [        
        'course_id',
        'title',
        'module_order',
        'slug',
    ];
    public $timestamps = true;
}
