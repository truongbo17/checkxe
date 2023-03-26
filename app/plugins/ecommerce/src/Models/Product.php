<?php

namespace Bo\Ecommerce\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Bo\Base\Models\Traits\SpatieTranslatable\HasTranslations;
use Bo\Ecommerce\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use CrudTrait;
    use HasTranslations;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'ec_products';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['name', 'description', 'details', 'features', 'price', 'category_id', 'extras', 'status', 'condition'];
    // protected $hidden = [];
    // protected $dates = [];
    public $translatable = ['name', 'description', 'details', 'features', 'extras'];
    public $casts = [
        'features'       => 'object',
        'extra_features' => 'object',
        'status'         => ProductStatus::class,
    ];
    public $timestamps = true;

    public function category()
    {
        return $this->belongsTo('Bo\Ecommerce\Models\Category', 'category_id');
    }
}
