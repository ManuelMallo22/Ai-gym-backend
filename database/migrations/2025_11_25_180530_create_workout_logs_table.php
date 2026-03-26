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
    Schema::create('workout_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('workout_program_id');
        $table->unsignedBigInteger('workout_day_id');
        $table->date('date');

        $table->boolean('is_completed')->default(false);
        $table->json('exercises_summary')->nullable(); // we can store sets/reps/weights here as JSON

        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('workout_program_id')->references('id')->on('workout_programs')->onDelete('cascade');
        $table->foreign('workout_day_id')->references('id')->on('workout_days')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('workout_logs');
}

};
