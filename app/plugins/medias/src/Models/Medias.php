<?php

namespace Bo\Medias\Models;

use App\Traits\MediaModel;
use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
    use CrudTrait, MediaModel;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'medias';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $hidden = [];
    // protected $dates = [];

    public function getMedia()
    {
        return $this->getPublicUrl($this->target_data);
    }
}
