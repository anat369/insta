<?php

namespace App\Controllers\Admin;

use Respect\Validation\Validator;

class CategoriesController extends AdminController
{

    public function index()
    {
        $categories = $this->database->all('categories');

        echo $this->view->render('admin/categories/index', ['categories' =>  $categories]);
    }

    public function create()
    {
        echo $this->view->render('admin/categories/create');
    }

    public function store()
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty());

        HomeController::validate($validator, $_POST, ['title' => 'Заполните поле "Название"']);

        $this->database->create('categories', $_POST);

        return redirect('/admin/categories');
    }

    /**
     * Редактирование категории
     *
     * @param $id
     */
    public function edit($id)
    {
        $category = $this->database->find('categories', $id);
        echo $this->view->render('admin/categories/edit', ['category'    =>  $category]);
    }

    /**
     * Обновление категории
     *
     * @param $id
     */
    public function update($id)
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty());

        HomeController::validate($validator, $_POST, ['title' => 'Заполните поле "Название"']);

        $this->database->update('categories', $id, $_POST);

        return redirect('/admin/categories');
    }

    /**
     * Удаление категории
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->database->delete('categories', $id);
        return back();
    }

}