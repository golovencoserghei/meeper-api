<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeekScheduleColumnToStandTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stand_templates', function (Blueprint $table) {
            $table->json('week_schedule')->after('type');
        });

        Schema::dropColumns('stand_templates', ['days', 'times_range']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stand_templates', function (Blueprint $table) {
            $table->json('days');
            $table->json('times_range');

            $table->dropColumn('week_schedule');
        });
    }
}
