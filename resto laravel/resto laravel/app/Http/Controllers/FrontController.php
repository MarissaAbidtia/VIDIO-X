<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        $menus = Menu::with('kategori')->get();
        
        return view('home', compact('kategoris', 'menus'));
    }
}