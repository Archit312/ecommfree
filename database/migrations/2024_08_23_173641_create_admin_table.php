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
        Schema::create('admin', function (Blueprint $table) {
            $table->id(); // Creates an unsignedBigInteger primary key
            $table->unsignedBigInteger('user_id'); // Ensure user_id is the same type as id in users table
            $table->string('additional_email')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->longText('about_us')->nullable();
            $table->timestamps();

            // Adding foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
