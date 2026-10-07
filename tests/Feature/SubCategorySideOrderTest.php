<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SubCategory;
use App\Services\FrontService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubCategorySideOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_urutan_sub_kategori_di_side_menu_mengikuti_pengaturan_admin(): void
    {
        $category = Category::create([
            'category_uri' => 'himpunan-peraturan',
            'category_name' => 'Himpunan Peraturan',
            'category_show' => 'yes',
        ]);

        // create_date sengaja dibuat terbalik dari urutan: yang paling baru
        // punya urutan terakhir. Dengan begitu test ini membuktikan side menu
        // memakai kolom urutan, bukan create_date.
        SubCategory::create([
            'category_id' => $category->category_id,
            'sub_category_uri' => 'pertama',
            'sub_category_name' => 'Pertama',
            'sub_category_show' => 'yes',
            'create_date' => '2021-01-01',
            'urutan' => 1,
        ]);

        SubCategory::create([
            'category_id' => $category->category_id,
            'sub_category_uri' => 'kedua',
            'sub_category_name' => 'Kedua',
            'sub_category_show' => 'yes',
            'create_date' => '2024-01-01',
            'urutan' => 2,
        ]);

        $names = array_column(
            app(FrontService::class)->sideSubCategories($category->category_id),
            'sub_category_name'
        );

        $this->assertSame(['Pertama', 'Kedua'], $names);
    }
}
