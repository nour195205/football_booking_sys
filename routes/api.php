<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\ReportController;
/* --- روتات عامة (الكل يشوفها - الويب والموبايل) --- */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// روتات اليوزر العادي (عرض الملاعب والمواعيد المتاحة)
Route::get('/fields', [FieldController::class, 'index']); 
Route::get('/slots/available', [BookingController::class, 'getAvailableSlots']);



/* --- روتات الآدمن (محمية بالتوكن والدور) --- */
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    
    // إدارة الملاعب والأسعار
    Route::post('/fields', [FieldController::class, 'storeField']);
    Route::post('/prices', [FieldController::class, 'setPrice']); 
    
    // إدارة الحجوزات
    Route::post('/bookings', [BookingController::class, 'store']); // حجز جديد
    Route::get('/bookings', [BookingController::class, 'index']);  // رؤية كل الحجوزات
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']); // مسح حجز
    Route::get('/reports/daily', [ReportController::class, 'dailyReport']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);


    Route::put('/fields/{id}', [FieldController::class, 'updateField']); // تعديل
    Route::delete('/fields/{id}', [FieldController::class, 'destroy']); // حذف

    Route::get('/users', [AuthController::class, 'getAllUsers']);
    Route::post('/users/add', [AuthController::class, 'registerByAdmin']);
    Route::put('/users/{id}/role', [AuthController::class, 'updateRole']);
});