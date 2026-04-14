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
    Schema::create('itens_carrinho', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
        $table->integer('quantidade')->default(1);
        $table->text('observacoes')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('itens_carrinho');
}
};
