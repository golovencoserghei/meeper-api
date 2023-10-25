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
        Schema::create('stands_reports', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reported_by');
            $table->dateTime('report_date');
            $table->integer('publications')->nullable();
            $table->integer('videos')->nullable();
            $table->integer('return_visits')->nullable();
            $table->integer('bible_studies')->nullable();
            $table->unsignedBigInteger('stands_records_id');
            $table->unsignedBigInteger('congregation_id');
            $table->unsignedBigInteger('stand_id');
            $table->timestamps();

            $table->foreign('reported_by')->references('id')->on('users');
            $table->foreign('stands_records_id')->references('id')->on('stands_records');
            $table->foreign('congregation_id')->references('id')->on('congregations');
            $table->foreign('stand_id')->references('id')->on('stands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stands_reports');
    }
};
