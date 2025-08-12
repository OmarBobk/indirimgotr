<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'manage_users',
            'manage_sections',
            'manage_products',
            'view_sales',
            'create_orders',
            'customer_profile',
            'edit_orders',
            'delete_orders',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        // Admin: All permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Salesperson: Limited permissions
        $salesperson = Role::firstOrCreate(['name' => 'salesperson']);
        $salesperson->givePermissionTo([
            'view_sales',
            'create_orders',
            'edit_orders',
        ]);

        // Shop Owner: Restricted permissions
        $shopOwner = Role::firstOrCreate(['name' => 'shop_owner']);
        $shopOwner->givePermissionTo([
            'view_sales',
            'create_orders',
        ]);

        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->givePermissionTo([
            'customer_profile',
        ]);

        // Create Admin User
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
        ]);
        $admin->assignRole('admin');

        // Create Salesperson Users
        $ahmad = User::factory()->create([
            'name' => 'Ahmad',
            'email' => 'ahmad@gmail.com',
        ]);
        $ahmad->assignRole('salesperson');

        $zain = User::factory()->create([
            'id' => 9, // Zain Salesperson
            'name' => 'Zain',
            'email' => 'zain@gmail.com',
        ]);
        $zain->assignRole('salesperson');

        // Create Shop Owner Users
        $karman_telekom = User::factory()->create([
            'name' => 'Karman Telekom',
            'email' => 'karmantelekom@gmail.com',
        ]);
        $karman_telekom->assignRole('shop_owner');

        $cephane_owner = User::factory()->create([
            'name' => 'Cephane Owner',
            'email' => 'cephane@gmail.com',
        ]);
        $cephane_owner->assignRole('shop_owner');

        $zore_owner = User::factory()->create([
            'name' => 'Zore Owner',
            'email' => 'zore@gmail.com',
        ]);
        $zore_owner->assignRole('shop_owner');

        $simex_owner = User::factory()->create([
            'name' => 'Simex Owner',
            'email' => 'simex@gmail.com',
        ]);
        $simex_owner->assignRole('shop_owner');

        // Create Customer User
        $customer_A = User::factory()->create([
            'name' => 'Customer A',
            'email' => 'customer@gmail.com',
        ]);
        $customer_A->assignRole('customer');

        // Store user IDs in config for use in other seeders
        config([
            'seeder.users.admin_id' => $admin->id,
            'seeder.users.ahmad_id' => $ahmad->id,
            'seeder.users.zain_id' => $zain->id,
            'seeder.users.karman_owner_id' => $karman_telekom->id,
            'seeder.users.cephane_owner_id' => $cephane_owner->id,
            'seeder.users.zore_owner_id' => $zore_owner->id,
            'seeder.users.simex_owner_id' => $simex_owner->id,
            'seeder.users.customer_id' => $customer_A->id,
        ]);
    }
}
