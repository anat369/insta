<?php

namespace App\Models;

class Notification
{
    private $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    public function emailWasChanged($email, $selector, $token)
    {
        $message = 'https://php02/verify_email?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
        $this->mail->send($email, $message);

    }

    public function passwordReset($email, $selector, $token)
    {
        $message = 'https://php02/password-recovery/form?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
        $this->mail->send($email, $message);

    }
}