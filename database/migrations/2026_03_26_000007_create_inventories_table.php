<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('total_rooms');
            $table->integer('booked_rooms')->default(0);
            $table->integer('blocked_rooms')->default(0);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique(['room_type_id', 'date']);
            $table->index(['date', 'is_closed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
