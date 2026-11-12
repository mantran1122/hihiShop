<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DemoUserAndRolesSeeder extends Seeder {
  public function run(): void {
    $rAdmin = Role::firstOrCreate(['name'=>'Admin']);
    $rStaff = Role::firstOrCreate(['name'=>'Staff']);
    $rCustomer = Role::firstOrCreate(['name'=>'Customer']);

    $admin = User::firstOrCreate(
      ['email'=>'admin@example.com'],
      ['name'=>'Admin Quản Trị', 'password'=>Hash::make('admin123')]
    );
    $staff = User::firstOrCreate(
      ['email'=>'staff@example.com'],
      ['name'=>'Nhân Viên', 'password'=>Hash::make('staff123')]
    );
    $customer = User::firstOrCreate(
      ['email'=>'customer@example.com'],
      ['name'=>'Khách Hàng Mẫu', 'password'=>Hash::make('customer123')]
    );

    $admin->syncRoles([$rAdmin]);
    $staff->syncRoles([$rStaff]);
    $customer->syncRoles([$rCustomer]);
  }
}
