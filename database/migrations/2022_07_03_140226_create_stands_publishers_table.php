<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandsPublishersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stands_publishers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stand_template_id');
            $table->unsignedBigInteger('user_1')->nullable();
            $table->unsignedBigInteger('user_2')->nullable();
            $table->timestamp('date');
            $table->timestamps();

            $table->foreign('stand_template_id')->references('id')->on('stand_templates');
            $table->foreign('user_1')->nullable()->references('id')->on('users');
            $table->foreign('user_2')->nullable()->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stands_publishers');
    }
}
