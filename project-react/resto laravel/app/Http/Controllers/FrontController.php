<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        $menus = Menu::with('kategori')->get();
        
        return view('home', compact('kategoris', 'menus'));
    }
    
    public function kategori($kategori)
    {
        $kategoris = Kategori::all();
        $kategoriData = Kategori::where('kategori', $kategori)->first();
        
        if (!$kategoriData) {
            return redirect('/');
        }
        
        $menus = Menu::where('idkategori', $kategoriData->idkategori)->get();
        
        return view('home', compact('kategoris', 'menus', 'kategori'));
    }
    
    public function beli($id)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Silakan login terlebih dahulu untuk membeli');
        }
        
        // Logika untuk menambahkan ke keranjang akan ditambahkan nanti
        return redirect('/cart');
    }
}