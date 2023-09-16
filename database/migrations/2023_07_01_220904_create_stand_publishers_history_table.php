<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandPublishersHistoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stands_publishers_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stand_id');
            $table->unsignedBigInteger('congregation_id');
            $table->unsignedBigInteger('user_1')->nullable();
            $table->unsignedBigInteger('user_2')->nullable();
            $table->enum('day', [1,2,3,4,5,6,7]);
            $table->enum('time', [1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23]);
            $table->timestamp('date');
            $table->timestamps();

            $table->foreign('stand_id')->on('stands')->references('id');
            $table->foreign('congregation_id')->on('congregations')->references('id');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stands_publishers_history');
    }
}
