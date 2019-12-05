<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SampleNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $title;
    protected $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($text='テストメール')
    {
        $this->title = 'サンプルメールです';
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sample_notice')
                    ->subject($this->title)
                    ->with(['text' => $this->text]);
    }
}
