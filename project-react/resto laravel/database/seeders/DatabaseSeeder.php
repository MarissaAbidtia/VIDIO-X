<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Baris ini bisa di-uncomment jika Anda tidak ingin event model dijalankan saat seeding
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            KategoriSeeder::class,
            MenuSeeder::class, // Pastikan MenuSeeder ada dan benar
            // Tambahkan seeder lain jika diperlukan di sini, contoh:
            // OrderSeeder::class,
            // PelangganSeeder::class,
        ]);

        // Contoh jika Anda ingin menggunakan factory untuk User
        // App\Models\User::factory(10)->create();

        // App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}