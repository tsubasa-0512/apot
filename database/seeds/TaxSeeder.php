<?php

use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Tax::class)->create([
            'id'      => 1,
            'tax'    => '消費税10%',
            'tax_rate'    => 0.1,
        ]);
    }
}
