<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_plan_meal_plan_component', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_plan_component_id')->constrained()->onDelete('cascade');
            $table->decimal('price_per_person_per_night', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_plan_meal_plan_component');
    }
};
