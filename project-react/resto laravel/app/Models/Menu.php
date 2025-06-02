<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'idmenu';
    protected $fillable = ['idkategori', 'menu', 'gambar', 'deskripsi', 'harga'];
    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idkategori');
    }
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'idmenu');
    }
}
