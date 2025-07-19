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
       
    Schema::create('hall_assignment', function (Blueprint $table) {
    $table->id();
    $table->string('applicationId')->nullable();
    $table->string('batch')->nullable();
    $table->string('hall')->nullable();
    $table->string('seatNumber')->nullable();
    $table->unsignedBigInteger('verifiedBy')->nullable();
    $table->timestamps();

    $table->foreign('applicationId')->references('applicationId')->on('applications')->onDelete('cascade');
    $table->foreign('batch')->references('batchId')->on('batches')->onDelete('cascade');
    $table->foreign('hall')->references('hallId')->on('halls')->onDelete('cascade');
    $table->foreign('verifiedBy')->references('id')->on('users')->onDelete('cascade');
});


    Schema::table('batches', function (Blueprint $table) {
            $table->boolean('isVerificationActive')->default(0);
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
