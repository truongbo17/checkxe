<?php

namespace Bo\Car\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Bo\Medias\Models\Medias;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MediaModel;
use Illuminate\Support\Facades\Cache;

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
        // File có hạn là 13h, cache là 12h => đảm bảo cache phải hết trước khi sinh file
        return Cache::remember('cache_for_source_id_' . $this->id, Carbon::now()->addHours(12), function () {
            $result = [];
            Medias::where('source_id', $this->id)->each(function ($item) use (&$result) {
                $result[] = $this->getPublicUrl($item->target_data);
            });

            return $result;
        });
    }
}
