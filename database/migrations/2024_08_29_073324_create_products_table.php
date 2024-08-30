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
            $table->string('product_code')->unique();
            $table->string('product_name');
            $table->string('product_category');
            $table->string('product_quantity');
            $table->string('product_desc');
            $table->integer('product_price');
            $table->string('product_img_main');
            $table->string('product_img_1')->nullable();
            $table->string('product_img_2')->nullable();
            $table->string('product_img_3')->nullable();
            $table->integer('product_delivery_time')->nullable();
            $table->timestamps();

            $table->foreign('product_category')
                ->references('category_code') // Assuming 'id' is the primary key in the 'users' table
                ->on('product_category')
                ->onDelete('cascade');
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
