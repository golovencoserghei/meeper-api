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
        Schema::create('stand_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stand_id');
            $table->unsignedBigInteger('congregation_id');
            $table->json('week_schedule'); // @todo - rename to weeks_schedules
            $table->json('default_week_schedule')->nullable();
            $table->boolean('is_last_week_default')->default(false);
            $table->string('activation_at')->nullable();
            $table->integer('publishers_at_stand')->default(2);
            $table->integer('status')->default(1); // enabled
            $table->timestamps();

            $table->unique(['stand_id', 'congregation_id']);

            $table->foreign('stand_id')->on('stands')->references('id');
            $table->foreign('congregation_id')->on('congregations')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stand_templates');
    }
};
