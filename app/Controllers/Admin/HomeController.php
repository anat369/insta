<?php

namespace App\Controllers\Admin;

use Respect\Validation\Exceptions\ValidationException;

class HomeController extends AdminController
{

    public function index()
    {
        echo $this->view->render('admin/dashboard');
    }

    /**
     * Проверка данных
     *
     * @param $validator
     * @param $data
     * @param $message
     */
    public static function validate($validator, $data, $message)
    {
        try {
            $validator->assert($data);

        } catch (ValidationException $exception) {
            $exception->findMessages($message);
            flash()->error($exception->getMessage());

            return back();
        }
    }

}