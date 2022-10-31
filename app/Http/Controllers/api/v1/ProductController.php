<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests\api\v1\ProductStoreRequest;
use App\Http\Requests\api\v1\ProductUpdateRequest;

use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $products = $user->products()->get();
        return response()->json(
            ['data' => ProductResource::collection(
                    $products->loadMissing('owner')
                )
            ],
            200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request, User $user)
    {
        $id = Auth::user()->id;
        if ($user->id == $id){
            $name = $request->input('name');
            $price = $request->input('price');
            $expiration = $request->input('expiration');
            $owner_id = $id;

            $product = Product::create([
                'name'=>$name,
                'price'=>$price,
                'expiration'=>$expiration,
                'user_id'=>$owner_id
            ]);

            return (new ProductResource($product))
                ->response()
                ->setStatusCode(201);
        } else {
            return response()
                ->json(['data' => 'You cannot create products to other users.'])
                ->setStatusCode(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Product $product)
    {
        if ($product->user_id == $user->id)
        {
            return (new ProductResource($product
                    ->loadMissing('owner')
                )
            )
            ->response()
            ->setStatusCode(200);
        } else {
            return response()
                ->json(['data' => 'The product id '.$product->id.' does not belong to the user id '.$user->id])
                ->setStatusCode(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, User $user, Product $product)
    {
        $id = Auth::user()->id;
        if ($user->id == $id){
            if ($product->user_id == $user->id)
            {
                if ($request->exists('name')){
                    $name = $request->input('name');
                    $product->name = $name;
                }
                if ($request->exists('price')){
                    $price = $request->input('price');
                    $product->price = $price;
                }
                if ($request->exists('expiration')){
                    $expiration = $request->input('expiration');
                    $product->expiration = $expiration;
                }
                $product->save();
                return (new ProductResource($product))
                    ->response()
                    ->setStatusCode(200);
            } else {
                return response()
                    ->json(['data' => 'The product id '.$product->id.' does not belong to the user id '.$user->id])
                    ->setStatusCode(404);
            }
        } else {
            return response()
                ->json(['data' => 'You cannot update products to other users.'])
                ->setStatusCode(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Product $product)
    {
        $id = Auth::user()->id;
        if ($user->id == $id){
            if ($product->user_id == $user->id)
            {
                $product->delete();
                return response(null, 204);
            } else {
                return response()
                    ->json(['data' => 'The product id '.$product->id.' does not belong to the user id '.$user->id])
                    ->setStatusCode(404);
            }
        } else {
            return response()
                ->json(['data' => 'You cannot delete products to other users.'])
                ->setStatusCode(403);
        }
    }
}
