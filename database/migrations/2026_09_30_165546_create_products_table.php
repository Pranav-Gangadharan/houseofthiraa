<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->index(); // midi | maxi | coord
            $table->unsignedInteger('price');    // whole rupees
            $table->text('description')->nullable();
            $table->json('sizes');               // sizes currently in stock
            $table->json('images')->nullable();  // paths on the public disk
            $table->string('color', 7)->default('#9d0b1b'); // drives the pixel preview until photos exist
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
