<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Closure;
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
            'permissions.*' => ['bail', 'string', 'exists:permissions,name', $this->grantablePermission()],
        ];
    }

    /**
     * No escalation (User::mayGrantPermission): without this, holding admin.roles.edit was enough to
     * grant your own role every permission.
     */
    private function grantablePermission(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            /** @var User $actor */
            $actor = $this->user();
            if (! $actor->mayGrantPermission($value)) {
                $fail(__('You cannot grant a permission you do not hold yourself.'));
            }
        };
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
