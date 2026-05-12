<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;

// Стандартные маршруты сброса пароля Laravel
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    
    $status = Password::sendResetLink(
        $request->only('email')
    );
    
    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
            
            $user->save();
            
            event(new PasswordReset($user));
        }
    );
    
    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// Главная страница
Route::get('/', [EventController::class, 'index'])->name('home');

Route::post('/orders/{order}/auto-cancel', function(App\Models\Order $order) {
    if ($order->status === 'pending') {
        $order->cancel('Автоматическая отмена по истечении времени');
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
})->middleware('auth')->name('orders.auto-cancel');


// Публичные маршруты для мероприятий
Route::get('/concerts', [EventController::class, 'concerts'])->name('concerts');
Route::get('/festivals', [EventController::class, 'festivals'])->name('festivals');
Route::get('/search', [EventController::class, 'search'])->name('search');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Авторизация
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/api/cities', function() {
    // Получаем уникальные города из мероприятий
    $cities = \App\Models\Event::whereNotNull('location')
        ->whereNotNull('location')
        ->distinct()
        ->pluck('location')
        ->map(function($city) {
            return trim($city);
        })
        ->filter()
        ->unique()
        ->sort()
        ->values();
    
    return response()->json(['cities' => $cities]);
})->name('api.cities');

Route::post('/api/select-city', function(Request $request) {
    $request->validate(['city' => 'required|string']);
    session(['selected_city' => $request->city]);
    return response()->json(['success' => true]);
})->name('api.select-city');
// Защищенные маршруты (только для авторизованных)
Route::middleware('auth')->group(function () {
    // Профиль и заказы
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar/delete', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    
    Route::post('/favorites/add/{event}', [FavoriteController::class, 'add'])->name('favorites.add');
    Route::post('/favorites/remove/{event}', [FavoriteController::class, 'remove'])->name('favorites.remove');
    Route::get('/favorites', [FavoriteController::class, 'list'])->name('favorites.list');
    Route::get('/favorites/popup', [FavoriteController::class, 'popup'])->name('favorites.popup');


    Route::get('/orders/{order}/tickets/download', [OrderController::class, 'downloadTickets'])
        ->name('orders.tickets.download');
    // Заказы
    Route::post('/events/{event}/order', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    Route::post('/orders/{order}/payment/process', [OrderController::class, 'processPayment'])->name('orders.payment.process');
    Route::post('/orders/{order}/refund', [OrderController::class, 'refund'])->name('orders.refund');
    
    Route::get('/events/{event}/orders/export-csv', [OrderController::class, 'exportOrdersCSV'])->name('orders.export.csv');

    // Маршруты для организаторов (доступны и админам)
    Route::middleware('role:organizer,admin')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/events', [EventController::class, 'myEvents'])->name('events');                // organizer/events.blade.php
        Route::get('/events/create', [EventController::class, 'create'])->name('create');          // organizer/create-event.blade.php
        Route::post('/events', [EventController::class, 'store'])->name('store');                   // сохраняет новое мероприятие
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('edit');        // organizer/edit-event.blade.php
        Route::put('/events/{event}', [EventController::class, 'update'])->name('update');          // обновляет мероприятие
    });
    
    // Маршруты только для админов
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/events', [AdminController::class, 'events'])->name('events');
        Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
    });
});