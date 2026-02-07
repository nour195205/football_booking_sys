<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade'); // مربوط بالحجز
            $table->decimal('amount', 8, 2); // المبلغ المدفوع
            $table->date('paid_at'); // تاريخ الدفع (ده أهم حقل للتقارير)
            $table->string('note')->nullable(); // ملاحظة (عربون، باقي، الخ)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
