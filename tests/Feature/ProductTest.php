<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class ProductTest extends TestCase
{

    use RefreshDatabase;

    public function test_post_valid_product(){
        $this->seed();

        $product = Product::factory()->make();
        $name = $product->name;
        $price = $product->price;
        $expiration = $product->expiration->toDateString();
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'name' => $name,
                'price' => $price,
                'expiration' => $expiration
            ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => $name,
                    'price' => $price,
                    'expiration' => $expiration,
                    'owner' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]
            ]);

        //$response->dd();
    }

    public function test_post_product_to_other_users(){
        $this->seed();

        $users = User::take(2)->get();

        $product = Product::factory()->make();
        $name = $product->name;
        $price = $product->price;
        $expiration = $product->expiration->toDateString();
        $user = $users->first();

        $requester = $users->last();

        $response = $this->actingAs($requester)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user->id/products", [
                'name' => $name,
                'price' => $price,
                'expiration' => $expiration
            ])
            ->assertStatus(403)
            ->assertJson(['data' => 'You cannot create products to other users.']);

        //$response->dd();
    }

    public function test_post_product_missing_name_field(){
        $this->seed();

        $product = Product::factory()->make();
        $price = $product->price;
        $expiration = $product->expiration->toDateString();
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'price' => $price,
                'expiration' => $expiration
            ])
            ->assertStatus(422)
            ->assertJson([
                'name' => [
                    "The name field is required."
                ]
            ]);

        //$response->dd();
    }

    public function test_post_product_missing_price_field(){
        $this->seed();

        $product = Product::factory()->make();
        $name = $product->name;
        $expiration = $product->expiration->toDateString();
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'name' => $name,
                'expiration' => $expiration
            ])
            ->assertStatus(422)
            ->assertJson([
                'price' => [
                    "The price field is required."
                ]
            ]);

        //$response->dd();
    }

    public function test_post_product_missing_expiration_field(){
        $this->seed();

        $product = Product::factory()->make();
        $name = $product->name;
        $price = $product->price;
        $expiration = $product->expiration->toDateString();
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'name' => $name,
                'price' => $price,
                'expiration' => $expiration
            ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => $name,
                    'price' => $price,
                    'expiration' => $expiration,
                    'owner' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]
            ]);

        //$response->dd();
    }

    public function test_post_product_with_non_unique_name(){
        $this->seed();

        $product = Product::factory()->make();
        $name = Product::take(1)->get()->first()->name;
        $price = $product->price;
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'name' => $name,
                'price' => $price
            ])
            ->assertStatus(422)
            ->assertJson([
                'name' => [
                    "The name has already been taken."
                ]
            ]);

        //$response->dd();
    }

    public function test_post_product_with_price_above_accepted_range(){
        $this->seed();

        $product = Product::factory()->make();
        $name = $product->name;
        $price = 1000001;
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'name' => $name,
                'price' => $price
            ])
            ->assertStatus(422)
            ->assertJson([
                'price' => [
                    "The price must not be greater than 1000000."
                ]
            ]);

        //$response->dd();
    }

    public function test_post_product_with_price_below_accepted_range(){
        $this->seed();

        $product = Product::factory()->make();
        $name = $product->name;
        $price = 9;
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'name' => $name,
                'price' => $price
            ])
            ->assertStatus(422)
            ->assertJson([
                'price' => [
                    "The price must be at least 10."
                ]
            ]);

        //$response->dd();
    }

    public function test_post_product_with_date_before_today(){
        $this->seed();

        $product = Product::factory()->make();
        $name = $product->name;
        $price = $product->price;
        $expiration = Carbon::parse(date('Y-m-d',(strtotime ( '-1 day' , strtotime (Carbon::now()) ) )))->toDateString();
        $user_id = $product->user_id;
        $user = $product->owner()->get()->first();

        $response = $this->actingAs($user)
            ->withHeaders(['accept' => 'application/json'])
            ->post("/api/v1/users/$user_id/products", [
                'name' => $name,
                'price' => $price,
                'expiration' => $expiration
            ])
            ->assertStatus(422)
            ->assertJson([
                'expiration' => [
                    "The expiration must be a date after today."
                ]
            ]);

        //$response->dd();
    }
}
