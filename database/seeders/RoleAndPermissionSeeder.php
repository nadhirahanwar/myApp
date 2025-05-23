<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['role_name' => 'admin'],
            ['description' => 'Administrator']
        );

        $userRole = Role::firstOrCreate(
            ['role_name' => 'user'],
            ['description' => 'Regular User']
        );

        // Create permissions
        $createPermission = Permission::firstOrCreate(['permission_name' => 'create']);
        $readPermission = Permission::firstOrCreate(['permission_name' => 'read']);
        $updatePermission = Permission::firstOrCreate(['permission_name' => 'update']);
        $deletePermission = Permission::firstOrCreate(['permission_name' => 'delete']);

        // Assign permissions to roles
        $adminRole->permissions()->sync([$createPermission->id, $readPermission->id, $updatePermission->id, $deletePermission->id]);
        $userRole->permissions()->sync([$createPermission->id, $readPermission->id]);

        // Create default users and assign roles
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password')
            ]
        );
        $adminUser->roles()->sync([$adminRole->id]);

        $normalUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal User',
                'password' => bcrypt('password')
            ]
        );
        $normalUser->roles()->sync([$userRole->id]);
    }
}
