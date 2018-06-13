<?php

namespace App\Models;

use Delight\Auth\Auth;
use Illuminate\Support\Facades\Mail;

class Profile
{
    private $auth;
    private $mail;
    private $database;
    private $image;
    private $notification;

    /**
     * Profile constructor.
     * @param Auth $auth
     * @param Mail $mail
     * @param Database $database
     * @param Image $image
     * @param Notification $notification
     */
    public function __construct(Auth $auth, Mail $mail, Database $database, Image $image, Notification $notification)
    {
        $this->auth = $auth;
        $this->mail = $mail;
        $this->database = $database;
        $this->image = $image;
        $this->notification = $notification;
    }

    /**
     * Изменение информации в профиле пользователя
     *
     * @param $newEmail
     * @param null $newUsername
     * @param $newImage
     * @throws \Delight\Auth\AuthError
     * @throws \Delight\Auth\EmailNotVerifiedException
     * @throws \Delight\Auth\InvalidEmailException
     * @throws \Delight\Auth\NotLoggedInException
     * @throws \Delight\Auth\TooManyRequestsException
     * @throws \Delight\Auth\UserAlreadyExistsException
     */
    public function changeInformation($newEmail, $newUsername = null, $newImage)
    {

        if($this->auth->getEmail() != $newEmail) {
            $this->auth->changeEmail($newEmail, function ($selector, $token) use ($newEmail) {
                $this->notification->emailWasChanged($newEmail, $selector, $token);
                flash()->success(['На почту ' . $newEmail . ' был отправлен код с подтверждением. Пожалуйста, проверьте почтовый ящик']);
            });
        }

        $user = $this->database->find('users', $this->auth->getUserId());

        $image = $this->image->uploadImage($newImage, $user['image']);

        $this->database->update('users', $this->auth->getUserId(), [
            'username' => isset($newUsername) ? $newUsername : $this->auth->getUsername(),
            "image" => $image,
        ]);
    }
}