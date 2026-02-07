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
        Schema::create('field_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade'); // ربط بالسعر بالملعب
            $table->time('from_time'); // بداية الفترة (مثلاً 08:00:00)
            $table->time('to_time');   // نهاية الفترة (مثلاً 16:00:00)
            $table->decimal('price', 8, 2); // السعر في الفترة دي
            $table->string('label')->nullable(); // اختياري: (صباحي / مسائي)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_prices');
    }
};
