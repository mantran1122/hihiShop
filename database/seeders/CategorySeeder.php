<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name'=>'Nước hoa','slug'=>'nuoc-hoa'],
            ['name'=>'Giày','slug'=>'giay'],
            ['name'=>'Mắt kính','slug'=>'mat-kinh'],
            ['name'=>'Đồng hồ','slug'=>'dong-ho'],
        ];
        foreach ($cats as $i => $c) {
            DB::table('categories')->updateOrInsert(['slug'=>$c['slug']], [
                'name'=>$c['name'],
                'position'=>$i+1,
                'is_active'=>true,
                'created_at'=>now(),'updated_at'=>now(),
            ]);
        }
    }
}
