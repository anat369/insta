<?php

namespace App\Controllers;

class LoginController extends Controller
{
    public function showForm()
    {
        $this->checkAccess();

        echo $this->view->render('auth/login-form');
    }

    /**
     * Вход пользователя, после входа редирект на главную страницу
     *
     */
    public function login()
    {
        $this->checkAccess();

        try {
            $rememberTime = null;

            if (isset($_POST['remember'])) {
                $rememberTime = (3600);
            }

            $this->auth->login($_POST['email'], $_POST['password'], $rememberTime);

            $this->checkBanned();

            return redirect('/');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error(['Вы ввели неправильный email адрес']);
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error(['Вы ввели неправильный пароль']);
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            flash()->error(['Этот email адрес был не подтвержден']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Ошибка сервера, попробуйте чуть попозже!']);
        }

        return back();
    }

    public function logout()
    {
        $this->auth->logOut();
        return redirect('/');
    }

    /**
     * Проверка - забанен пользователь или нет
     */
    public function checkBanned()
    {
        if($this->auth->isBanned()) {
            flash()->error(['Вы были забанены, скорее всего за недостойное поведение! :)']);
            $this->auth->logout();
            return redirect('/login');
        }
    }

}