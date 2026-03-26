<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_plan_id')->constrained();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('adults');
            $table->integer('children')->default(0);
            $table->string('status'); // confirmed, cancelled, completed
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            $table->index(['check_in_date', 'check_out_date']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
