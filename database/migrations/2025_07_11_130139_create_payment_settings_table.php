<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->unsignedBigInteger('applicationType')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('applicationType')->references('typeId')->on('application_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_settings');
    }
}