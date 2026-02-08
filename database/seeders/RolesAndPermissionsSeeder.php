<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إعادة تعيين الكاش
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. تعريف الأدوار (Roles)
        $roles = [
            'DevTest',      // صلاحيات مطلقة (مخفي)
            'SuperAdmin',   // إدارة النظام
            'Owner',        // مالك الصالون
            'Manager',      // مدير فرع
            'Employee',     // موظف
            // 'Customer'   // لا يحتاج دور في لوحة التحكم، يمكن إضافته إذا لزم الأمر للـ API
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 3. إنشاء حساب DevTest (يتم قراءة البيانات من .env للأمان)
        $devUser = User::firstOrCreate(
            ['email' => env('DEV_TEST_EMAIL', 'dev@system.local')],
            [
                'name' => 'Dev System',
                'password' => Hash::make(env('DEV_TEST_PASSWORD', 'password')),
            ]
        );
        $devUser->assignRole('DevTest');

        // 4. إنشاء SuperAdmin افتراضي
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->assignRole('SuperAdmin');
    }
}
