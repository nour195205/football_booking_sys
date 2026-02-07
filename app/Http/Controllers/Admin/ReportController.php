<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dailyReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->toDateString();
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // جلب حجوزات اليوم (عادية وثابتة) مع بيانات الملاعب وأسعارها
        $bookings = Booking::with(['field.prices'])
            ->where(function ($query) use ($date, $dayOfWeek) {
                $query->where('booking_date', $date)
                      ->orWhere(function ($q) use ($dayOfWeek) {
                          $q->where('is_constant', 1)
                            ->where('day_of_week', $dayOfWeek);
                      });
            })->get();

        $totalDeposit = 0;
        $totalRemaining = 0;

        foreach ($bookings as $booking) {
            $totalDeposit += $booking->deposit;

            // حساب سعر الساعة بناءً على وقت الحجز من جدول الأسعار
            $priceEntry = $booking->field->prices
                ->where('from_time', '<=', $booking->start_time)
                ->where('to_time', '>', $booking->start_time)
                ->first();

            $totalPrice = $priceEntry ? $priceEntry->price : 0;
            $totalRemaining += ($totalPrice - $booking->deposit);
            
            // إضافة حقل محسوب لكل حجز لعرضه في الجدول
            $booking->remaining = ($totalPrice - $booking->deposit);
        }

        return view('admin.reports.daily', [
            'date' => $date,
            'bookingsCount' => $bookings->count(),
            'totalDeposit' => $totalDeposit,
            'totalRemaining' => $totalRemaining,
            'details' => $bookings
        ]);
    }
}