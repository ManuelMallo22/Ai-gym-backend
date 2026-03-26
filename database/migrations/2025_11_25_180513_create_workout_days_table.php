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
    Schema::create('workout_days', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('workout_program_id');
        $table->integer('day_number');            // 1..7
        $table->string('day_name')->nullable();   // "Day 1 - Upper Body"
        $table->boolean('is_rest_day')->default(false);
        $table->timestamps();

        $table->foreign('workout_program_id')
              ->references('id')->on('workout_programs')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('workout_days');
}

};
