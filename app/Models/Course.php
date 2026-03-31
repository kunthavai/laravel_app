<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;
    protected $connection = 'mongodb';
    protected $collection = 'courses';

    protected $fillable = [       
        'title',
        'description',
        'slug',
    ];
    public $timestamps = true;
}