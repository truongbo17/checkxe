<?php

namespace Bo\Handbook\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Handbook extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'handbooks';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name'
    ];
    // protected $hidden = [];
    // protected $dates = [];

}
