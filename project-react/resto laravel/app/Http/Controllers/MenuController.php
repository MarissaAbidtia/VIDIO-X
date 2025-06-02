<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('pages.home', compact('menus'));
    }

    public function makanan()
    {
        $menus = Menu::where('kategori_id', 1)->get();
        return view('pages.makanan', compact('menus'));
    }

    public function minuman()
    {
        $menus = Menu::where('kategori_id', 2)->get();
        return view('pages.minuman', compact('menus'));
    }

    public function jajan()
    {
        $menus = Menu::where('kategori_id', 3)->get();
        return view('pages.jajan', compact('menus'));
    }

    public function gorengan()
    {
        $menus = Menu::where('kategori_id', 4)->get();
        return view('pages.gorengan', compact('menus'));
    }
}
