<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

final class UpdateUserRequest extends UserFormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editedUser())],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
        ];
    }

    /**
     * Stop an administrator from locking themselves out by editing their own account.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->editedUser()->is($this->user())) {
                    return;
                }

                if (! $this->boolean('is_active')) {
                    $validator->errors()->add('is_active', __('You cannot deactivate your own account.'));
                }

                $superAdmin = config('access.super_admin_role');
                if ($this->user()->hasRole($superAdmin) && ! in_array($superAdmin, $this->input('roles', []), true)) {
                    $validator->errors()->add('roles', __('You cannot remove your own :role role.', ['role' => $superAdmin]));
                }
            },
        ];
    }

    private function editedUser(): User
    {
        /** @var User */
        return $this->route('user');
    }
}
