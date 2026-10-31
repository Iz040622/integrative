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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->date('paymentDate');
            $table->time('paymentTime');
            $table->string('paymentMethod');
            $table->decimal('subTotal', 10, 2);
            $table->decimal('totalAmount', 10, 2);
            $table->string('invoiceUrl');
            $table->foreignId('promo_id')->nullable()->constrained('promo_code')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
