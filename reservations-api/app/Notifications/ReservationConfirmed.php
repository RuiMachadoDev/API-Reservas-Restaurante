<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification
{
    use Queueable;

    private $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Reserva Confirmada')
            ->line('A sua reserva foi confirmada com sucesso!')
            ->line('Detalhes da reserva:')
            ->line('Data e hora: ' . $this->reservation->reservation_time)
            ->line('Número de pessoas: ' . $this->reservation->number_of_people)
            ->line('Mesa: ' . $this->reservation->table->name)
            ->action('Ver Mais Detalhes', url('/'))
            ->line('Obrigado por escolher o nosso restaurante!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->id,
            'table_id' => $this->reservation->table_id,
            'number_of_people' => $this->reservation->number_of_people,
            'reservation_time' => $this->reservation->reservation_time,
        ];
    }
}
