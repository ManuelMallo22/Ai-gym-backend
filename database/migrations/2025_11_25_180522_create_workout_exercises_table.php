<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('workout_exercises', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('workout_day_id');

        $table->string('exercise_name');
        $table->string('muscle_group')->nullable();
        $table->integer('sets')->nullable();
        $table->string('reps')->nullable();           // "8-10"
        $table->integer('rest_seconds')->nullable();  // rest time
        $table->integer('order')->default(0);         // order in the day

        $table->timestamps();

        $table->foreign('workout_day_id')
              ->references('id')->on('workout_days')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('workout_exercises');
}

};
