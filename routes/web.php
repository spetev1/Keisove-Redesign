<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('demo-unlock', function (Request $request) {
    if (hash_equals((string) config('demo.password'), (string) $request->input('password'))) {
        $request->session()->put('demo_unlocked', true);

        return redirect()->intended(route('home'));
    }

    return back()->with('demo_gate_failed', true);
})->name('demo.unlock');

Route::get('/', HomeController::class)->name('home');

/*
 * Slugs are latin transliterations of the Bulgarian names (keisove, parfyumi),
 * so the paths stay readable without percent-encoding half the URL.
 */
Route::get('kategoriya/{category}', CategoryController::class)->name('category.show');
Route::get('produkt/{product}', ProductController::class)->name('product.show');
Route::get('promocii', PromotionController::class)->name('promotions');
Route::get('tarsene', SearchController::class)->name('search');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
