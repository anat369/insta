<?php

namespace App\Controllers;

use App\Models\Mail;
use App\Models\Profile;

class ProfileController extends Controller
{
    private $mail;
    private $profile;

    /**
     * ProfileController constructor.
     * @param Mail $mail
     * @param Profile $profile
     */
    public function __construct(Mail $mail, Profile $profile)
    {
        parent::__construct();
        $this->mail = $mail;
        $this->profile = $profile;
    }

    /**
     * Страница профиля пользователя
     */
    public function showInfo()
    {
        $user = $this->database->find('users',$this->auth->getUserId());
        echo $this->view->render('profile/info', compact('user'));
    }

    public function showSecurity()
    {
        echo $this->view->render('profile/security');
    }

    /**
     * @throws \Delight\Auth\AuthError
     */
    public function postInfo()
    {
        try {
            $this->profile->changeInformation($_POST['email'], $_POST['username'],  $_FILES['image']);
            flash()->success(['Ваш профиль обновлен']);
            return redirect('/');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error(['Неверный формат email адреса']);
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            flash()->error(['Данный email адрес уже существует']);
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            flash()->error(['Ваша почта еще не подтверждена']);
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            flash()->error(['Вы еще не залогинены']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Ошибка на сервере, попробуйте чуть попозже!']);
        }
        return back();

    }

    /**
     * Изменение пароля пользователя
     */
    public function postSecurity()
    {
        try {
            $this->auth->changePassword($_POST['password'], $_POST['new_password']);
            flash()->success(['Пароль успешно изменен.']);
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            flash()->error(['Залогиньтесь!']);
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error(['Неправильный пароль!']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Ошибка сервера, попробуйте чуть попозже!']);
        }

        return back();
    }

}