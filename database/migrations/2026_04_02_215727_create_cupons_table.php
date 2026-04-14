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
    Schema::create('cupons', function (Blueprint $table) {
        $table->id();
        $table->string('codigo')->unique();
        $table->enum('tipo', ['fixo', 'porcentagem']);
        $table->decimal('valor', 8, 2);
        $table->decimal('valor_minimo_pedido', 8, 2)->default(0);
        $table->integer('maximo_usos')->nullable();
        $table->integer('contagem_usos')->default(0);
        $table->boolean('ativo')->default(true);
        $table->date('expira_em')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('cupons');
}
};
