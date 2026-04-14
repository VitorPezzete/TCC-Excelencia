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
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('endereco_id')->constrained('enderecos')->onDelete('restrict');
        $table->foreignId('cupom_id')->nullable()->constrained('cupons')->onDelete('set null');
        $table->enum('status', ['pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado'])->default('pendente');
        $table->decimal('subtotal', 8, 2);
        $table->decimal('desconto', 8, 2)->default(0);
        $table->decimal('taxa_entrega', 8, 2)->default(0);
        $table->decimal('total', 8, 2);
        $table->text('observacoes')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('pedidos');
}
};
