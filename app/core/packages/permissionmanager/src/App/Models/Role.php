<?php

namespace Bo\PermissionManager\App\Models;

use Bo\Base\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use CrudTrait;

    public function getTable()
    {
        return config('permission.table_names.roles', parent::getTable());
    }

    protected $fillable = [
        'name',
        'list_route_admin',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'list_route_admin' => 'array'
    ];

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            config('bo.permissionmanager.models.user'),
            'model',
            config('bo.permissionmanager.table_model_has_roles'),
            config('bo.permissionmanager.model_has_role_primary_key'),
            config('bo.permissionmanager.model_morph_key')
        );
    }

}
