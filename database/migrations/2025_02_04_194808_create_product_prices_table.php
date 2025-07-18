<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained();
            $table->decimal('base_price', 10, 2);
            $table->decimal('converted_price', 10, 2);
            $table->boolean('is_main_price')->default(false);
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['product_id']);
            $table->index(['currency_id']);
            $table->index('is_main_price');
            
            // Add unique constraint to prevent duplicate prices
            $table->unique(
                ['product_id', 'currency_id'],
                'unique_product_currency'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
}; 