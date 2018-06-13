<?php

namespace App\Controllers;

class VerificationController extends Controller
{
    public function showForm()
    {
        echo $this->view->render('auth/verification-form');
    }

    public function verify()
    {
        try {
            $this->auth->confirmEmail($_GET['selector'], $_GET['token']);

            flash()->success(['Ваш email адрес был подвержден, добро пожаловать!']);
            return redirect('/login');
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            flash()->error(['Неверный токен']);
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            flash()->error(['Время действия токена истекло!']);
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            flash()->error(['Такой email адрес уже существует!']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Ошибка сервера, попробуйте позже!']);
        }

    }

}