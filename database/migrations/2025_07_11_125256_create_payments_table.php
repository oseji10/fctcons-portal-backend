<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('applicationId');
            $table->unsignedBigInteger('userId');
            $table->string('rrr')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('orderId')->unique();
            $table->string('status')->default('pending'); // pending, success, failed
            $table->text('response')->nullable();
            $table->text('channel')->nullable();
            $table->text('paymentDate')->nullable();


            $table->timestamps();

            $table->foreign('applicationId')->references('applicationId')->on('applications')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}