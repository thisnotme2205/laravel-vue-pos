<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller{
    public function index(Request $request){
        $products = Product::when(!in_array($request->category, ["null", null], true), function($query) use($request){
            return $query->where('category_id', $request->category);
        })->when($request->category == "null", function($query){
            return $query->where('category_id', null);
        })->when($request->cart, function($query) use($request){
            return $query->whereNotIn('id', Cart::select('product_id'))->where('stock', '>', 0)->where('name', 'LIKE', '%'.$request->search.'%');
        })->orderBy('name', 'ASC')->get();

        // if($products->isEmpty()){
        //     $products = [array(
        //         'id' => null,
        //         'name' => null,
        //         'category_id' => null,
        //         'price' => null,
        //         'stock' => null
        //     )];
        // }

        if($request->cart){
            return json_encode($products);
        }

        return datatables()->of($products)
            ->addColumn('category_name', fn($product) => $product->category_id == null ? '-' : $product->category->name)
            ->editColumn('price', fn($product) => $product->price)
            ->make(true);
    }

    public function create(){
        //
    }

    public function store(Request $request){
        Product::create($request->all());
    }

    public function show(Product $product){
        //
    }

    public function edit(Product $product){
        //
    }

    public function update(Request $request, Product $product){
        if($request->stock){
            $product->increment('stock', $request->stock);
        }else{
            $product->update($request->all());
        }
    }

    public function destroy(Product $product){
        $product->delete();
    }

    public function product(Request $request){
        $product = Product::where('stock', '!=', '0')
            ->when($request->search, function($query) use($request){
                return $query->where('name', 'LIKE', '%'.$request->query.'%');
            })->orderBy('name', 'asc')->first();

        return response()->json($product);
    }
}