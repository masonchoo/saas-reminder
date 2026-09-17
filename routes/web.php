<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SubscriptionController;
use App\Models\Subscription;

// Homepage
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');
 
// About page
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

//Login page
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');


//Login page
Route::get('/home', function () {
    return Inertia::render('Dashboard', [
        'subscriptions' => Subscription::latest()->get()
    ]);
})->name('dashboard');

Route::post('/subscriptions/import', [SubscriptionController::class, 'import_excel'])->name('subscriptions.import');

Route::get('/r/{id}', function ($id) {
    $botPhone = "60123456789";
    $url = "https://wa.me/{$botPhone}?text=" . urlencode("/remind {$id}");
    return redirect()->away($url);
});