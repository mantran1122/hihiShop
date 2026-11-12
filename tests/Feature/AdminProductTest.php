<?php

namespace Tests\Feature;

use App\Models\BienTheSanPham;
use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\User;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CouponSeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            DemoUserAndRolesSeeder::class,
            RolesSeeder::class,
        ]);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->customer = User::where('email', 'customer@example.com')->firstOrFail();
    }

    public function test_admin_can_open_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Thêm Sản Phẩm Mới');
    }

    public function test_admin_can_open_edit_form(): void
    {
        $product = SanPham::firstOrFail();

        $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product->id))
            ->assertOk()
            ->assertSee('Sửa Sản Phẩm')
            ->assertSee($product->name);
    }

    public function test_admin_can_create_product_with_default_variant(): void
    {
        $category = DanhMuc::firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Túi Xách Da Cao Cấp',
            'category_id' => $category->id,
            'price' => 1250000,
            'stock_qty' => 7,
            'sku' => 'BAG-TEST-01',
            'short_desc' => 'Da bò thật, may thủ công',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = SanPham::where('name', 'Túi Xách Da Cao Cấp')->first();
        $this->assertNotNull($product);

        $variant = $product->bienTheMacDinh;
        $this->assertNotNull($variant);
        $this->assertSame('BAG-TEST-01', $variant->sku);
        $this->assertSame(1250000, (int) $variant->price);
        $this->assertSame(7, (int) $variant->stock_qty);
        $this->assertTrue((bool) $product->is_active);
    }

    public function test_product_requires_name_category_price_and_stock(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [])
            ->assertSessionHasErrors(['name', 'category_id', 'price', 'stock_qty']);
    }

    public function test_sku_must_be_unique(): void
    {
        $existing = BienTheSanPham::firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Sản phẩm trùng SKU',
            'category_id' => DanhMuc::firstOrFail()->id,
            'price' => 100000,
            'stock_qty' => 1,
            'sku' => $existing->sku,
        ])->assertSessionHasErrors('sku');
    }

    public function test_image_path_must_exist_in_public_folder(): void
    {
        $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Ảnh không tồn tại',
            'category_id' => DanhMuc::firstOrFail()->id,
            'price' => 100000,
            'stock_qty' => 1,
            'image' => 'images/khong-co-that/anh.jpg',
        ])->assertSessionHasErrors('image');
    }

    public function test_admin_can_update_product_and_variant(): void
    {
        $product = SanPham::with('bienTheMacDinh')->firstOrFail();
        $sku = $product->bienTheMacDinh->sku;

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Tên đã đổi',
            'category_id' => $product->category_id,
            'price' => 2222000,
            'stock_qty' => 99,
            'sku' => $sku,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertSame('Tên đã đổi', $product->name);
        $this->assertSame(2222000, (int) $product->bienTheMacDinh->price);
        $this->assertSame(99, (int) $product->bienTheMacDinh->stock_qty);
    }

    public function test_customer_cannot_manage_products(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.products.create'))
            ->assertStatus(403);

        $this->actingAs($this->customer)
            ->post(route('admin.products.store'), [])
            ->assertStatus(403);
    }
}
