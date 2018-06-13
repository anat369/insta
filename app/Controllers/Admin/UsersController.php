<?php

namespace App\Controllers\Admin;

use App\Models\Image;
use App\Models\Roles;
use Delight\Auth\Status;

class UsersController extends AdminController
{
    private $image;

    /**
     * UsersController constructor.
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        parent::__construct();
        $this->image = $image;
    }

    public function index()
    {
        $users = $this->database->all('users');
        echo $this->view->render('admin/users/index', ['users' =>  $users]);
    }

    public function create()
    {
        $roles = Roles::getRoles();
        echo $this->view->render('admin/users/create', ['roles'  =>  $roles]);
    }

    public function store()
    {
        try {
            $id = $this->auth->admin()->createUser($_POST['email'], $_POST['password'], $_POST['username']);
            $user = $this->database->find('users',$id);
            $data = [
                'status'    =>  (isset($_POST['status']) ? Status::BANNED : Status::NORMAL),
                'roles_mask'    =>  $_POST['roles_mask']
            ];

            $data['image'] = $this->image->uploadImage($_FILES['image'], $user['image']);

            $this->database->update('users', $id, $data);
            return redirect('/admin/users');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error(['Неправильный формат email адреса']);
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error(['Неправильный пароль']);
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            flash()->error(['Такой пользователь уже существует']);
        }

        return back();
    }

    public function edit($id)
    {
        $user = $this->database->find('users', $id);
        $roles = Roles::getRoles();
        $this->auth->hasRole(1);
        echo $this->view->render('admin/users/edit', ['user'    =>  $user, 'roles'   =>  $roles]);
    }

    /**
     * Обновление информации о пользователе
     *
     * @param $id
     */
    public function update($id)
    {
        $data = [
            'email' => $_POST['email'],
            'username' => $_POST['username'],
            'status' => (isset($_POST['status']) ? STATUS::BANNED : STATUS::NORMAL),
            'roles_mask' => $_POST['roles_mask'],
        ];
        if(!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'],PASSWORD_DEFAULT);
        }
//        $user = $this->database->find('users', $id);
//        $data['image'] = $this->image->uploadImage($_FILES['image'], $user['image']);

        $this->database->update('users', $id, $data);
        return redirect('/admin/users');
    }

    public function delete($id)
    {
        try {
            $user = $this->database->find('users', $id);
            $this->image->deleteImage($user['image']);
            $this->auth->admin()->deleteUserById($id);
            return redirect('/admin/users');
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            flash()->error(['Такой пользователь не найден']);
        }

        return back();
    }

}