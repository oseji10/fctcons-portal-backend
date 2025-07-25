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
    Schema::create('batched_candidates', function (Blueprint $table) {
    $table->id();
    $table->string('applicationId');
    $table->string('batchId');
    $table->timestamps();

    $table->foreign('applicationId')->references('applicationId')->on('applications')->onDelete('cascade');
    $table->foreign('batchId')->references('batchId')->on('batches')->onDelete('cascade');
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
