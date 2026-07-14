<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketsEmail extends Mailable implements ShouldQueue
{
   use Queueable, SerializesModels;

    public $transaction;
    public $tickets;

    public function __construct($transaction, $tickets)
    {
        $this->transaction = $transaction;
        $this->tickets = $tickets;
    }

    public function build()
    {
        return $this->subject('Tiket Pemesanan Anda - Pesona NTT')
                    ->markdown('emails.tickets');
    }
}
