<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
                'errors' => ['Error saving product']
            ], 500);
       }
    }

    /**
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
                    'errors' => ["Product with ID: {$id} not found"]
                ], 404);

            return response()->json($product);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'errors' => ['Error getting product']
            ], 500);
        }
    }

    /**
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
                    'errors' => ["Product with ID: {$id} not found"]
                ], 404);

            $product->delete();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'errors' => ['Error deleting product']
            ], 500);
        }
    }

    /**
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
                    'errors' => ["Product with ID: {$id} not found"]
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
                'errors' => ['Error updating product']
            ], 500);
        }
    }

    /**
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

            if ($request->has('stock'))
                $products->where('stock', $request->stock);

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
                'errors' => ['Error getting products']
            ], 500);
        }
    }
}
