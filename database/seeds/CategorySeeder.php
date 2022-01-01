<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Category::class)->create([
            'id'      => 1,
            'category'    => '新規事業立案',
        ]);

        factory(Category::class)->create([
            'id'      => 2,
            'category'    => '営業戦略策定',
        ]);
        factory(Category::class)->create([
            'id'      => 3,
            'category'    => 'BPR',
        ]);
    }
}
