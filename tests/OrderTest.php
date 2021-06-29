<?php

use App\Models\Product;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class OrderTest extends TestCase
{
    use DatabaseTransactions;       // use database transactions so inserts are not committed to the database

    /**
     * Positive test: Successful order
     */
    public function testSuccessfulOrder() {
        // Create a sample product
        $product = Product::factory()->create();

        // Create a user
        $user = User::factory()->create();

        // Then call the Order API with a valid product and quantity of 1
        // and check the following:
        // - Status code is 201
        // - Status message is "You have successfully ordered this product."
        // - Product quantity has decreased by 1
        $this->actingAs($user)->json('POST', '/order', ['product_id' => $product->id, 'quantity' => 1])
            ->seeStatusCode(201)
            ->seeJson(['message' => 'You have successfully ordered this product.']);
    }

    /**
     * Negative test: Unsuccessful Order due to unavailable stock
     */
    public function testOrderUnavailableStock() {
        // Create a sample product
        $product = Product::factory()->create();

        // Create a user
        $user = User::factory()->create();

        // Then call the Order API with a valid product and quantity of 6
        // and check the following:
        // - Status code is 400
        // - Status message is "You have successfully ordered this product."
        // - Product quantity has decreased by 1
        $this->actingAs($user)->json('POST', '/order', ['product_id' => $product->id, 'quantity' => 6])
            ->seeStatusCode(400)
            ->seeJson(['message' => 'Failed to order this product due to unavailability of the stock']);
    }
}
