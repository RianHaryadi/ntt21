<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\LoginForm;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PaketTourController;
use App\Http\Controllers\CultureController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserDashboardController;

// Halaman Utama dan Autentikasi
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', LoginForm::class)->name('login');
Route::get('/register', \App\Livewire\RegisterForm::class)->name('register');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// Destinasi Wisata (publik)
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{id}', [DestinationController::class, 'show'])->name('destinations.show');

// Hotel (publik)
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');

// Paket Tour (publik)
Route::get('/paket-tours', [PaketTourController::class, 'index'])->name('paket-tours.index');
Route::get('/paket-tours/{id}', [PaketTourController::class, 'show'])->name('paket-tours.show');

// Budaya (publik)
Route::get('/cultures', [CultureController::class, 'index'])->name('cultures.index');

// Pengecekan Booking (publik)
Route::get('/booking/check', [BookingController::class, 'checkForm'])->name('booking.checkForm');
Route::post('/booking/check', [BookingController::class, 'check'])->name('booking.check');
Route::get('/booking/{booking_number}', [BookingController::class, 'show'])->name('booking.show');

// Rute yang memerlukan login
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Booking Destinasi
    Route::get('/destinations/{destination}/book', [DestinationController::class, 'book'])->name('destinations.book');
    Route::post('/destinations/store', [DestinationController::class, 'store'])->name('destinations.store');

    // Booking Hotel
    Route::get('/hotels/{hotel}/book', [HotelBookingController::class, 'create'])->name('hotels.book');
    Route::post('/booking/hotel', [HotelBookingController::class, 'store'])->name('booking.hotel.store');
    Route::get('/booking/success/{id}', [HotelBookingController::class, 'success'])->name('booking.success');

    // Booking Paket Tour
    Route::get('/paket-tour/create/{tourPackage}', [PaketTourController::class, 'create'])->name('paket-tour.create');
    Route::post('/paket-tour/store', [PaketTourController::class, 'store'])->name('paket-tour.store');

    // Transaksi
    Route::get('/transactions/{booking_code}/payment', [TransactionController::class, 'payment'])->name('transaction.payment');
    Route::post('/transactions/{transaction}/pay', [TransactionController::class, 'confirmPayment'])->name('transactions.pay');
    Route::get('/transactions/{booking_code}/success', [TransactionController::class, 'success'])->name('transactions.success');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
});

// Asisten Perjalanan AI (Travel Chatbot)
Route::prefix('travel')->name('travel.')->group(function () {
    Route::get('/chat', [\App\Http\Controllers\TravelChatController::class, 'chat'])->name('chat');
    Route::get('/recommendation/{token}', [\App\Http\Controllers\TravelChatController::class, 'recommendation'])->name('recommendation');
});
