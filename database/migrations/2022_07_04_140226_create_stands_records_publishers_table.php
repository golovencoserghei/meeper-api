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
        Schema::create('stands_records_publishers', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('stands_records_id');
            $table->unsignedBigInteger('publisher_id');
            $table->timestamps();

            $table->foreign('stands_records_id')->references('id')->on('stands_records');
            $table->foreign('publisher_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stands_records_publishers');
    }
};
