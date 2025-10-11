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
    Schema::create('admissions', function (Blueprint $table) {
    $table->id('admissionId');
    $table->string('applicationId')->nullable();
    $table->unsignedBigInteger('programmeId')->nullable();
    $table->unsignedBigInteger('session')->nullable();
    $table->timestamps();

    $table->foreign('applicationId')->references('applicationId')->on('applications')->onDelete('set null');
    $table->foreign('programmeId')->references('programmeId')->on('programmes')->onDelete('set null');
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
