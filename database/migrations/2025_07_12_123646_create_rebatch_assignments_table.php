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
    Schema::create('rebatched_candidates', function (Blueprint $table) {
    $table->id();
    $table->string('applicationId')->nullable();
    $table->string('oldBatchId')->nullable();
    $table->string('newBatchId')->nullable();
    $table->unsignedBigInteger('rebatchedBy')->nullable();
    $table->timestamps();

    $table->foreign('applicationId')->references('applicationId')->on('applications')->onDelete('cascade');
    $table->foreign('oldBatchId')->references('batchId')->on('batches')->onDelete('cascade');
    $table->foreign('newBatchId')->references('batchId')->on('batches')->onDelete('cascade');
    $table->foreign('rebatchedBy')->references('id')->on('users')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_assignments');
    }
};
