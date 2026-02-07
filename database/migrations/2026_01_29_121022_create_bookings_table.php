<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade'); // ربط بالملعب
            $table->string('user_name'); // اسم العميل
            $table->decimal('deposit', 8, 2)->default(0); // العربون
            $table->time('start_time'); // بداية الساعة
            $table->time('end_time');   // نهاية الساعة
            $table->date('booking_date')->nullable(); // تاريخ اليوم (لو حجز عادي)
            $table->boolean('is_constant')->default(false); // هل هو حجز ثابت؟
            $table->integer('day_of_week')->nullable(); // رقم اليوم (0-6) لو ثابت
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
