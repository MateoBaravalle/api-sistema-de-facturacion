<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->timestamp('payment_date')->nullable();
            $table->string('proof_file_url')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('external_transaction_id')->nullable();
            $table->string('reference_number')->unique();
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
        Schema::dropIfExists('payments');
    }
}
