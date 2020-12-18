<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private $categories = [
        [
            'title' => '修船',
            'has_rent' => false,
        ],
        [
            'title' => '造船',
            'has_rent' => false,
        ],
        [
            'title' => '青武包商公司',
            'has_rent' => false,
        ],
        [
            'title' => '外部单位',
            'has_rent' => true,
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->categories as $category) {
            Category::create($category);
        }
    }
}