<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tpengguna;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Tpengguna::create([
            'kodepengguna' => 'U-001',
            'namapengguna' => 'owner',
            'katakunci' => bcrypt('pakandre'),
            'status' => 'aktif',
            'aktif' => 1,
        ]);
    }
}
