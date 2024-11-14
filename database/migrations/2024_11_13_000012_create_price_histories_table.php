
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->timestamp('change_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_histories');
    }
}