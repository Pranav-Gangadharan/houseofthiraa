<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number', 12)->unique();
            $table->string('status')->default('pending')->index(); // pending | paid | shipped | failed
            $table->string('name');
            $table->string('phone', 15);
            $table->string('email');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('pincode', 6);
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('shipping')->default(0);
            $table->unsignedInteger('total');
            $table->string('razorpay_order_id')->nullable()->index();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
