<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('workout_logs')) {
            Schema::create('workout_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('workout_program_id');
                $table->unsignedBigInteger('workout_day_id');
                $table->date('date');
                $table->boolean('is_completed')->default(false);
                $table->json('exercises_summary')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('workout_logs');
    }
};