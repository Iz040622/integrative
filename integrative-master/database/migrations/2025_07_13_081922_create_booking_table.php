<?php
// Chin Jia Jun

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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->date('bookingDate');
            //$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id');
            $table->foreignId('payment_id')->nullable();
            //$table->foreignId('payment_id')->nullable()->constrained('payment')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
