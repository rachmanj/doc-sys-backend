<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_spi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spi_id')->constrained('spis')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_spi');
    }
}; 