<?php

namespace Tests\Feature;

use App\Http\Controllers\Backend\ProductController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    public function test_store_persists_translated_product_fields(): void
    {
        $response = $this->post(route('admin.products.store'), [
            'name' => 'Controller Stored Product',
            'description' => 'Controller stored description',
            'short_description' => 'Short controller description',
            'meta_title' => 'Stored Meta Title',
            'meta_description' => 'Stored Meta Description',
            'meta_keywords' => 'stored,keywords',
            'slug' => 'controller-stored-product',
            'is_active' => '1',
            'in_stock' => '1',
            'manage_stock' => '0',
            'vendor_status' => 'approved',
        ]);

        $product = Product::firstOrFail();

        $response->assertRedirect(route('admin.products.index'));
        $this->assertSame('Controller Stored Product', $product->fresh()->name);
        $this->assertSame('Controller stored description', $product->fresh()->description);
        $this->assertSame('Short controller description', $product->fresh()->short_description);
        $this->assertSame('Stored Meta Title', $product->fresh()->meta_title);
        $this->assertSame('Stored Meta Description', $product->fresh()->meta_description);
        $this->assertSame('stored,keywords', $product->fresh()->meta_keywords);
    }

    public function test_search_finds_products_by_translated_name(): void
    {
        $product = Product::create([
            'slug' => 'translated-search-product',
            'is_active' => true,
            'in_stock' => true,
            'manage_stock' => false,
            'vendor_status' => 'approved',
        ]);

        $product->setTranslation('name', 'Translated Search Product');
        $product->setTranslation('description', 'Searchable translated description');

        $response = $this->getJson(route('admin.products.search', ['q' => 'Translated Search']));

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => 'Translated Search Product',
            ]);
    }

    public function test_duplicate_creates_a_product_copy_with_translations(): void
    {
        $product = Product::create([
            'slug' => 'duplicated-product-source',
            'is_active' => true,
            'in_stock' => true,
            'manage_stock' => false,
            'vendor_status' => 'approved',
        ]);

        $product->setTranslation('name', 'Duplicated Product Name');
        $product->setTranslation('description', 'Duplicated Product Description');

        $response = app(ProductController::class)->duplicate($product);

        $duplicate = Product::whereKeyNot($product->id)->firstOrFail();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertNotSame($product->slug, $duplicate->slug);
        $this->assertSame('Duplicated Product Name', $duplicate->fresh()->name);
        $this->assertSame('Duplicated Product Description', $duplicate->fresh()->description);
    }
}
