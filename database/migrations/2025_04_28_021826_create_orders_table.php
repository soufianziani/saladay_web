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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('mnt_recu', 10, 2);
            $table->decimal('mnt_rendu', 10, 2);
            $table->enum('status', ['pending', 'complete', 'cancel'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
