<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->delete();
        Product::create([
            'name' => 'Papitas',
            'price' => 100,
            'expiration' => Carbon::parse('2022-10-15'),
            'user_id' => User::all()->random()->id,
        ]);
        Product::create([
            'name' => 'Gomitas',
            'price' => 200,
            'expiration' => Carbon::parse('2022-12-03'),
            'user_id' => User::all()->random()->id,
        ]);
        Product::create([
            'name' => 'Chocolatas',
            'price' => 300,
            'expiration' => Carbon::parse('2022-12-22'),
            'user_id' => User::all()->random()->id,
        ]);
    }
}
