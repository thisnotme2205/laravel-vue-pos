<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller{
    public function index(){
        $totalPrice = 0;
        $carts = Cart::orderBy('created_at', 'desc')->get();
        
        foreach($carts as $cart){
            $totalPrice = $totalPrice + $cart->qty * $cart->product->price;
        }
        
        return datatables()->of($carts)
            ->addColumn('name', fn($cart) => $cart->product->name)
            ->addColumn('price', fn($cart) => idrCurrency($cart->product->price))
            ->addColumn('total', fn($cart) => idrCurrency($cart->totalPrice()))
            ->make(true);
    }

    public function sumTransaction(){
        $totalPrice = 0;
        $carts = Cart::orderBy('created_at', 'desc')->get();
        
        foreach($carts as $cart){
            $totalPrice = $totalPrice + $cart->qty * $cart->product->price;
        }

        return $totalPrice;
    }

    public function create(){
        //
    }

    public function store(Request $request){
        if($request->product_id != null){
            $product = Product::find($request->product_id);

            if($product->stock >= $request->qty){
                Cart::create($request->all());
                $product->decrement('stock', $request->qty);
            }
        }

        return redirect()->back();
    }

    public function show(Cart $cart){
        //
    }

    public function edit(Request $request, Cart $cart)
    {
        //
    }

    public function update(Request $request, Cart $cart){
        if($request->qty > 0){
            if($request->qty < $cart->qty){
                $cart->product->increment('stock', $cart->qty - $request->qty);
            }else if($request->qty > $cart->qty){
                $cart->product->decrement('stock', $request->qty - $cart->qty);
            }

            $cart->update(['qty' => $request->qty]);
        }

        return redirect()->back();
    }

    public function destroy(Cart $cart){
        Product::find($cart->product_id)->increment('stock', $cart->qty);
        $cart->delete();
    }
}
