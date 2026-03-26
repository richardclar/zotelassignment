<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->integer('min_nights')->default(1);
            $table->integer('max_nights')->nullable();
            $table->integer('min_days_before_checkin')->nullable();
            $table->integer('max_days_before_checkin')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['valid_from', 'valid_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_rules');
    }
};
