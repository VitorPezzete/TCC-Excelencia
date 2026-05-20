<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $table = 'pagamentos';

    protected $fillable = [
        'pedido_id',
        'metodo',
        'troco_para',
        'status',
        'mp_payment_id',
        'pix_qr_code',
        'pix_qr_code_base64',
        'pix_expiracao',
    ];

    protected $casts = [
        'pix_expiracao' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /** Verifica se o PIX ainda está dentro do prazo de validade */
    public function pixValido(): bool
    {
        return $this->pix_expiracao && now()->lt($this->pix_expiracao);
    }
}

