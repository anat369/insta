<?php

namespace App\Controllers;

use App\Models\Notification;

class ResetPasswordController extends Controller
{

    private $notification;

    public function __construct(Notification $notification)
    {
        parent::__construct();
        $this->notification = $notification;
    }

    public function showForm()
    {
        echo $this->view->render('auth/password-recovery-form');
    }

    public function recovery()
    {
        try {
            $this->auth->forgotPassword($_POST['email'], function ($selector, $token) {
                // send `$selector` and `$token` to the user (e.g. via email)
                $this->notification->passwordReset($_POST['email'], $selector, $token);
                flash()->success(['Код сброса пароля был отправлен вам на почту.']);
            });

        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            // invalid email address
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            // email not verified
        }
        catch (\Delight\Auth\ResetDisabledException $e) {
            // password reset is disabled
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            // too many requests
        }
        return back();
    }

    public function showSetForm()
    {
        if ($this->auth->canResetPassword($_GET['selector'], $_GET['token'])) {
            // put the selector into a `hidden` field (or keep it in the URL)
            // put the token into a `hidden` field (or keep it in the URL)

            // ask the user for their new password
            echo $this->view->render('auth/password-set-form', ['data'    =>  $_GET]);
        }
    }

    public function change()
    {
        try {
            $this->auth->resetPassword($_POST['selector'], $_POST['token'], $_POST['password']);

            flash()->success(['Пароль был успешно изменен!']);
            return redirect('/login');
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            flash()->error(['Неверный токен']);
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            flash()->error(['Токен просрочен']);
        }
        catch (\Delight\Auth\ResetDisabledException $e) {
            flash()->error(['Изменение пароля отключено пользователем']);
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error(['Введите пароль']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Превышен лимит попыток!']);
        }

        return back();
    }

}