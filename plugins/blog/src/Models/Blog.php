<?php

namespace Bo\Blog\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'blogs';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name'
    ];
    // protected $hidden = [];
    // protected $dates = [];

}
