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
    Schema::create('admission_settings', function (Blueprint $table) {
    $table->id('settingId');
    $table->unsignedBigInteger('session')->nullable();
    $table->string('resumptionDate')->nullable();
    $table->string('orientationDate')->nullable();
    $table->string('acceptanceDeadline')->nullable();
    $table->string('registrar')->nullable();
    $table->string('status')->nullable();
    $table->timestamps();

    $table->foreign('session')->references('sessionId')->on('academic_sessions')->onDelete('set null');

});

  
    Schema::table('applications', function (Blueprint $table) {
    $table->unsignedBigInteger('session')->nullable();

    $table->foreign('session')->references('sessionId')->on('academic_sessions')->onDelete('set null');

});

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
