<?php

namespace VaahCms\Modules\OrderSystem\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrashMail extends Mailable
{
    use Queueable, SerializesModels;

    public $collection;
    public $super_admin;

    public function __construct($collection, $super_admin)
    {
        $this->collection = $collection;
        $this->super_admin = $super_admin;
    }

    public function build()
    {
        return $this->view('ordersystem::emails.trashmail')
            ->with([
                'collection' => $this->collection,
                'super_admin' => $this->super_admin,
            ]);
    }
}
