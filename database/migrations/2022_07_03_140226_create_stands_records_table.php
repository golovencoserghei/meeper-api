<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stands_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stand_template_id');
            $table->integer('day');
            $table->dateTime('date_time');
            $table->timestamps();

            // @todo - add unique flags

            $table->foreign('stand_template_id')
                ->on('stand_templates')
                ->references('id')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stands_records');
    }
};
