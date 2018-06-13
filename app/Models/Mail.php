<?php

namespace App\Models;

use Swift_Mailer;
use Swift_Message;

class Mail
{
    private $mail;

    /**
     * Mail constructor.
     * @param Swift_Mailer $mail
     */
    public function __construct(Swift_Mailer $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Отправка писем
     *
     * @param $email
     * @param $body
     * @return int
     */
    public function send($email, $body)
    {
        $message = (new Swift_Message('Инстраграмм'))
            ->setFrom(['test@test.ru' => 'Test'])
            ->setTo($email)
            ->setBody($body);

        return $this->mail->send($message);
    }
}