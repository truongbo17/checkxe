<?php

namespace Bo\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Blogs extends Model
{
    use \Bo\Base\Models\Traits\CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'blogs';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

}
