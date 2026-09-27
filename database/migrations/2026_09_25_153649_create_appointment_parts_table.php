<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('appointment_parts', function (Blueprint $table) {
        $table->id();

        $table->foreignId('appointment_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('product_id')
            ->constrained()
            ->restrictOnDelete();

        $table->foreignId('added_by')
            ->constrained('users');

        $table->unsignedInteger('quantity')
            ->default(1);

        $table->decimal('unit_price', 10, 2);

        $table->enum('approval_status', [
            'pending',
            'approved',
            'declined'
        ])->default('pending');

        $table->timestamps();
    });
}
};
