<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discount_rules', function (Blueprint $table) {
            $table->foreignId('rate_plan_type_id')->nullable()->after('discount_type_id')->constrained()->onDelete('cascade');
            $table->index(['rate_plan_type_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('discount_rules', function (Blueprint $table) {
            $table->dropForeign(['rate_plan_type_id']);
            $table->dropColumn('rate_plan_type_id');
        });
    }
};
