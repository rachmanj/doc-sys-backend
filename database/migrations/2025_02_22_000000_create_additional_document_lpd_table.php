<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_document_lpd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpd_id')->constrained('lpds')->onDelete('cascade');
            $table->foreignId('additional_document_id')->constrained('additional_documents')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_document_lpd');
    }
}; 