<?php

namespace Bo\Settings\App\Models;

use Config;
use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use CrudTrait;

    protected $fillable = [
        'key',
        'name',
        'description',
        'value',
        'type',
        'active',
    ];

    /**
     * Set table name
     * */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('bo.setting.table_name');
    }

    /**
     * Get value
     *
     * @param string $key
     * @param string $type
     *
     * @return mixed
     * */
    public function get(string $key, string $type = 'setting')
    {
        $setting = new self();

        $entry = $setting
            ->where('key', $type . '_' . $key)
            ->where('type', $type)
            ->firstOrFail();
        return $entry->value;
    }

    /**
     * Update or Set value Setting
     *
     * @param string $key
     * @param string $value
     * @param string $type
     * */
    public function set(string $key, string $value, string $type = 'setting')
    {
        $setting = new self();
        // update the value in the database
        $setting->updateOrCreate(
            [
                'key'  => $type . '_' . $key,
                'type' => $type
            ],
            [
                'value' => $value
            ]);

        // update the value in the session
        Config::set($type . '_' . $key, $value);
        if (Config::get($type . '_' . $key) == $value) {
            return true;
        }

        return false;
    }
}
