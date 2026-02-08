<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class BookingController extends Controller
{
    // الصفحة الرئيسية للوحة التحكم
    public function dashboard()
    {
        $fields = Field::all();
        return view('admin.bookings.dashboard', compact('fields'));
    }

    // جلب المربعات (Slots) بالـ AJAX
    public function getSlotsHtml(Request $request)
    {
        $fieldId = $request->field_id;
        $date = $request->date;
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // جلب الحجوزات (العادية في اليوم ده + الثابتة اللي في نفس يوم الأسبوع)
        $bookings = Booking::where('field_id', $fieldId)
            ->where(function ($query) use ($date, $dayOfWeek) {
                $query->where('booking_date', $date)
                      ->orWhere(function ($q) use ($dayOfWeek) {
                          $q->where('is_constant', 1)
                            ->where('day_of_week', $dayOfWeek);
                      });
            })
            ->get()
            ->keyBy('start_time');

        return view('admin.bookings.partials.slots_grid', compact('bookings', 'date'))->render();
    }

    // تنفيذ الحجز
   
   
   
   
   
   
   
 public function store(Request $request)
{
    // 1. التحقق من البيانات
    $request->validate([
        'field_id'     => 'required|exists:fields,id',
        'user_name'    => 'required|string|max:255',
        'start_time'   => 'required',
        'booking_date' => 'required|date',
        'deposit'      => 'nullable|numeric|min:0', // العربون اللي هيدفعه دلوقتي
    ]);

    try {
        return \DB::transaction(function () use ($request) {
            
            $dayOfWeek = \Carbon\Carbon::parse($request->booking_date)->dayOfWeek;
            $endTime = \Carbon\Carbon::parse($request->start_time)->addHour()->toTimeString();

            // 2. منع الحجز المزدوج (القفل السحري)
            $alreadyBooked = \App\Models\Booking::where('field_id', $request->field_id)
                ->where('start_time', $request->start_time)
                ->where(function ($query) use ($request, $dayOfWeek) {
                    $query->where('booking_date', $request->booking_date)
                          ->orWhere(function ($q) use ($dayOfWeek) {
                              $q->where('is_constant', 1)
                                ->where('day_of_week', $dayOfWeek);
                          });
                })
                ->lockForUpdate()
                ->exists();

            if ($alreadyBooked) {
                return back()->withInput()->with('error', 'عذراً، هذا الموعد تم حجزه للتو!');
            }

            // 3. إنشاء الحجز (بدون تخزين العربون في جدول الـ bookings نفسه لو حابب تتبع النظام الجديد)
            // ملاحظة: يُفضل الاحتفاظ بـ deposit في جدول الحجز كمرجع سريع، لكن العمليات المحاسبية هتكون من جدول المدفوعات
            $booking = \App\Models\Booking::create([
                'field_id'     => $request->field_id,
                'user_name'    => $request->user_name,
                'start_time'   => $request->start_time,
                'end_time'     => $endTime,
                'booking_date' => $request->booking_date,
                'deposit'      => $request->deposit ?? 0, 
                'is_constant'  => $request->has('is_constant') ? 1 : 0,
                'day_of_week'  => $dayOfWeek,
            ]);

            // 4. السحر هنا: تسجيل العربون في "يومية الخزنة" بتاريخ النهاردة
            if ($request->deposit > 0) {
                // بنفترض إن عندك موديل اسمه Payment مرتبط بجدول الـ payments
                \App\Models\Payment::create([
                    'booking_id'   => $booking->id,
                    'amount'       => $request->deposit,
                    'paid_at'      => \Carbon\Carbon::today(), // تاريخ اللحظة اللي الآدمن قبض فيها الفلوس
                    'note'         => 'عربون حجز لموعد بتاريخ: ' . $request->booking_date,
                ]);
            }

            return back()->with('success', 'تم تسجيل الحجز وإضافة العربون للخزنة بنجاح!');
        });

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}

public function update(Request $request, $id)
{
    $request->validate([
        'user_name' => 'required|string',
        'deposit' => 'required|numeric|min:0',
    ]);

    $booking = \App\Models\Booking::findOrFail($id);
    
    $booking->update([
        'user_name' => $request->user_name,
        'deposit' => $request->deposit,
        'is_constant' => $request->has('is_constant') ? 1 : 0,
    ]);

    return back()->with('success', 'تم تحديث بيانات الحجز بنجاح');
}
    // حذف/إلغاء حجز
    public function destroy($id)
    {
        Booking::findOrFail($id)->delete();
        return back()->with('success', 'تم إلغاء الحجز');
    }


    public function collectRemaining(Request $request, $bookingId)
{
    $request->validate([
        'amount_paid' => 'required|numeric|min:1'
    ]);

    // تسجيل العملية في الخزنة بتاريخ "دلوقتي"
    \App\Models\Payment::create([
        'booking_id' => $bookingId,
        'amount'     => $request->amount_paid,
        'paid_at'    => \Carbon\Carbon::today(), // ده اللي هيخليها تظهر في تقرير النهاردة
        'note'       => 'تحصيل باقي مبلغ الحجز',
    ]);

    return back()->with('success', 'تم تحصيل المبلغ وإضافته لتقرير اليوم!');
}

// داخل app/Http/Controllers/BookingController.php

public function publicStatus()
{
    $fields = \App\Models\Field::with('prices')->get();
    return view('public_booking_view', compact('fields'));
}

// app/Http/Controllers/BookingController.php

public function publicSlots(Request $request)
{
    $fieldId = $request->field_id;
    $date = $request->date;
    $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;

    $bookings = \App\Models\Booking::where('field_id', $fieldId)
        ->where(function ($query) use ($date, $dayOfWeek) {
            $query->where('booking_date', $date)
                  ->orWhere(function ($q) use ($dayOfWeek) {
                      $q->where('is_constant', 1)
                        ->where('day_of_week', $dayOfWeek);
                  });
        })
        ->get()
        ->keyBy('start_time');

    // هنكريت ملف جديد لليوزر مخصوص
    return view('partials.public_slots_list', compact('bookings', 'date'))->render();
}
}