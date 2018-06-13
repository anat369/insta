<?php

namespace App\Controllers;

use App\Models\Registration;
use App\Models\Roles;
use Delight\Auth\Status;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class RegisterController extends Controller
{

    protected $registration;

    /**
     * RegisterController constructor.
     * @param Registration $registration
     */
    public function __construct(Registration $registration)
    {
        parent::__construct();
        $this->registration = $registration;
    }

    public function showForm()
    {
        echo $this->view->render('auth/register-form');
    }

    /**
     * @throws \Delight\Auth\AuthError
     * @throws \Delight\Db\Throwable\IntegrityConstraintViolationException
     */
    public function register()
    {
        $this->validate();
        try {
            $data = [
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'],PASSWORD_DEFAULT),
                'username' => $_POST['username'],
                'status' => Status::NORMAL,
                'verified' => 1,
                'roles_mask' => Roles::USER,
                'registered' => \time(),
            ];

            $this->database->create('users', $data);

            flash()->success(['Приветствуем вас, ' . $_POST['username'] . ' ! Теперь вы можете выполнить вход на сайт.']);
            return redirect('/login');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error(['Неправильный email адрес!']);
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error(['Неправильный пароль!']);
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            flash()->error(['Такой пользователь уже существует!']);
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error(['Слишком много попыток зарегистрироваться, попробуйте чуть попозже!']);
        }

        return redirect('/register');
    }

    private function validate()
    {
        $validator = Validator::key('username', Validator::stringType()->notEmpty())
            ->key('email', Validator::email())
            ->key('password', Validator::stringType()->notEmpty())
            ->key('terms', Validator::trueVal())
            ->keyValue('password_confirmation', 'equals', 'password');

        try {
            $validator->assert($_POST);

        } catch (ValidationException $exception) {
            $exception->findMessages($this->getMessages());
            flash()->error($exception->getMessages());

            return redirect('register');
        }
    }

    private function getMessages()
    {
        return [
            'terms' => 'Вы должны согласится с правилами.',
            'username' => 'Введите имя',
            'email' => 'Неверный формат e-mail',
            'password' => 'Введите пароль',
            'password_confirmation' => 'Пароли не сопадают'
        ];
    }
}