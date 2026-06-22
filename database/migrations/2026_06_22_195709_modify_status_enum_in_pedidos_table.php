<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('aguardando_pagamento', 'pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado') DEFAULT 'pendente'");
        DB::statement("ALTER TABLE historico_status_pedido MODIFY COLUMN status ENUM('aguardando_pagamento', 'pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado') DEFAULT 'pendente'");
        DB::statement("ALTER TABLE historico_status_pedido MODIFY COLUMN status ENUM('pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado')");
    }
};
