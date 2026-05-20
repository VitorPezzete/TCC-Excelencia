<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Status do pagamento online (null = pagamento presencial, não aplicável)
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado', 'cancelado'])
                  ->nullable()
                  ->after('troco_para');

            // ID do pagamento retornado pelo Mercado Pago
            $table->string('mp_payment_id')->nullable()->after('status');

            // QR Code PIX copia-e-cola
            $table->text('pix_qr_code')->nullable()->after('mp_payment_id');

            // QR Code PIX em base64 (imagem)
            $table->text('pix_qr_code_base64')->nullable()->after('pix_qr_code');

            // Data/hora de expiração do QR Code PIX
            $table->dateTime('pix_expiracao')->nullable()->after('pix_qr_code_base64');
        });
    }

    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropColumn(['status', 'mp_payment_id', 'pix_qr_code', 'pix_qr_code_base64', 'pix_expiracao']);
        });
    }
};
