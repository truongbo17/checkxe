<?php

namespace Bo\PermissionManager\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateCrudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return bo_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'list_route_admin' => 'required|string',
        ];

        return $rules;
    }
}
