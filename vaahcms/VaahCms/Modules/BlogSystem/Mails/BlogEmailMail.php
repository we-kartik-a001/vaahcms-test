<?php  namespace VaahCms\Modules\BlogSystem\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BlogEmailMail extends Mailable {

    use Queueable, SerializesModels;

    public $super_admin;
    public $item;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $item, $super_admin)
    {
        $this->super_admin = $super_admin->name;
        $this->item = $item;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function build()
    {
        return $this->view('blogsystem::emails.blogemail');
    }

}
