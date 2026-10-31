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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 200);
            $table->json('roomAmenities')->nullable();
            $table->integer('paxNo')->default(1);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('imageUrl', 255)->nullable();
            $table->integer('availability')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_type');
    }
};
