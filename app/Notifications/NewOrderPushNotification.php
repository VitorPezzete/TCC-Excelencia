<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Models\Pedido;

class NewOrderPushNotification extends Notification
{
    use Queueable;

    public $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $idStr = str_pad($this->pedido->id, 4, '0', STR_PAD_LEFT);
        return (new WebPushMessage)
            ->title('NOVO PEDIDO APROVADO!')
            ->icon('/favicon.ico')
            ->body("Pedido #{$idStr} de R$ " . number_format($this->pedido->total, 2, ',', '.') . " chegou na cozinha. Clique para abrir o Painel.")
            ->action('Abrir Painel', 'open_panel')
            ->options(['TTL' => 1000])
            ->data(['url' => url('/admin')]);
    }
}
