<?php

namespace Bo\Shortcode\App\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Shortcode extends Model
{
    use CrudTrait;

    protected $fillable = [
        'key',
        'name',
        'value',
        'type',
        'option'
    ];

    /**
     * Set table name
     * */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = "shortcodes";
    }
}
