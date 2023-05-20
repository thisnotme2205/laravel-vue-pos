<?php

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::view('products', 'pages.products');
Route::view('products/create', 'pages.create-product');

Route::view('transaction', 'pages.transaction');

Route::get('checkout', function(){
    $total = 0;
    $carts = Cart::all();
    
    foreach($carts as $cart){
        $total = $total + $cart->qty * $cart->product->price;
    }
        
    $transaction = Transaction::create(['total' => $total]);
        
    foreach($carts as $cart){
        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'name'           => $cart->product->name,
            'price'          => $cart->product->price,
            'qty'            => $cart->qty
        ]);
    }

    Cart::truncate();

    return redirect()->route('home');
});