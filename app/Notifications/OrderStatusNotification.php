<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Models\Pedido;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public $pedido;
    public $status;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
        $this->status = $pedido->status;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $idStr = str_pad($this->pedido->id, 4, '0', STR_PAD_LEFT);
        
        $title = 'Atualização do Pedido #' . $idStr;
        $body = '';
        $icon = '/favicon.ico';

        switch ($this->status) {
            case 'confirmado':
                $title = 'Pagamento Confirmado!';
                $body = "Recebemos o pagamento do seu pedido. Logo começaremos a prepará-lo!";
                break;
            case 'preparando':
                $title = 'Pedido em Preparo!';
                $body = "A mágica começou! Estamos preparando suas delícias com muito carinho.";
                break;
            case 'saiu_para_entrega':
                $title = 'Saiu para Entrega!';
                $body = "O entregador já está a caminho com o seu pedido. Fique de olho!";
                break;
            case 'entregue':
                $title = 'Pedido Entregue!';
                $body = "Seu pedido chegou! Bom apetite. Não esqueça de deixar uma avaliação para nós!";
                break;
            case 'cancelado':
                $title = 'Pedido Cancelado';
                $body = "Seu pedido precisou ser cancelado. Se tiver dúvidas, entre em contato.";
                break;
            default:
                $body = "O status do seu pedido mudou para: " . ucfirst(str_replace('_', ' ', $this->status));
                break;
        }

        return (new WebPushMessage)
            ->title($title)
            ->icon($icon)
            ->body($body)
            ->action('Ver Pedido', 'open_order')
            ->data(['url' => url('/perfil?tab=pedidos')]);
    }
}
