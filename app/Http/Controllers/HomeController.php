<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;

class HomeController extends Controller
{
    public function index()
    {
        $categories = DanhMuc::active()->orderBy('position')->get();
        $products = SanPham::active()->with('bienTheMacDinh','anh','bienThes')->latest()->take(12)->get();
        return view('home', compact('categories','products'));
    }
}
