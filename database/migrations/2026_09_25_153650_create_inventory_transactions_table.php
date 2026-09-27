<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create(
        'inventory_transactions',
        function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'stock_in',
                'order',
                'service',
                'adjustment',
            ]);

            $table->integer('quantity');

            $table->integer('stock_before');
            $table->integer('stock_after');

            $table->text('remarks')->nullable();

            $table->timestamps();
        }
    );
}
};
