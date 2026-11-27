<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('captain_name');
            $table->string('phone', 20)->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['service', 'product']);
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('stock')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('court')->default('Quadra 1');
            $table->dateTime('starts_at');
            $table->unsignedTinyInteger('duration_hours');
            $table->string('status')->default('pendente');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['court', 'starts_at', 'status']);
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
            $table->unique(['booking_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('products');
        Schema::dropIfExists('teams');
    }
};
