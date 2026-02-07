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



Route::get('/booking', function () {
    $fields = Field::all(); // سحب الملاعب مباشرة من الداتابيز
    return view('fields', compact('fields'));
})->name('booking');

// روت خاص بجلب المربعات لليوزر والآدمن (AJAX)
Route::get('/get-slots-html', [App\Http\Controllers\BookingController::class, 'getSlotsHtml'])->name('get.slots.html');

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
});


