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

    // ---------------------------------------------------------------------
    // 1. Tài khoản admin thật cho production.
    //    Đặt ADMIN_EMAIL + ADMIN_PASSWORD trong biến môi trường rồi chạy seeder.
    // ---------------------------------------------------------------------
    $adminEmail = env('ADMIN_EMAIL');
    $adminPassword = env('ADMIN_PASSWORD');

    if ($adminEmail && $adminPassword) {
      $admin = User::firstOrCreate(
        ['email' => $adminEmail],
        ['name' => 'Quản Trị Viên', 'password' => Hash::make($adminPassword)]
      );
      $admin->syncRoles([$rAdmin]);
    }

    // ---------------------------------------------------------------------
    // 2. Tài khoản demo: chỉ tạo ở local/testing, hoặc khi bật SEED_DEMO_USERS=true
    //    và truyền DEMO_USER_PASSWORD. Production mặc định KHÔNG tạo để tránh
    //    lộ tài khoản admin mặc định trên môi trường công khai.
    // ---------------------------------------------------------------------
    $demoPassword = env('DEMO_USER_PASSWORD');

    if (!$demoPassword && app()->environment(['local', 'testing'])) {
      $demoPassword = 'admin123';
    }

    if (!$demoPassword) {
      return;
    }

    $demoAdmin = User::firstOrCreate(
      ['email'=>'admin@example.com'],
      ['name'=>'Admin Quản Trị', 'password'=>Hash::make($demoPassword)]
    );
    $staff = User::firstOrCreate(
      ['email'=>'staff@example.com'],
      ['name'=>'Nhân Viên', 'password'=>Hash::make($demoPassword)]
    );
    $customer = User::firstOrCreate(
      ['email'=>'customer@example.com'],
      ['name'=>'Khách Hàng Mẫu', 'password'=>Hash::make($demoPassword)]
    );

    $demoAdmin->syncRoles([$rAdmin]);
    $staff->syncRoles([$rStaff]);
    $customer->syncRoles([$rCustomer]);
  }
}
