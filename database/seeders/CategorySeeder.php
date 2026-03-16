<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'President', 'description' => 'Vote for the next President', 'order' => 1],
            ['name' => 'Vice President', 'description' => 'Vote for the next Vice President', 'order' => 2],
            ['name' => 'Senator', 'description' => 'Vote for Senator', 'order' => 3],
            ['name' => 'Governor', 'description' => 'Vote for Governor', 'order' => 4],
            ['name' => 'Mayor', 'description' => 'Vote for Mayor', 'order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
