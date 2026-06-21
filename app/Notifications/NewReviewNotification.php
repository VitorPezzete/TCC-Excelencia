<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Models\Avaliacao;

class NewReviewNotification extends Notification
{
    use Queueable;

    public $avaliacao;

    public function __construct(Avaliacao $avaliacao)
    {
        $this->avaliacao = $avaliacao;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $emoji = $this->avaliacao->nota >= 4 ? '⭐' : '⚠️';
        $title = "Nova Avaliação: {$this->avaliacao->nota} Estrelas {$emoji}";
        $nomeCliente = $this->avaliacao->user->name ?? 'Cliente';

        return (new WebPushMessage)
            ->title($title)
            ->icon('/favicon.ico')
            ->body("{$nomeCliente} avaliou o pedido #" . str_pad($this->avaliacao->pedido_id, 4, '0', STR_PAD_LEFT))
            ->action('Ver Avaliações', 'open_reviews')
            ->data(['url' => url('/admin?section=avaliacoes')]);
    }
}
