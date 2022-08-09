<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();
        //Create  claim document
        DB::table('products')->insert([
            [
                'name' => 'Tomate',
                'price' => 25,
                'unit_id' => 2,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Sandia',
                'price' => 50,
                'unit_id' => 1,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Manzana',
                'price' => 70,
                'unit_id' => 2,
                'created_at' => $date,
                'updated_at' => $date
            ],
        ]);
    }
}
