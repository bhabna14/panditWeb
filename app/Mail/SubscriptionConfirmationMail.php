<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order; // Public property for the data

    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order = $order; // Assign the order to the public property
    }

    /**
     * Build the message.
     */


    public function build()
    {
        // Log::info('Building the email for flower request.', ['request_id' => $this->flowerRequest->request_id]);

        return $this->subject('New Flower Order Arrived')
            ->view('emails.flower_subscription_order');
    }
    

}

