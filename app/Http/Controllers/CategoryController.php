<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DanhMuc::active()->orderBy('position')->get();
        return redirect()->route('home');
    }

    public function show($slug, Request $request)
    {
        $cat = DanhMuc::where('slug',$slug)->firstOrFail();
        $q = $cat->sanPhams()->with('bienTheMacDinh','anh')->where('is_active',true);

        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $q->join('product_variants','products.id','=','product_variants.product_id')
                  ->orderBy('product_variants.price','asc');
            }
            if ($request->sort === 'price_desc') {
                $q->join('product_variants','products.id','=','product_variants.product_id')
                  ->orderBy('product_variants.price','desc');
            }
        }

        $products = $q->paginate(12)->withQueryString();
        return view('category', compact('cat','products'));
    }
}
