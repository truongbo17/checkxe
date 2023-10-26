<?php

namespace Bo\Car\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MediaModel;

class Car extends Model
{
    use CrudTrait, MediaModel;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'cars';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $hidden = [];
    // protected $dates = [];

    public const PENDING_STATUS = 0;
    public const PUBLISH_STATUS = 1;

    public function getPublicUrlAttribute()
    {
        return $this->getPublicUrl();
    }
}
