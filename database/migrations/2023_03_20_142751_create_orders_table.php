<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('payment_id');
            $table->foreignId('handling_id');
            $table->foreignId('service_id');
            $table->foreignId('fabcon_id')->nullable();
            $table->foreignId('detergent_id')->nullable();
            $table->string('trans_number');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->string('ref_num')->nullable();
            $table->string('total')->nullable();
            $table->string('amount')->nullable();
            $table->integer('change')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
