<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Usine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usine::factory()->createMany(
            [
                'nomusine' => 'USINE DE KOUMASSI',
                'active' => 'true',
            ],
            [
                'nomusine' => 'USINE DE YAOUNDE',
                'active' => 'true'
            ],
            [
                'nomusine' => 'USINE DE GAROUA',
                'active' => 'true'
            ]
        );
    }
}
