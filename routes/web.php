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
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\CancellationController;

// Halaman Utama dan Autentikasi
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', LoginForm::class)->name('login');
Route::get('/register', \App\Livewire\RegisterForm::class)->name('register');

// Social Login Google
Route::get('/auth/google', [SocialAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Toggle Bahasa
Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

// Toggle Mata Uang
Route::get('/currency/{currency}', [\App\Http\Controllers\CurrencyController::class, 'switch'])->name('currency.switch');

// Saran Pencarian (autocomplete)
Route::get('/search/suggestions', [\App\Http\Controllers\SearchSuggestionController::class, 'suggest'])
    ->middleware('throttle:60,1')
    ->name('search.suggestions');

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

// Peta Interaktif (publik)
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/api/map/destinations', [MapController::class, 'data'])->name('map.data');

// Booking Hotel (publik — guest checkout diperbolehkan)
Route::get('/hotels/{hotel}/book', [HotelBookingController::class, 'create'])->name('hotels.book');
Route::get('/hotels/{hotel}/availability', [HotelBookingController::class, 'checkAvailability'])->name('hotels.availability');
Route::post('/booking/hotel', [HotelBookingController::class, 'store'])->name('booking.hotel.store');
Route::get('/booking/success/{id}', [HotelBookingController::class, 'success'])->name('booking.success');
Route::get('/booking/{id}/voucher', [HotelBookingController::class, 'downloadVoucher'])->name('booking.voucher');

// AI Features (publik)
Route::prefix('ai')->name('ai.')->group(function () {
    Route::get('/', [AIController::class, 'hub'])->name('hub');
    Route::get('/search', [AIController::class, 'searchPage'])->name('search');

    // Endpoint yang memanggil Claude API — dibatasi rate agar tidak disalahgunakan
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('/review-summary', [AIController::class, 'reviewSummary'])->name('review-summary');
        Route::get('/smart-search', [AIController::class, 'smartSearch'])->name('smart-search');
        Route::get('/best-time', [AIController::class, 'bestTime'])->name('best-time');
        Route::get('/recommendations', [AIController::class, 'personalRecommendations'])->middleware('auth')->name('recommendations');
    });
});

// Pengecekan Booking (publik)
Route::get('/booking/check', [BookingController::class, 'checkForm'])->name('booking.checkForm');
Route::post('/booking/check', [BookingController::class, 'check'])->name('booking.check');
Route::post('/booking/{booking_number}/resend', [BookingController::class, 'resendEmail'])->name('booking.resendEmail');
Route::post('/booking/{booking_number}/cancel-request', [BookingController::class, 'requestCancellation'])->name('booking.requestCancellation');
Route::get('/booking/{booking_number}', [BookingController::class, 'show'])->name('booking.show');

// Rute yang memerlukan login
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Redeem Poin Loyalti
    Route::post('/loyalty/redeem', [\App\Http\Controllers\LoyaltyController::class, 'redeem'])->name('loyalty.redeem');

    // Booking Destinasi
    Route::get('/destinations/{destination}/book', [DestinationController::class, 'book'])->name('destinations.book');
    Route::post('/destinations/store', [DestinationController::class, 'store'])->name('destinations.store');

    // Booking Paket Tour
    Route::get('/paket-tour/create/{tourPackage}', [PaketTourController::class, 'create'])->name('paket-tour.create');
    Route::post('/paket-tour/store', [PaketTourController::class, 'store'])->name('paket-tour.store');

    // Review
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'toggleHelpful'])->name('reviews.helpful');

    // Tanya-Jawab Komunitas
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('/questions/{question}/answers', [QuestionController::class, 'storeAnswer'])->name('questions.answers.store');
    Route::delete('/answers/{answer}', [QuestionController::class, 'destroyAnswer'])->name('answers.destroy');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Cancellation & Refund
    Route::post('/bookings/{id}/cancel', [CancellationController::class, 'request'])->name('bookings.cancel');

    // Admin Cancellation
    Route::middleware('admin')->group(function () {
        Route::post('/admin/bookings/{id}/cancel/approve', [CancellationController::class, 'approve'])->name('admin.bookings.cancel.approve');
        Route::post('/admin/bookings/{id}/cancel/reject', [CancellationController::class, 'reject'])->name('admin.bookings.cancel.reject');
    });

    // Transaksi
    Route::get('/transactions/{booking_code}/payment', [TransactionController::class, 'payment'])->name('transaction.payment');
    Route::get('/transactions/{booking_code}/success', [TransactionController::class, 'success'])->name('transactions.success');
    Route::get('/transactions/{booking_code}/ticket', [TransactionController::class, 'downloadTicket'])->name('transactions.ticket');

    // Keranjang (lintas tipe: destinasi, hotel, paket tour dalam satu checkout)
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{id}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [\App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout');

    // Order gabungan (hasil checkout keranjang)
    Route::get('/orders/{order_code}/payment', [\App\Http\Controllers\OrderController::class, 'payment'])->name('orders.payment');
    Route::get('/orders/{order_code}/success', [\App\Http\Controllers\OrderController::class, 'success'])->name('orders.success');
});

// Webhook Midtrans (publik, dipanggil server Midtrans langsung — tanpa sesi/CSRF)
Route::post('/midtrans/notification', [TransactionController::class, 'notification'])->name('midtrans.notification');

// Asisten Perjalanan AI (Travel Chatbot)
Route::prefix('travel')->name('travel.')->group(function () {
    Route::get('/chat', [\App\Http\Controllers\TravelChatController::class, 'chat'])->name('chat');
    Route::get('/recommendation/{token}', [\App\Http\Controllers\TravelChatController::class, 'recommendation'])->name('recommendation');
});

// Temporary Editorial Routes for UI Review
Route::prefix('editorial')->group(function () {
    Route::get('/home', function () { return view('editorial.home'); });
    Route::get('/destination', function () { return view('editorial.destination'); });
    Route::get('/search', function () { return view('editorial.search'); });
    Route::get('/itinerary', function () { return view('editorial.itinerary'); });
});
