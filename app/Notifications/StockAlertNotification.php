<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification // Removed ShouldQueue to avoid queue worker requirement unless queue is configured
{
    use Queueable;

    private $product_name;
    private $stock_left;
    private $sku;
    private $location;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($product_name, $stock_left, $sku, $location = '')
    {
        $this->product_name = $product_name;
        $this->stock_left = $stock_left;
        $this->sku = $sku;
        $this->location = $location;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Alerte de Stock Bas / إنذار نقص المخزون : " . $this->product_name)
                    ->greeting("Bonjour / سلام,")
                    ->line("Ceci est une alerte automatique concernant un de vos produits dont le stock vient d'atteindre le seuil critique.")
                    ->line("هذا إنذار بخصوص أحد المنتجات لي نقص بزاف فالمخزون.")
                    ->line('---')
                    ->line('**Produit / المنتج :** ' . $this->product_name)
                    ->line('**SKU / الرمز :** ' . $this->sku)
                    ->line('**Magasin / المتجر :** ' . $this->location)
                    ->line('**Stock Restant / الباقي :** ' . $this->stock_left)
                    ->line('---')
                    ->line('Veuillez penser à réapprovisionner ce produit / المرجو تزويد المخزون قريباً.')
                    ->action('Accéder aux produits', url('/products'))
                    ->salutation(" ")
                    ->success();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

