<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rate_plans', function (Blueprint $table) {
            $table->foreignId('rate_plan_type_id')->nullable()->after('room_type_id');
            $table->unsignedBigInteger('meal_plan_id')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('slug')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rate_plans', function (Blueprint $table) {
            $table->dropForeign(['rate_plan_type_id']);
            $table->dropColumn('rate_plan_type_id');
            $table->unsignedBigInteger('meal_plan_id')->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
            $table->string('slug')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
};
