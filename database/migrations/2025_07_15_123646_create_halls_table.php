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
    Schema::create('halls', function (Blueprint $table) {
    $table->id();
    $table->string('hallId')->unique();
    $table->string('hallName')->nullable();
    $table->string('capacity')->nullable();
    $table->timestamps();
});

Schema::table('applications', function (Blueprint $table) {
            $table->string('hall')->nullable();
            $table->string('seatNumber')->nullable();
            $table->foreign('hall')->references('hallId')->on('halls')->onDelete('cascade');
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
