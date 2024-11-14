
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('importance', ['low', 'moderate', 'high'])->default('moderate');
            $table->enum('notification_type', ['info', 'warning', 'alert'])->default('info');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->string('related_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
