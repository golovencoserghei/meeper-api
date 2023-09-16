<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStandTemplateAndStandPublishersTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stand_templates', static function (Blueprint $table): void {
            $table->dropColumn('day');
            $table->jsonb('days')->after('type')->comment('Days of the week that the stand can stay');
        });

        Schema::table('stands_publishers', static function (Blueprint $table): void {
            $table->integer('day')->after('time')->comment('The day of the week on which the publisher enrolled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stand_templates', static function (Blueprint $table): void {
            $table->dropColumn('days');
            $table->enum('day', [1,2,3,4,5,6,7])->default(1);
        });

        Schema::table('stands_publishers', static function (Blueprint $table): void {
            $table->dropColumn('day');
        });
    }
}
