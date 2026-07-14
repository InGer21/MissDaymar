<?php

namespace App\Notifications;

use App\Models\SalesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SalesOrderCreated extends Notification
{
    use Queueable;

    public function __construct(public SalesOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entity = $this->order->entity;
        $user = $this->order->user;
        $total = number_format($this->order->total_usd, 2, ',', '.');
        $url = url("/admin/sales-orders/{$this->order->id}");

        $lines = collect($this->order->items)
            ->map(fn ($item) => "  • {$item->quantity} × {$item->presentation->product->name} - {$item->presentation->presentation_type} {$item->presentation->format}");
        $itemsList = $lines->implode("\n");

        return (new MailMessage)
            ->subject("Nueva orden de venta #{$this->order->id}")
            ->greeting('Nueva orden creada')
            ->line("**Cliente:** {$entity->name}")
            ->line("**Total:** \$ {$total}")
            ->line("**Creada por:** {$user->name}")
            ->line('**Productos:**')
            ->line($itemsList)
            ->action('Ver orden', $url);
    }
}
