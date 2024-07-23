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
            $table->string('tradeName');
            $table->foreignId('generic_name_id')->constrained('generic_names');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('company_id')->constrained('companies');
            $table->integer('price');
            $table->integer('amount');
            $table->integer('expiringYear');
            $table->integer('expiringMonth');
            $table->integer('expiringDay');
            $table->integer('Show');
            $table->timestamps();
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
