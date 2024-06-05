<?php

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_product()
    {
        // Arrange
        $product            = Product::factory()->create(); // Create a product in the database
        $productRepository  = new ProductRepository();

        // Act
        $actualProduct = $productRepository->find($product->id);

        // Assert
        $this->assertEquals($product->id, $actualProduct->id);
        $this->assertEquals($product->name, $actualProduct->name);
    }
}
