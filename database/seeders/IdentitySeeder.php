<?php

namespace Database\Seeders;

use App\Modules\Identity\Models\Permission;
use App\Modules\Identity\Models\Role;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IdentitySeeder extends Seeder
{
    /**
     * Permission groups keyed by their owning module.
     *
     * @var array<string, array<int, string>>
     */
    private array $permissionGroups = [
        'Dashboard' => ['dashboard.view'],
        'Identity' => ['users.view', 'users.create', 'users.update', 'users.delete', 'roles.view', 'roles.manage'],
        'Student' => ['students.view', 'students.create', 'students.update', 'students.delete'],
        'Staff' => ['staff.view', 'staff.manage'],
        'Agent' => ['agents.view', 'agents.manage'],
        'Instructor' => ['instructors.view', 'instructors.manage'],
        'Course' => ['courses.view', 'courses.manage'],
        'Booking' => ['bookings.view', 'bookings.manage'],
        'Schedule' => ['schedules.view', 'schedules.manage'],
        'Vehicle' => ['vehicles.view', 'vehicles.manage'],
        'JPJ' => ['jpj.view', 'jpj.manage'],
        'Invoice' => ['invoices.view', 'invoices.manage'],
        'Payment' => ['payments.view', 'payments.manage'],
        'Wallet' => ['wallets.view', 'wallets.manage'],
        'Notification' => ['notifications.view', 'notifications.manage'],
        'Configuration' => ['configurations.view', 'configurations.manage'],
        'Document' => ['documents.view', 'documents.manage'],
    ];

    /**
     * Seed the Identity roles, permissions and demo users.
     */
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedUsers();
    }

    private function seedPermissions(): void
    {
        foreach ($this->permissionGroups as $group => $slugs) {
            foreach ($slugs as $slug) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    ['name' => Str::headline($slug), 'group' => $group],
                );
            }
        }
    }

    private function seedRoles(): void
    {
        $permissionIds = Permission::query()->pluck('id', 'slug');

        $definitions = [
            'admin' => [
                'name' => 'Administrator',
                'permissions' => $permissionIds->keys()->all(),
            ],
            'staff' => [
                'name' => 'Staff',
                'permissions' => [
                    'dashboard.view', 'students.view', 'students.update', 'courses.view', 'courses.manage',
                    'bookings.view', 'bookings.manage', 'schedules.view', 'schedules.manage', 'vehicles.view',
                    'invoices.view', 'invoices.manage', 'payments.view', 'payments.manage',
                    'documents.view', 'documents.manage', 'notifications.view',
                ],
            ],
            'instructor' => [
                'name' => 'Instructor',
                'permissions' => [
                    'dashboard.view', 'students.view', 'courses.view', 'schedules.view', 'documents.view',
                ],
            ],
            'agent' => [
                'name' => 'Agent',
                'permissions' => [
                    'dashboard.view', 'students.view', 'students.create', 'bookings.view', 'bookings.manage',
                    'invoices.view', 'wallets.view', 'documents.view',
                ],
            ],
            'student' => [
                'name' => 'Student',
                'permissions' => [
                    'dashboard.view', 'courses.view', 'bookings.view', 'schedules.view',
                    'invoices.view', 'wallets.view', 'documents.view',
                ],
            ],
        ];

        foreach ($definitions as $slug => $definition) {
            $role = Role::updateOrCreate(
                ['slug' => $slug],
                ['name' => $definition['name'], 'is_system' => true],
            );

            $role->permissions()->sync(
                $permissionIds->only($definition['permissions'])->values()->all(),
            );
        }
    }

    private function seedUsers(): void
    {
        $users = [
            ['email' => 'admin@imjpj.my', 'name' => 'System Administrator', 'role' => 'admin'],
            ['email' => 'staff@imjpj.my', 'name' => 'Staff Member', 'role' => 'staff'],
            ['email' => 'instructor@imjpj.my', 'name' => 'Driving Instructor', 'role' => 'instructor'],
            ['email' => 'agent@imjpj.my', 'name' => 'Registration Agent', 'role' => 'agent'],
            ['email' => 'student@imjpj.my', 'name' => 'Student User', 'role' => 'student'],
        ];

        foreach ($users as $data) {
            $user = User::query()->where('email', $data['email'])->first();

            if (! $user) {
                $user = User::factory()->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);
            }

            $user->roles()->sync(
                Role::query()->where('slug', $data['role'])->pluck('id')->all(),
            );

            $this->createProfile($user, $data['role']);
        }
    }

    private function createProfile(User $user, string $role): void
    {
        match ($role) {
            'admin' => $user->adminProfile()->updateOrCreate([], ['employee_no' => 'ADM-0001', 'title' => 'Administrator']),
            'staff' => $user->staffProfile()->updateOrCreate([], ['staff_no' => 'STF-0001', 'position' => 'Counter Staff']),
            'instructor' => $user->instructorProfile()->updateOrCreate([], ['staff_no' => 'INS-0001', 'instructor_class' => 'D']),
            'agent' => $user->agentProfile()->updateOrCreate([], ['agent_code' => 'AGT-0001', 'commission_rate' => 5.00]),
            'student' => $user->studentProfile()->updateOrCreate([], ['student_no' => 'STU-0001']),
            default => null,
        };
    }
}
