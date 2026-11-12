<?php

namespace Tests\Feature;

use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\ThuongHieu;
use App\Models\User;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogTest extends TestCase
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
            DemoUserAndRolesSeeder::class,
            RolesSeeder::class,
        ]);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->customer = User::where('email', 'customer@example.com')->firstOrFail();
    }

    public function test_admin_can_view_catalog_pages(): void
    {
        $this->actingAs($this->admin)->get(route('admin.categories.index'))
            ->assertOk()->assertSee('Danh Sách Danh Mục');

        $this->actingAs($this->admin)->get(route('admin.categories.create'))
            ->assertOk()->assertSee('Thêm Danh Mục Mới');

        $this->actingAs($this->admin)->get(route('admin.brands.index'))
            ->assertOk()->assertSee('Danh Sách Thương Hiệu');

        $this->actingAs($this->admin)->get(route('admin.brands.create'))
            ->assertOk()->assertSee('Thêm Thương Hiệu Mới');
    }

    public function test_admin_can_create_category_with_auto_slug(): void
    {
        $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Túi xách',
            'position' => 5,
            'is_active' => 1,
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Túi xách',
            'slug' => 'tui-xach',
            'position' => 5,
        ]);
    }

    public function test_duplicate_category_slug_is_rejected(): void
    {
        $existing = DanhMuc::firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Danh mục khác',
            'slug' => $existing->slug,
        ])->assertSessionHasErrors('slug');
    }

    public function test_category_with_products_cannot_be_hidden(): void
    {
        $category = DanhMuc::has('sanPhams')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.categories.toggle', $category->id))
            ->assertSessionHas('error');

        $this->assertTrue((bool) $category->fresh()->is_active);
    }

    public function test_admin_can_create_and_update_brand(): void
    {
        $this->actingAs($this->admin)->post(route('admin.brands.store'), [
            'name' => 'Burberry',
            'origin' => 'Anh',
        ])->assertRedirect(route('admin.brands.index'));

        $brand = ThuongHieu::where('name', 'Burberry')->firstOrFail();
        $this->assertSame('burberry', $brand->slug);

        $this->actingAs($this->admin)->put(route('admin.brands.update', $brand->id), [
            'name' => 'Burberry UK',
            'origin' => 'Vương quốc Anh',
        ])->assertRedirect(route('admin.brands.index'));

        $this->assertSame('Burberry UK', $brand->fresh()->name);
    }

    public function test_brand_with_products_cannot_be_deleted(): void
    {
        $brand = ThuongHieu::create(['name' => 'Brand Có Sản Phẩm', 'slug' => 'brand-co-san-pham']);
        SanPham::firstOrFail()->update(['brand_id' => $brand->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.brands.destroy', $brand->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('brands', ['id' => $brand->id]);
    }

    public function test_empty_brand_can_be_deleted(): void
    {
        $brand = ThuongHieu::create(['name' => 'Brand Trống', 'slug' => 'brand-trong']);

        $this->actingAs($this->admin)
            ->delete(route('admin.brands.destroy', $brand->id))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    public function test_customer_cannot_manage_catalog(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.categories.index'))
            ->assertStatus(403);

        $this->actingAs($this->customer)
            ->post(route('admin.brands.store'), [])
            ->assertStatus(403);
    }
}
