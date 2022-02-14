<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info (title="Products API", version="1.0")
 */
class ProductApiController extends Controller
{
    private $createRules = [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'image' => ['required', 'string'],
        'brand' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric'],
        'price_sale' => ['required', 'numeric'],
        'category' => ['required', 'string', 'max:255'],
        'stock' => ['required', 'integer']
    ];

    /**
     * @OA\Post (
     *     path="/api/product",
     *     summary="Create product",
     *     tags={"Products"},
     *     @OA\RequestBody (
     *         required=true,
     *         description="Product details",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="name",
     *                 type="string",
     *                 example="MacBook Pro 13.3 Retina [MYD82] M1 Chip 256 GB - Space Gray"
     *             ),
     *             @OA\Property (
     *                 property="description",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property (
     *                 property="image",
     *                 type="string",
     *                 example="apple.com/v/macbook-pro/ac/images/overview/hero_13__d1tfa5zby7e6_large_2x.jpg"
     *             ),
     *             @OA\Property (
     *                 property="brand",
     *                 type="string",
     *                 example="Apple"
     *             ),
     *             @OA\Property (
     *                 property="price",
     *                 type="float",
     *                 example=2000
     *             ),
     *             @OA\Property (
     *                 property="price_sale",
     *                 type="float",
     *                 example=1950
     *             ),
     *             @OA\Property (
     *                 property="category",
     *                 type="string",
     *                 example="Macbook Pro"
     *             ),
     *             @OA\Property (
     *                 property="stock",
     *                 type="integer",
     *                 example=5
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=200,
     *         description="Success",
     *         @OA\Header(
     *             header="Location",
     *             description="URL for the details of the created product",
     *             @OA\Schema (
     *                 type="String",
     *                 example="/api/product/1"
     *             )
     *         ),
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property (
     *                 property="product",
     *                 type="array",
     *                 @OA\Items (
     *                     type="object",
     *                     @OA\Property (
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property (
     *                         property="name",
     *                         type="string",
     *                         example="MacBook Pro 13.3 Retina [MYD82] M1 Chip 256 GB - Space Gray"
     *                     ),
     *                     @OA\Property (
     *                         property="description",
     *                         type="string",
     *                         example=""
     *                     ),
     *                     @OA\Property (
     *                         property="image",
     *                         type="string",
     *                         example="apple.com/v/macbook-pro/ac/images/overview/hero_13__d1tfa5zby7e6_large_2x.jpg"
     *                     ),
     *                     @OA\Property (
     *                         property="brand",
     *                         type="string",
     *                         example="Apple"
     *                     ),
     *                     @OA\Property (
     *                         property="price",
     *                         type="float",
     *                         example=2000
     *                     ),
     *                     @OA\Property (
     *                         property="price_sale",
     *                         type="float",
     *                         example=1950
     *                     ),
     *                     @OA\Property (
     *                         property="category",
     *                         type="string",
     *                         example="Macbook Pro"
     *                     ),
     *                     @OA\Property (
     *                         property="stock",
     *                         type="integer",
     *                         example=5
     *                     ),
     *                     @OA\Property (
     *                         property="created_at",
     *                         type="string",
     *                         example="2022-02-14 12:18:34"
     *                     ),
     *                     @OA\Property (
     *                         property="updated_at",
     *                         type="string",
     *                         example="2022-02-14 12:18:34"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items (
     *                     type="string",
     *                     example={
     *                         "The name field is required.",
     *                         "The brand field is required."
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Error saving product"
     *             )
     *         )
     *     )
     * )
     *
     * Persist incoming product into the database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), 1);
        if (!$data)
            return response()->json([
                'status' => ' error',
                'errors' => ['Request body is not a valid JSON']
            ], 400);

        $validate = Validator::make($data, $this->createRules);
        if ($validate->fails())
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()->all()
            ], 400);

       try {
           $product = Product::create($data);

           return response()->json([
               'status' => 'success',
               'product' => $product
           ], 201, ['Location' => route('productsApi.get', $product->id)]);
       } catch (\Exception $e) {
           Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error' => 'Error saving product'
            ], 500);
       }
    }

    /**
     * @OA\Get (
     *     path="/api/product/{id}",
     *     summary="Product details",
     *     tags={"Products"},
     *     @OA\Parameter (
     *         in="path",
     *         name="id",
     *         example=1,
     *         description="Product ID",
     *         required=true
     *     ),
     *     @OA\Response (
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property (
     *                 property="product",
     *                 type="object",
     *                 @OA\Property (
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property (
     *                     property="name",
     *                     type="string",
     *                     example="MacBook Pro 13.3 Retina [MYD82] M1 Chip 256 GB - Space Gray"
     *                 ),
     *                 @OA\Property (
     *                     property="description",
     *                     type="string",
     *                     example=""
     *                 ),
     *                 @OA\Property (
     *                     property="image",
     *                     type="string",
     *                     example="apple.com/v/macbook-pro/ac/images/overview/hero_13__d1tfa5zby7e6_large_2x.jpg"
     *                 ),
     *                 @OA\Property (
     *                     property="brand",
     *                     type="string",
     *                     example="Apple"
     *                 ),
     *                 @OA\Property (
     *                     property="price",
     *                     type="float",
     *                     example=2000
     *                 ),
     *                 @OA\Property (
     *                     property="price_sale",
     *                     type="float",
     *                     example=1950
     *                 ),
     *                 @OA\Property (
     *                     property="category",
     *                     type="string",
     *                     example="Macbook Pro"
     *                 ),
     *                 @OA\Property (
     *                     property="stock",
     *                     type="integer",
     *                     example=5
     *                 ),
     *                 @OA\Property (
     *                     property="created_at",
     *                     type="string",
     *                     example="2022-02-13 14:22:41"
     *                 ),
     *                 @OA\Property (
     *                     property="update_at",
     *                     type="string",
     *                     example="2022-02-13 14:22:41"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Product not found"
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Error getting product"
     *             )
     *         )
     *     )
     * )
     *
     * Get product from the database
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(int $id)
    {
        try {
            $product = Product::find($id);
            if (!$product)
                return response()->json([
                    'status' => 'error',
                    'error' => "Product not found"
                ], 404);

            return response()->json($product);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error' => 'Error getting product'
            ], 500);
        }
    }

    /**
     * @OA\Delete (
     *     path="/api/product/{id}",
     *     summary="Delete product",
     *     tags={"Products"},
     *      @OA\Parameter (
     *         in="path",
     *         name="id",
     *         example=1,
     *         description="Product ID",
     *         required=true
     *     ),
     *     @OA\Response (
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Product not found"
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Error deleting product"
     *             )
     *         )
     *     )
     * )
     *
     * Delete product from database
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(int $id)
    {
        try {
            $product = Product::find($id);
            if (!$product)
                return response()->json([
                    'status' => 'error',
                    'error' => "Product not found"
                ], 404);

            $product->delete();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error' => 'Error deleting product'
            ], 500);
        }
    }

    /**
     * @OA\Put (
     *     path="/api/product/{id}",
     *     summary="Update product",
     *     tags={"Products"},
     *     @OA\Parameter (
     *         in="path",
     *         name="id",
     *         example=1,
     *         description="Product ID",
     *         required=true
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         description="Product details",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property (
     *                 property="name",
     *                 type="string",
     *                 example="MacBook Pro 13.3 Retina [MYD82] M1 Chip 256 GB - Space Gray"
     *             ),
     *             @OA\Property (
     *                 property="description",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property (
     *                 property="image",
     *                 type="string",
     *                 example="apple.com/v/macbook-pro/ac/images/overview/hero_13__d1tfa5zby7e6_large_2x.jpg"
     *             ),
     *             @OA\Property (
     *                 property="brand",
     *                 type="string",
     *                 example="Apple"
     *             ),
     *             @OA\Property (
     *                 property="price",
     *                 type="float",
     *                 example=2000
     *             ),
     *             @OA\Property (
     *                 property="price_sale",
     *                 type="float",
     *                 example=1950
     *             ),
     *             @OA\Property (
     *                 property="category",
     *                 type="string",
     *                 example="Macbook Pro"
     *             ),
     *             @OA\Property (
     *                 property="stock",
     *                 type="integer",
     *                 example=5
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=200,
     *         description="Success",
     *         @OA\Header(
     *             header="Location",
     *             description="URL for the details of the updated product",
     *             @OA\Schema (
     *                 type="String",
     *                 example="/api/product/1"
     *             )
     *         ),
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property (
     *                 property="product",
     *                 type="array",
     *                 @OA\Items (
     *                     type="object",
     *                     @OA\Property (
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property (
     *                         property="name",
     *                         type="string",
     *                         example="MacBook Pro 13.3 Retina [MYD82] M1 Chip 256 GB - Space Gray"
     *                     ),
     *                     @OA\Property (
     *                         property="description",
     *                         type="string",
     *                         example=""
     *                     ),
     *                     @OA\Property (
     *                         property="image",
     *                         type="string",
     *                         example="apple.com/v/macbook-pro/ac/images/overview/hero_13__d1tfa5zby7e6_large_2x.jpg"
     *                     ),
     *                     @OA\Property (
     *                         property="brand",
     *                         type="string",
     *                         example="Apple"
     *                     ),
     *                     @OA\Property (
     *                         property="price",
     *                         type="float",
     *                         example=2000
     *                     ),
     *                     @OA\Property (
     *                         property="price_sale",
     *                         type="float",
     *                         example=1950
     *                     ),
     *                     @OA\Property (
     *                         property="category",
     *                         type="string",
     *                         example="Macbook Pro"
     *                     ),
     *                     @OA\Property (
     *                         property="stock",
     *                         type="integer",
     *                         example=5
     *                     ),
     *                     @OA\Property (
     *                         property="created_at",
     *                         type="string",
     *                         example="2022-02-14 12:18:34"
     *                     ),
     *                     @OA\Property (
     *                         property="updated_at",
     *                         type="string",
     *                         example="2022-02-14 12:18:34"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Product not found"
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items (
     *                     type="string",
     *                     example={
     *                         "The name field is required.",
     *                         "The brand field is required."
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response (
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Error updating product"
     *             )
     *         )
     *     )
     * )
     *
     * Updates a product in the database
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            $product = Product::find($id);
            if (!$product)
                return response()->json([
                    'status' => 'error',
                    'error' => "Product not found"
                ], 404);

            $data = json_decode($request->getContent(), 1);
            if (!$data)
                return response()->json([
                    'status' => ' error',
                    'errors' => ['Request body is not a valid JSON']
                ], 400);

            $rules = $this->createRules;
            $rules['id'] = ['required', 'exists:products', "in:{$id}"];

            $validate = Validator::make($data, $rules);
            if ($validate->fails())
                return response()->json([
                    'status' => 'error',
                    'errors' => $validate->errors()->all()
                ], 400);

            $product->update($data);

            return response()->json([
                'status' => 'success',
                'product' => $product
            ], 200, ['Location' => route('productsApi.get', $product->id)]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error' => 'Error updating product'
            ], 500);
        }
    }

    /**
     * @OA\Get (
     *     path="/api/product",
     *     summary="List products",
     *     tags={"Products"},
     *     @OA\Parameter (
     *         in="query",
     *         name="page",
     *         example=2,
     *         description="Page number",
     *         required=false
     *     ),
     *     @OA\Parameter (
     *         in="query",
     *         name="name",
     *         example="mac",
     *         description="Product name",
     *         required=false
     *     ),
     *     @OA\Parameter (
     *         in="query",
     *         name="min_price",
     *         example=100,
     *         description="Minimum price",
     *         required=false
     *     ),
     *     @OA\Parameter (
     *         in="query",
     *         name="max_price",
     *         example=150,
     *         description="Maximum price",
     *         required=false
     *     ),
     *     @OA\Parameter (
     *         in="query",
     *         name="min_price_sale",
     *         example=120,
     *         description="Minimum sale price",
     *         required=false
     *     ),
     *     @OA\Parameter (
     *         in="query",
     *         name="max_price_sale",
     *         example=180,
     *         description="Maximum sale price",
     *         required=false
     *     ),
     *     @OA\Parameter (
     *         in="query",
     *         name="min_stock",
     *         example=5,
     *         description="Minimum stock",
     *         required=false
     *     ),
     *     @OA\Parameter (
     *         in="query",
     *         name="max_stock",
     *         example=15,
     *         description="Maximum stock",
     *         required=false
     *     ),
     *     @OA\Response (
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property (
     *                 property="total",
     *                 type="integer",
     *                 example=100
     *             ),
     *             @OA\Property (
     *                 property="from",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property (
     *                 property="to",
     *                 type="integer",
     *                 example=10
     *             ),
     *             @OA\Property (
     *                 property="previous_page",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property (
     *                 property="next_page",
     *                 type="string",
     *                 example="/api/product?page=2"
     *             ),
     *             @OA\Property (
     *                 property="last_page",
     *                 type="string",
     *                 example="/api/product?page=10"
     *             ),
     *             @OA\Property (
     *                 property="products",
     *                 type="array",
     *                 @OA\Items (
     *                     type="object",
     *                     @OA\Property (
     *                         property="id",
     *                         type="integer",
     *                         example="1"
     *                     ),
     *                     @OA\Property (
     *                         property="name",
     *                         type="string",
     *                         example="MacBook Pro 13.3 Retina [MYD82] M1 Chip 256 GB - Space Gray"
     *                     ),
     *                     @OA\Property (
     *                         property="description",
     *                         type="string",
     *                         example=""
     *                     ),
     *                     @OA\Property (
     *                         property="image",
     *                         type="string",
     *                         example="apple.com/v/macbook-pro/ac/images/overview/hero_13__d1tfa5zby7e6_large_2x.jpg"
     *                     ),
     *                     @OA\Property (
     *                         property="brand",
     *                         type="string",
     *                         example="Apple"
     *                     ),
     *                     @OA\Property (
     *                         property="price",
     *                         type="float",
     *                         example=2000
     *                     ),
     *                     @OA\Property (
     *                         property="price_sale",
     *                         type="float",
     *                         example=1950
     *                     ),
     *                     @OA\Property (
     *                         property="category",
     *                         type="string",
     *                         example="Macbook Pro"
     *                     ),
     *                     @OA\Property (
     *                         property="stock",
     *                         type="integer",
     *                         example=5
     *                     ),
     *                     @OA\Property (
     *                         property="created_at",
     *                         type="string",
     *                         example="2022-02-13 14:22:41"
     *                     ),
     *                     @OA\Property (
     *                         property="update_at",
     *                         type="string",
     *                         example="2022-02-13 14:22:41"
     *                     )
     *                 )
     *             ),
     *         )
     *     ),
     *     @OA\Response (
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent (
     *             @OA\Property (
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property (
     *                 property="error",
     *                 type="string",
     *                 example="Error getting products"
     *             )
     *         )
     *     )
     * )
     *
     *
     * List products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        try {
            $products = Product::orderBy('id');

            if ($request->has('name'))
                $products->where('name', 'like', "%{$request->name}%");

            if ($request->has('min_price'))
                $products->where('price', '>=', $request->min_price);

            if ($request->has('max_price'))
                $products->where('price', '<=', $request->max_price);

            if ($request->has('min_price_sale'))
                $products->where('price_sale', '>=', $request->min_price_sale);

            if ($request->has('max_price_sale'))
                $products->where('price_sale', '<=', $request->max_price_sale);

            if ($request->has('min_stock'))
                $products->where('stock', '>=', $request->min_stock);

            if ($request->has('max_stock'))
                $products->where('stock', '<=', $request->max_stock);

            $rpp = $request->has('rpp') ? $request->rpp : 10; // Results per page
            $results = $products->paginate($rpp)->appends($request->all());

            return response()->json([
                'status' => 'success',
                'total' => $results->total(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
                'previous_page' => $results->previousPageUrl(),
                'next_page' => $results->nextPageUrl(),
                'last_page' => route('productsApi.list', array_merge($request->all(), ['page' => $results->lastPage()])),
                'products' => $results->items()
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error' => 'Error getting products'
            ], 500);
        }
    }
}
