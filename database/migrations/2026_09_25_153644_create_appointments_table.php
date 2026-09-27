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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();

        $table->string('appointment_number')->unique();

        $table->foreignId('customer_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('motorcycle_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('service_id')
            ->constrained()
            ->restrictOnDelete();

        $table->foreignId('mechanic_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->date('appointment_date');
        $table->time('appointment_time');

        $table->text('problem_description')
            ->nullable();

        $table->enum('status', [
            'pending',
            'confirmed',
            'mechanic_assigned',
            'checked_in',
            'in_service',
            'ready_for_release',
            'completed',
            'cancelled',
            'rejected',
            'no_show',
        ])->default('pending');

        $table->text('admin_notes')->nullable();
        $table->text('mechanic_notes')->nullable();

        $table->timestamp('service_started_at')->nullable();
        $table->timestamp('service_completed_at')->nullable();

        $table->timestamps();
    });
}
};
