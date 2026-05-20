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
        Schema::create('zonas_entrega', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                        // Ex: "Zona Centro", "Zona Norte"
            $table->decimal('taxa', 8, 2);                // Valor do frete em R$
            $table->json('bairros');                      // Array JSON de bairros cobertos
            $table->decimal('frete_gratis_acima', 8, 2)->nullable(); // Opcional: frete grátis se subtotal >= valor
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas_entrega');
    }
};
