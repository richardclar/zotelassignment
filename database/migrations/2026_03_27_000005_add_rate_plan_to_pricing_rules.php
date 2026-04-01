<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            $table->foreignId('rate_plan_id')->nullable()->after('room_type_id')->constrained()->onDelete('cascade');

            $table->dropUnique(['room_type_id', 'date', 'occupancy']);

            $table->unique(['room_type_id', 'rate_plan_id', 'date', 'occupancy'], 'pricing_unique');
            $table->index(['room_type_id', 'rate_plan_id', 'date', 'occupancy']);
        });
    }

    public function down(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            $table->dropUnique(['room_type_id', 'rate_plan_id', 'date', 'occupancy']);
            $table->dropForeign(['rate_plan_id']);
            $table->dropColumn('rate_plan_id');

            $table->unique(['room_type_id', 'date', 'occupancy']);
        });
    }
};
