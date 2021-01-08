<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private $categories = [
        [
            'title' => '造船',
            'has_lease' => false,
        ],
        [
            'title' => '修船',
            'has_lease' => false,
        ],
        [
            'title' => '配餐',
            'has_lease' => false,
        ],
        [
            'title' => '服务商',
            'has_lease' => false,
        ],
        [
            'title' => '麦克德莫特包商',
            'has_lease' => true,
        ],
        [
            'title' => '外部单位',
            'has_lease' => true,
        ],
        [
            'title' => '二区包商',
            'has_lease' => false,
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
