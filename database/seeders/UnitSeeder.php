<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnitSeeder extends Seeder
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
        DB::table('units')->insert([
            [
                'name'=> 'Unidad',
                'value' => 'und',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name'=> 'Libra',
                'value' => 'lb',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'litro',
                'value' => 'lt',
                'created_at' => $date,
                'updated_at' => $date
            ]
        ]);
    }
}
