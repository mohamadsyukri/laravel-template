<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

/**
 * Shared pieces of the create- and edit-user forms.
 */
abstract class UserFormRequest extends FormRequest
{
    /**
     * Opening the screen is checked by AuthorizeRoute; editing another account by UserPolicy on the
     * route; each role handed out by grantableRole().
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * No escalation (User::mayGrantRole): refuse a role that grants more than the signed-in user
     * holds. Runs after `exists`, under `bail`, so the role is always found.
     */
    protected function grantableRole(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            /** @var User $actor */
            $actor = $this->user();
            $role = Role::query()->with('permissions')->where('name', $value)->first();
            if ($role !== null && ! $actor->mayGrantRole($role)) {
                $fail(__('You cannot assign the :role role; it grants more than your own access.', ['role' => $role->name]));
            }
        };
    }

    /**
     * The toggle posts nothing when it is off; turn that into false before validating.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'password' => __('Password'),
            'roles' => __('Roles'),
            'is_active' => __('Active'),
        ];
    }

    /**
     * @return array{name: string, email: string, password: ?string, is_active: bool, roles: list<string>}
     */
    public function userData(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'email' => $this->string('email')->lower()->toString(),
            'password' => $this->filled('password') ? $this->string('password')->toString() : null,
            'is_active' => $this->boolean('is_active'),
            'roles' => array_values($this->input('roles', [])),
        ];
    }
}
