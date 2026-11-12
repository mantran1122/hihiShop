<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder {
  public function run(): void {
    $cat = DB::table('categories')->pluck('id','slug');

    $demo = [
      // Nước hoa
      [
        'cat'=>'nuoc-hoa',
        'name'=>'Nước hoa Citrus Fresh Eau De Parfum',
        'price'=>1590000,
        'image'=>'images/nuocHoa/Burberry London for women Edp/z6985538024915_fec532e7da2f5e46bbf61ead2e885f1b.jpg',
        'variants'=>[
          ['sku'=>'PF-CIT-50','price'=>1590000,'stock'=>50,'attrs'=>['concentration'=>'EDT','volume_ml'=>50,'fragrance_family'=>'Citrus']],
          ['sku'=>'PF-CIT-100','price'=>2290000,'stock'=>40,'attrs'=>['concentration'=>'EDP','volume_ml'=>100,'fragrance_family'=>'Citrus']],
        ]
      ],
      // Giày
      [
        'cat'=>'giay',
        'name'=>'Giày Sneaker Classic Canvas White',
        'price'=>990000,
        'image'=>null, // tự động lấy ảnh trong public/images/giay (xem resolveImage)
        'variants'=>[
          ['sku'=>'SH-CL-41','price'=>990000,'stock'=>20,'attrs'=>['size_eu'=>'41','material'=>'Canvas']],
          ['sku'=>'SH-CL-42','price'=>990000,'stock'=>25,'attrs'=>['size_eu'=>'42','material'=>'Canvas']],
        ]
      ],
      // Mắt kính
      [
        'cat'=>'mat-kinh',
        'name'=>'Kính mát Thời Trang Wayfarer UV400',
        'price'=>790000,
        'image'=>'images/matKinh/gant/z6985374841258_96ac95d7532f3dadc8e9dd96500ae0ca.jpg',
        'variants'=>[
          ['sku'=>'GL-WF-STD','price'=>790000,'stock'=>30,'attrs'=>['frame_shape'=>'Wayfarer','lens_width'=>52]],
        ]
      ],
      // Đồng hồ
      [
        'cat'=>'dong-ho',
        'name'=>'Đồng hồ Automatic Sapphire Chống Nước',
        'price'=>3590000,
        'image'=>'images/DongHo/Fleury D1496BYI  Women\'s/z6985341987281_fc4d6c76a2da9edfafad97a232cef0a4.jpg',
        'variants'=>[
          ['sku'=>'WT-AU-40','price'=>3590000,'stock'=>15,'attrs'=>['movement'=>'Automatic','water_res_atm'=>10]],
        ]
      ],
    ];

    foreach ($demo as &$item) {
      if (empty($item['image'])) {
        $item['image'] = $this->resolveImage($item['cat']);
      }
    }
    unset($item);

    foreach ($demo as $p) {
      $pid = DB::table('products')->insertGetId([
        'category_id'=>$cat[$p['cat']],
        'name'=>$p['name'],
        'slug'=>Str::slug($p['name']).'-'.Str::random(5),
        'short_desc'=>'Sản phẩm cao cấp chính hãng từ HIHI Shop. Đầy đủ tem bảo hành và hộp đựng.',
        'description'=>'Sản phẩm mang thiết kế hiện đại, tinh tế và sang trọng. Phù hợp cho mọi dịp đi làm, dạo phố hoặc dự tiệc. Cam kết 100% chính hãng, hỗ trợ đổi trả trong vòng 7 ngày.',
        'is_active'=>true,
        'created_at'=>now(),'updated_at'=>now()
      ]);

      if (!empty($p['image'])) {
        DB::table('product_images')->insert([
          'product_id' => $pid,
          'path' => $p['image'],
          'is_cover' => true,
          'sort' => 0,
          'created_at' => now(),
          'updated_at' => now()
        ]);
      }

      foreach ($p['variants'] as $i=>$v) {
        $vid = DB::table('product_variants')->insertGetId([
          'product_id'=>$pid, 'sku'=>$v['sku'],
          'price'=>$v['price'], 'compare_price'=>null, 'cost'=>null,
          'stock_qty'=>$v['stock'], 'is_default'=>$i===0, 'created_at'=>now(),'updated_at'=>now()
        ]);

        foreach ($v['attrs'] as $code=>$val) {
          $attr = DB::table('attributes')->where('code',$code)->first();
          if(!$attr) continue;
          $payload = [
            'product_id'=>$pid,'attribute_id'=>$attr->id,
            'created_at'=>now(),'updated_at'=>now()
          ];
          if($attr->type==='number'){
            $payload['value_number'] = is_numeric($val)? $val : null;
          } elseif($attr->type==='select'){
            $valId = DB::table('attribute_values')->where('attribute_id',$attr->id)->where('value',$val)->value('id');
            $payload['attribute_value_id'] = $valId;
          } else {
            $payload['value_text'] = (string)$val;
          }
          DB::table('product_attribute_values')->updateOrInsert(
            ['product_id'=>$pid,'attribute_id'=>$attr->id],
            $payload
          );
        }
      }
    }
  }

  /**
   * Tìm ảnh đại diện thật trong public/images/<thư mục>; nếu chưa có thì trả về ảnh minh hoạ.
   * Thả ảnh .jpg/.png vào public/images/giay rồi seed lại là sản phẩm tự có ảnh.
   */
  private function resolveImage(string $categorySlug): string
  {
    $folders = [
      'nuoc-hoa' => 'nuocHoa',
      'giay' => 'giay',
      'mat-kinh' => 'matKinh',
      'dong-ho' => 'DongHo',
    ];

    $folder = $folders[$categorySlug] ?? null;

    if ($folder) {
      $dir = public_path('images/'.$folder);

      if (is_dir($dir)) {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
          if (!$file->isFile()) {
            continue;
          }
          if (!in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            continue;
          }
          $files[] = $file->getPathname();
        }

        sort($files);

        if (!empty($files)) {
          $relative = str_replace(public_path().DIRECTORY_SEPARATOR, '', $files[0]);
          return str_replace('\\', '/', $relative);
        }
      }

      $placeholder = 'images/placeholder-'.$categorySlug.'.svg';
      if (is_file(public_path($placeholder))) {
        return $placeholder;
      }
    }

    return 'images/placeholder.svg';
  }
}
