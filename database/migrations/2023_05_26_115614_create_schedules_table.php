<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->date('day');
            $table->string('type'); //Production && Pause && Entretien && Autres
            $table->time('expected_start_time');
            $table->time('expected_end_time');
            $table->time('started_time')->nullable(); //AP, AJ, A, OFF, AR, R, DA, P
            $table->time('ended_time')->nullable();
            $table->integer('breakdown_total_min')->nullable()->default(0);
            $table->text('memo')->nullable();
            $table->string('status')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'created_by');
            $table->foreignIdFor(\App\Models\User::class, 'updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignIdFor(\App\Models\User::class, 'deleted_by')->nullable();
        });
    }

    // AP: Absence paye
    // AR: Arrive retarde
    // DA: Depart Anticipe
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
