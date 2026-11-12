<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder {
  public function run(): void {
    $attrs = [
      // nước hoa
      ['code'=>'concentration','name'=>'Nồng độ','type'=>'select','cat'=>'nuoc-hoa','values'=>['EDT','EDP','Parfum']],
      ['code'=>'volume_ml','name'=>'Dung tích (ml)','type'=>'number','cat'=>'nuoc-hoa'],
      ['code'=>'fragrance_family','name'=>'Nhóm hương','type'=>'select','cat'=>'nuoc-hoa','values'=>['Citrus','Floral','Woody','Oriental']],
      // giày
      ['code'=>'size_eu','name'=>'Size EU','type'=>'select','cat'=>'giay','values'=>['38','39','40','41','42','43','44']],
      ['code'=>'material','name'=>'Chất liệu','type'=>'select','cat'=>'giay','values'=>['Leather','Canvas','Mesh']],
      // mắt kính
      ['code'=>'frame_shape','name'=>'Kiểu dáng gọng','type'=>'select','cat'=>'mat-kinh','values'=>['Aviator','Wayfarer','Round','Square']],
      ['code'=>'lens_width','name'=>'Chiều ngang tròng (mm)','type'=>'number','cat'=>'mat-kinh'],
      // đồng hồ
      ['code'=>'movement','name'=>'Bộ máy','type'=>'select','cat'=>'dong-ho','values'=>['Quartz','Automatic']],
      ['code'=>'water_res_atm','name'=>'Chống nước (ATM)','type'=>'number','cat'=>'dong-ho'],
    ];

    $catIds = DB::table('categories')->pluck('id','slug');

    foreach ($attrs as $a) {
      $attrId = DB::table('attributes')->updateOrInsert(['code'=>$a['code']],[
        'name'=>$a['name'],'type'=>$a['type'],'created_at'=>now(),'updated_at'=>now()
      ]) ? DB::table('attributes')->where('code',$a['code'])->value('id') : null;

      if (!empty($a['values'])) {
        foreach ($a['values'] as $val) {
          DB::table('attribute_values')->updateOrInsert(
            ['attribute_id'=>$attrId,'value'=>$val],
            ['created_at'=>now(),'updated_at'=>now()]
          );
        }
      }
      // map attribute với category
      DB::table('attribute_category')->updateOrInsert([
        'attribute_id'=>$attrId, 'category_id'=>$catIds[$a['cat']] ?? null
      ], ['created_at'=>now(),'updated_at'=>now()]);
    }
  }
}
