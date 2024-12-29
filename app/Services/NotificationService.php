<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationService
{
    protected $messaging;

    // Initialize Firebase Messaging
    public function __construct($credentialsPath = null)
    {
        // Use provided path or default to configuration value
        $path = $credentialsPath ?: config('services.firebase.user.credentials');

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Firebase credentials file not found at: {$path}");
        }

        // Initialize Firebase Messaging
        $factory = (new Factory)->withServiceAccount($path);
        $this->messaging = $factory->createMessaging();
    }

    // Send a notification to a single device
    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification([
                'title' => $title,
                'body'  => $body,
            ])
            ->withData($data);

        return $this->messaging->send($message);
    }

    // public function sendBulkNotifications($deviceTokens, $title, $body, $data = [])
    // {
    //     $messages = [];
    //     foreach ($deviceTokens as $deviceToken) {
    //         $messages[] = CloudMessage::withTarget('token', $deviceToken)
    //             ->withNotification([
    //                 'title' => $title,
    //                 'body'  => $body,
    //             ])
    //             ->withData($data);
    //     }
    //     return $this->messaging->sendAll($messages);
    // }
    
    // public function sendBulkNotifications($deviceTokens, $title, $body, $data = [])
    public function sendBulkNotifications($deviceTokens, $title, $body, $data = [])
    {
        $messages = [];

        foreach ($deviceTokens as $deviceToken) {
            // Ensure the token is a valid string
            if (is_string($deviceToken) && !empty($deviceToken)) {
                $messages[] = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification([
                        'title' => $title,
                        'body'  => $body,
                    ])
                    ->withData($data);
            } else {
                \Log::warning('Invalid device token encountered.', ['deviceToken' => $deviceToken]);
            }
        }

        if (!empty($messages)) {
            return $this->messaging->sendAll($messages);
        }

        \Log::info('No valid messages to send.');
        return null;
    }

}
