<?php

namespace App\Models;

use Delight\Auth\Auth;

class Registration
{
    private $auth;
    private $database;
    private $notification;

    /**
     * Registration constructor.
     * @param Auth $auth
     * @param Database $database
     * @param Notification $notification
     */
    public function __construct(Auth $auth, Database $database, Notification $notification)
    {
        $this->auth = $auth;
        $this->database = $database;
        $this->notification = $notification;
    }

    /**
     * Регистрация нового пользователя
     *
     * @param $email
     * @param $password
     * @param $username
     * @return mixed
     * @throws \Delight\Auth\AuthError
     * @throws \Delight\Auth\InvalidEmailException
     * @throws \Delight\Auth\InvalidPasswordException
     * @throws \Delight\Auth\TooManyRequestsException
     * @throws \Delight\Auth\UserAlreadyExistsException
     * @throws \Delight\Db\Throwable\IntegrityConstraintViolationException
     */
    public function make($email, $password, $username)
    {

        $userId = $this->auth->register($email, $password, $username, function ($selector, $token) use($email) {
            $this->notification->emailWasChanged($email, $selector, $token);
        });

        $this->database->update('users', $userId, ['roles_mask' =>  Roles::USER]);

        return $userId;
    }

    /**
     * Подтверждение регистрации по email
     *
     * @param $selector
     * @param $token
     * @return string
     * @throws \Delight\Auth\AuthError
     * @throws \Delight\Auth\InvalidSelectorTokenPairException
     * @throws \Delight\Auth\TokenExpiredException
     * @throws \Delight\Auth\TooManyRequestsException
     * @throws \Delight\Auth\UserAlreadyExistsException
     */
    public function verify($selector, $token)
    {
        return $this->auth->confirmEmail($selector, $token);

    }

}