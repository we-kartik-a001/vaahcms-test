<?php

namespace VaahCms\Modules\OrderSystem\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use VaahCms\Modules\OrderSystem\Models\Order;

class OrderStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new mail instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Define the email envelope (subject etc.).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order created - #' . ($this->order->id ?? ''),
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('ordersystem::emails.orderstatus')
                    ->with(['order' => $this->order]);
    }
}
