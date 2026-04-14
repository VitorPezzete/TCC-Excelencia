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
    Schema::create('historico_status_pedido', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
        $table->enum('status', ['pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado']);
        $table->string('observacao')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('historico_status_pedido');
}
};
