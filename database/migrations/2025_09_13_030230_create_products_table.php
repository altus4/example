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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('weight', 8, 2)->nullable();
            $table->softDeletes();
            $table->index(['price']);
            $table->timestamps();

            $table->fullText(['name', 'description', 'sku'], 'idx_products_fulltext_name_description_sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
