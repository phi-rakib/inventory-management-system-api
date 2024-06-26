<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'account-list',
            'account-create',
            'account-edit',
            'account-delete',
            'account-restore',
            'account-force-delete',
            'deposit-category-list',
            'deposit-category-create',
            'deposit-category-edit',
            'deposit-category-delete',
            'deposit-category-restore',
            'deposit-category-force-delete',
            'payment-method-list',
            'payment-method-create',
            'payment-method-edit',
            'payment-method-delete',
            'payment-method-restore',
            'payment-method-force-delete',
            'deposit-list',
            'deposit-create',
            'deposit-edit',
            'deposit-delete',
            'deposit-restore',
            'deposit-force-delete',
            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',
            'expense-category-restore',
            'expense-category-force-delete',
            'expense-list',
            'expense-create',
            'expense-edit',
            'expense-delete',
            'expense-restore',
            'expense-force-delete',
            'brand-list',
            'brand-create',
            'brand-edit',
            'brand-delete',
            'brand-restore',
            'brand-force-delete',
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',
            'category-restore',
            'category-force-delete',
            'unit-type-list',
            'unit-type-create',
            'unit-type-edit',
            'unit-type-delete',
            'unit-type-restore',
            'unit-type-force-delete',
            'attribute-list',
            'attribute-create',
            'attribute-edit',
            'attribute-delete',
            'attribute-restore',
            'attribute-force-delete',
            'attribute-value-list',
            'attribute-value-create',
            'attribute-value-edit',
            'attribute-value-delete',
            'attribute-value-restore',
            'attribute-value-force-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'product-restore',
            'product-force-delete',
            'warehouse-list',
            'warehouse-create',
            'warehouse-edit',
            'warehouse-delete',
            'warehouse-restore',
            'warehouse-force-delete',
            'adjustment-list',
            'adjustment-create',
            'adjustment-edit',
            'adjustment-delete',
            'adjustment-restore',
            'adjustment-force-delete',
            'supplier-list',
            'supplier-create',
            'supplier-edit',
            'supplier-delete',
            'supplier-restore',
            'supplier-force-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $user = User::updateOrCreate(
            [
                'email' => 'mdrakibulhaider.int@gmail.com',
            ],
            [
                'name' => 'Rakibul Haider',
                'password' => Hash::make('password'),
            ],
        );

        $role = Role::updateOrCreate(
            ['name' => 'Supplier'],
            [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $role = Role::updateOrCreate(
            ['name' => 'Admin'],
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
