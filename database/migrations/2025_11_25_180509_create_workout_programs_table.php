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
    Schema::create('workout_programs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');

        // basic info
        $table->string('goal')->nullable();
        $table->string('fitness_level')->nullable();
        $table->integer('duration_weeks')->default(4); // we can reuse 1-week plan for 4 weeks

        // lifecycle
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->enum('status', ['draft', 'active', 'completed'])->default('draft');

        // AI + diet
        $table->json('diet_plan')->nullable();
        $table->json('ai_request')->nullable();
        $table->longText('ai_raw_response')->nullable();

        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('workout_programs');
}

};
