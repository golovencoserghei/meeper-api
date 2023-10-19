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
        Schema::table('stand_templates', static function (Blueprint $table) {
            $table->boolean('is_reports_enabled')->default(false)->after('is_last_week_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('stand_templates', ['is_reports_enabled']);
    }
};
