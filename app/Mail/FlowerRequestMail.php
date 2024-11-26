<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FlowerRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $flowerRequest; // Public property for the data

    /**
     * Create a new message instance.
     */
    public function __construct($flowerRequest)
    {
        $this->flowerRequest = $flowerRequest; // Pass data to the Mailable
    }

    /**
     * Build the message.
     */


    public function build()
    {
        Log::info('Building the email for flower request.', ['request_id' => $this->flowerRequest->request_id]);

        return $this->subject('New Flower Request Received')
            ->view('emails.flower_request');
    }
    

}

