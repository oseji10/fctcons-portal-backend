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
        Schema::create('passport_photographs', function (Blueprint $table) {
            $table->id('photoId');
            $table->string('applicationId')->nullable();
            $table->unsignedBigInteger('userId')->nullable();
            $table->string('photoPath')->nullable();
            
            
            $table->timestamps();
            $table->softDeletes();

        $table->foreign('applicationId')->references('applicationId')->on('applications')->onDelete('cascade');
        $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

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
