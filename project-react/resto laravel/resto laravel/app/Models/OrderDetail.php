<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'idorderdetail';
    protected $fillable = ['idorder', 'idmenu', 'jumlah', 'hargajual'];
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'idorder');
    }
    
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idmenu');
    }
}
