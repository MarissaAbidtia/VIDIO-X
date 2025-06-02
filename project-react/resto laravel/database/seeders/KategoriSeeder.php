<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategoris = [
            ['kategori' => 'makanan'],
            ['kategori' => 'minuman'],
            ['kategori' => 'jajan'],
            ['kategori' => 'gorengan']
        ];
        
        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}
