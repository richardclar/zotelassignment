<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_plan_meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_plan_component_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['rate_plan_id', 'meal_plan_component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_plan_meal_plans');
    }
};
