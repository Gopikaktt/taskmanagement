<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Always clear cached roles and permissions first
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ---------------------------------------------------------------------
        // 1. Staff Guard Roles & Permissions
        // ---------------------------------------------------------------------
        $staffGuard = 'staff';

        $staffPermissions = [
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',
            'export reports',
        ];

        foreach ($staffPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => $staffGuard]);
        }

        $managerRole  = Role::firstOrCreate(['name' => 'manager', 'guard_name' => $staffGuard]);
        $teamLeadRole = Role::firstOrCreate(['name' => 'team-lead', 'guard_name' => $staffGuard]);
        $memberRole   = Role::firstOrCreate(['name' => 'member', 'guard_name' => $staffGuard]);
        $internRole   = Role::firstOrCreate(['name' => 'intern', 'guard_name' => $staffGuard]);

        // 1. Manager has ALL permissions
        $managerRole->syncPermissions($staffPermissions);

        // 2. Team Lead has view, create, edit, assign, and export reports (no delete)
        $teamLeadRole->syncPermissions(['view tasks', 'create tasks', 'edit tasks', 'assign tasks', 'export reports']);

        // 3. Member has view, edit, and create permissions
        $memberRole->syncPermissions(['view tasks', 'edit tasks', 'create tasks']);

        $internRole->syncPermissions(['view tasks']);

        // Assign 'manager' to primary staff (John Doe)
        $johnDoe = Staff::where('email', 'staff@example.com')->first();
        if ($johnDoe) {
            $johnDoe->syncRoles([$managerRole]);
        }

        // Assign 'team-lead' to David Lee
        $davidLee = Staff::where('email', 'david.lee@example.com')->first();
        if ($davidLee) {
            $davidLee->syncRoles([$teamLeadRole]);
        }

        // Assign 'member' to all other staff members who don't have manager or team-lead
        $otherStaff = Staff::whereNotIn('email', ['staff@example.com', 'david.lee@example.com'])->get();
        foreach ($otherStaff as $staff) {
            if (!$staff->hasRole('manager') && !$staff->hasRole('team-lead')) {
                $staff->syncRoles([$memberRole]);
            }
        }

        // ---------------------------------------------------------------------
        // 2. Admin Guard Roles & Permissions
        // ---------------------------------------------------------------------
        $adminGuard = 'admin';

        $adminPermissions = [
            'manage projects',
            'manage tasks',
            'manage staff',
            'view reports',
        ];

        foreach ($adminPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => $adminGuard]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $adminGuard]);
        $superAdminRole->syncPermissions($adminPermissions);

        $projectAdminRole = Role::firstOrCreate(['name'=>'project-admin', 'guard_name' => $adminGuard]);
        $projectAdminRole->syncPermissions(['manage projects','manage tasks','view reports']);

        $auditorRole = Role::firstOrCreate(['name'=>'auditor', 'guard_name'=>$adminGuard]);
        $auditorRole->syncPermissions(['view reports']);
        // Assign 'super-admin' to all seeded admins
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->assignRole($superAdminRole);
        }
    }
}
