<?php

namespace Bo\MenuCRUD\App\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use CrudTrait;

    protected $table = 'menus';
    protected $fillable = [
        'name',
        'description',
    ];

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_items_pivot');
    }

    public function gotoMenuItem()
    {
        return '<a class="btn btn-sm btn-link" target="_blank" href="' . bo_url('menu-item') . '?menu-id=' . $this->id . ' " data-toggle="tooltip" title="Just a demo custom button."><i class="las la-sitemap"></i> Menu Item</a>';
    }
}
