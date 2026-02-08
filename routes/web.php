<?php

use Illuminate\Support\Facades\Route;
use App\Models\Field;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;


Route::get('/', function () {
    return view('welcome');
});




// روت عرض الملاعب والمواعيد للجمهور
Route::get('/booking-status', [App\Http\Controllers\BookingController::class, 'publicStatus'])->name('public.status');
// روت خاص بجلب المربعات لليوزر والآدمن (AJAX)
Route::get('/get-slots-html', [App\Http\Controllers\BookingController::class, 'getSlotsHtml'])->name('get.slots.html');
Route::get('/public-slots', [App\Http\Controllers\BookingController::class, 'publicSlots']);

// روتات الضيوف (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// تسجيل الخروج
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// روتات الأدمن لإدارة المستخدمين
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AuthController::class, 'getAllUsers'])->name('admin.users.index');
    Route::post('/users/add', [AuthController::class, 'registerByAdmin'])->name('admin.users.store');
    Route::put('/users/{id}/role', [AuthController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/admin/users/{id}', [AuthController::class, 'destroy'])->name('admin.users.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/fields', [FieldController::class, 'index'])->name('admin.fields.index');    
    Route::get('/fields/create', [FieldController::class, 'create'])->name('admin.fields.create');
    Route::post('/fields', [FieldController::class, 'storeField'])->name('admin.fields.store');
    Route::get('/fields/{id}/edit', [FieldController::class, 'edit'])->name('admin.fields.edit');
    Route::put('/fields/{id}', [FieldController::class, 'updateField'])->name('admin.fields.update');
    Route::delete('/fields/{id}', [FieldController::class, 'destroy'])->name('admin.fields.destroy');

    Route::get('/reports/daily', [ReportController::class, 'dailyReport'])->name('admin.reports.daily');

    
    Route::get('/dashboard', [BookingController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/get-slots', [BookingController::class, 'getSlotsHtml'])->name('admin.getSlots');
    Route::post('/bookings', [BookingController::class, 'store'])->name('admin.bookings.store');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('admin.bookings.destroy');
    // ضيف السطر ده جوه مجموعة الـ Admin أو الـ Auth
Route::put('/bookings/{id}', [App\Http\Controllers\BookingController::class, 'update'])->name('admin.bookings.update');

    Route::post('/admin/bookings/{id}/collect', [BookingController::class, 'collectRemaining'])->name('admin.bookings.collect');
});


