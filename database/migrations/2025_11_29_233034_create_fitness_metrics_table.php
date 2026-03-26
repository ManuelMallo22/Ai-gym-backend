<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_metrics', function (Blueprint $table) {
            $table->id();

            // Link to logged-in user
            $table->unsignedBigInteger('user_id');

            // Optional link to the program that created this snapshot
            $table->unsignedBigInteger('program_id')->nullable();

            $table->string('gender', 10)->nullable();      // 'male', 'female', etc.
            $table->integer('age')->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('goal')->nullable();

            // Calculated
            $table->decimal('bmi', 6, 2)->nullable();
            $table->decimal('bmr', 8, 2)->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_metrics');
    }
};
