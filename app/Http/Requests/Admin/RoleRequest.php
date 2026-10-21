<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Create and edit a role. The super-admin role keeps its name: config/access.php matches on it.
 */
final class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $role = $this->route('role');
        $nameRules = ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role)];

        if ($role instanceof Role && $role->name === config('access.super_admin_role')) {
            $nameRules[] = Rule::in([$role->name]);
        }

        return [
            'name' => $nameRules,
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['name' => __('Role name'), 'permissions' => __('Permissions')];
    }

    /**
     * @return array{name: string, permissions: list<string>}
     */
    public function roleData(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'permissions' => array_values($this->input('permissions', [])),
        ];
    }
}
