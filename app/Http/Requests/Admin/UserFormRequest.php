<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Shared pieces of the create- and edit-user forms.
 */
abstract class UserFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
