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
        Schema::create('order_bot_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('order_range_min')->default(1);
            $table->integer('order_range_max')->default(100);
            $table->decimal('order_value_min', 10, 2)->default(10.00);
            $table->decimal('order_value_max', 10, 2)->default(1000.00);
            $table->integer('items_per_order_min')->default(1);
            $table->integer('items_per_order_max')->default(10);
            $table->integer('one_item_order_chance_min')->default(0);
            $table->integer('one_item_order_chance_max')->default(100);
            $table->integer('order_speed_min')->default(1);
            $table->integer('order_speed_max')->default(60);
            $table->string('order_speed_unit')->default('seconds'); // 'seconds', 'minutes', 'hours'
            $table->string('csv_file_path')->nullable(); // Path to the CSV file
            $table->boolean('telegram_bot')->nullable(); // Path to the CSV file
            $table->boolean('unlimited_orders')->nullable(); // Path to the CSV file
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bot_settings');
    }
};
