<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Payment;

class ReportController extends Controller
{
public function dailyReport(Request $request)
{
    $date = $request->date ?? \Carbon\Carbon::today()->toDateString();

    // أهم حاجة هنا الـ with('booking.field') عشان تجيب بيانات الحجز والملعب مع بعض
    $payments = \App\Models\Payment::with(['booking.field'])
        ->whereDate('paid_at', $date)
        ->get();

    $totalCashIn = $payments->sum('amount');
    $bookingsCount = $payments->pluck('booking_id')->unique()->count();

    return view('admin.reports.daily', [
        'date'           => $date,
        'details'        => $payments, // بنبعتها كـ details عشان التوافق مع الـ View
        'bookingsCount'  => $bookingsCount,
        'totalDeposit'   => $totalCashIn,
        'totalRemaining' => 0, // الخزنة مش بتهتم بالمتبقي "المستقبلي" بل بـ "اللي دخل فعلاً"
    ]);
}
}