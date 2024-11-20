
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('percentage')->nullable();
            $table->decimal('fixed_amount', 10, 2)->nullable();
            $table->enum('discount_type', ['by_percentage', 'fixed_amount']);
            $table->foreignId('supplier_id')->constrained();
            $table->string('category')->nullable();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('min_purchase', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_accumulative')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
