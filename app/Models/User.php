<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    public function getActivitylogOptions(): LogOptions
    {
        // Never the password hash or remember token: the audit trail is readable by admins.
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * By role name from config, never by user id: ids change with every reseed or restore.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('access.super_admin_role'));
    }

    /**
     * No escalation: a role may be handed out only by someone who already holds all its permissions.
     * The super-admin role has no permission rows (Gate::before lets it through), so only a
     * super-admin may grant it.
     */
    public function mayGrantRole(Role $role): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        if ($role->name === config('access.super_admin_role')) {
            return false;
        }

        return $this->holdsAll($role->permissions->pluck('name')->all());
    }

    public function mayGrantPermission(string $permission): bool
    {
        return $this->isSuperAdmin() || $this->holdsAll([$permission]);
    }

    /**
     * Editing an account includes setting its password, so managing a user whose access exceeds
     * yours is a takeover of that access.
     */
    public function mayManage(self $target): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        if ($target->isSuperAdmin()) {
            return false;
        }

        return $this->holdsAll($target->getAllPermissions()->pluck('name')->all());
    }

    /**
     * @param  list<string>  $permissions
     */
    private function holdsAll(array $permissions): bool
    {
        return array_diff($permissions, $this->getAllPermissions()->pluck('name')->all()) === [];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
