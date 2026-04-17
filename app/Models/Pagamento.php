<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $table = 'pagamentos';
    protected $fillable = [
        'pedido_id',
        'metodo',
        'troco_para'
    ];
    
    public function pedido() {
        return $this->belongsTo(Pedido::class);
    }
}
