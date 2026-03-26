<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_id');
$table->unsignedBigInteger('category_id')->nullable();///////////changed to nullable

            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('background_image'); // path relative to storage/
$table->string('chef_image')->nullable(); // path relative to storage/////changed to nullable
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('portal_id')->references('id')->on('portals')->onDelete('cascade');
             $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
  }
};