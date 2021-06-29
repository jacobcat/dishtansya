<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'name' => 'ChickenJoy',
            'available_stock' => 5
        ]);
        DB::table('products')->insert([
            'name' => 'Hamburger',
            'available_stock' => 5
        ]);
        DB::table('products')->insert([
            'name' => 'Spaghetti',
            'available_stock' => 5
        ]);
        DB::table('products')->insert([
            'name' => 'Palabok',
            'available_stock' => 5
        ]);
    }
}
