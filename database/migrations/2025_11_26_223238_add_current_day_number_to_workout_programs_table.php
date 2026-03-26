<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('workout_programs', function (Blueprint $table) {
            $table->unsignedInteger('current_day_number')
                ->default(1)
                ->after('duration_weeks'); // adjust if you want
        });
    }

    public function down()
    {
        Schema::table('workout_programs', function (Blueprint $table) {
            $table->dropColumn('current_day_number');
        });
    }
};
