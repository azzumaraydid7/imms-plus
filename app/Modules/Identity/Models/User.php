<?php

namespace App\Modules\Identity\Models;

use App\Modules\Agent\Models\AgentProfile;
use App\Modules\Instructor\Models\InstructorProfile;
use App\Modules\Staff\Models\StaffProfile;
use App\Modules\Student\Models\StudentProfile;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['uuid', 'name', 'email', 'phone', 'status', 'email_verified_at', 'last_login_at'])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The password hash lives in the credentials table, not on the user row.
     */
    public function getAuthPassword(): string
    {
        return (string) $this->credential?->password;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function credential(): HasOne
    {
        return $this->hasOne(UserCredential::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function agentProfile(): HasOne
    {
        return $this->hasOne(AgentProfile::class);
    }

    public function instructorProfile(): HasOne
    {
        return $this->hasOne(InstructorProfile::class);
    }

    public function adminProfile(): HasOne
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    /**
     * @param  array<int, string>  $slugs
     */
    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles->whereIn('slug', $slugs)->isNotEmpty();
    }

    public function hasPermission(string $slug): bool
    {
        return $this->effectivePermissions()->contains('slug', $slug);
    }

    /**
     * @return array<int, string>
     */
    public function roleSlugs(): array
    {
        return $this->roles->pluck('slug')->all();
    }

    /**
     * @return array<int, string>
     */
    public function permissionSlugs(): array
    {
        return $this->effectivePermissions()->pluck('slug')->all();
    }

    /**
     * All permissions granted to the user through their roles.
     *
     * @return Collection<int, Permission>
     */
    public function effectivePermissions(): Collection
    {
        return $this->roles()->with('permissions')->get()
            ->flatMap(fn (Role $role) => $role->permissions)
            ->unique('id')
            ->values();
    }
}
