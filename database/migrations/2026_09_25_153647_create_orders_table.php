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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        $table->string('order_number')->unique();

        $table->foreignId('customer_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->decimal('total_amount', 10, 2);

        $table->date('pickup_date');

        $table->time('pickup_time')->nullable();

        $table->string('pickup_code', 10)
            ->nullable();

        $table->enum('status', [
            'pending',
            'confirmed',
            'preparing',
            'ready_for_pickup',
            'claimed',
            'cancelled',
        ])->default('pending');

        $table->enum('payment_method', [
            'pay_at_store'
        ])->default('pay_at_store');

        $table->timestamps();
    });
}
};
