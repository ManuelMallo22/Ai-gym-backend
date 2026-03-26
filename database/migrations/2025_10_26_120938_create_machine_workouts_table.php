<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('machine_workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('weight'); // kg
            $table->unsignedTinyInteger('reps');
            $table->unsignedTinyInteger('sets')->default(1);
            $table->boolean('completed')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('machine_workouts');
    }
};
