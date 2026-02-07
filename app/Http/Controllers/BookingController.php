<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    // 1. التحقق من البيانات المرسلة
    $request->validate([
        'field_id'     => 'required|exists:fields,id',
        'user_name'    => 'required|string|max:255',
        'start_time'   => 'required',
        'booking_date' => 'required|date',
        'deposit'      => 'nullable|numeric|min:0',
    ]);

    try {
        // 2. استخدام الـ Transaction لضمان سلامة البيانات
        return \DB::transaction(function () use ($request) {
            
            $dayOfWeek = \Carbon\Carbon::parse($request->booking_date)->dayOfWeek;
            
            // حساب وقت النهاية تلقائياً (ساعة بعد البداية)
            $endTime = \Carbon\Carbon::parse($request->start_time)->addHour()->toTimeString();

            // 3. فحص التعارض مع استخدام lockForUpdate
            // الـ Lock ده بيخلي أي عملية تانية تحاول تفحص نفس الموعد "تستنى" لما دي تخلص
            
            $alreadyBooked = \App\Models\Booking::where('field_id', $request->field_id)
                ->where('start_time', $request->start_time)
                ->where(function ($query) use ($request, $dayOfWeek) {
                    $query->where('booking_date', $request->booking_date)
                          ->orWhere(function ($q) use ($dayOfWeek) {
                              $q->where('is_constant', 1)
                                ->where('day_of_week', $dayOfWeek);
                          });
                })
                ->lockForUpdate() // <--- القفل السحري هنا لمنع الحجز المزدوج
                ->exists();

            if ($alreadyBooked) {
                // لو الموعد اتخطف في الأجزاء من الثانية اللي فاتت
                return back()->withInput()->with('error', 'عذراً، هذا الموعد تم حجزه للتو! يرجى اختيار موعد آخر.');
            }

            // 4. تنفيذ الحجز الفعلي
            \App\Models\Booking::create([
                'field_id'     => $request->field_id,
                'user_name'    => $request->user_name,
                'start_time'   => $request->start_time,
                'end_time'     => $endTime,
                'booking_date' => $request->booking_date,
                'deposit'      => $request->deposit ?? 0,
                'is_constant'  => $request->has('is_constant') ? 1 : 0,
                'day_of_week'  => $dayOfWeek,
            ]);

            return back()->with('success', 'تم الحجز بنجاح بنظام الحماية!');
        });

    } catch (\Exception $e) {
        // في حالة حدوث أي خطأ غير متوقع في السيرفر
        return back()->withInput()->with('error', 'حدث خطأ أثناء معالجة الحجز، حاول مرة أخرى.');
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
}