<?php

namespace App\Controllers;

class HomeController extends Controller
{
    /**
     * Вывод картинок на главной странице, по умолчанию 8 штук
     */
    public function index()
    {
        $images = $this->database->all('images', 8);

        echo $this->view->render('home', ['images'   =>  $images]);
    }

    /**
     * Получаем все картинки по id категории
     *
     * @param $id
     */
    public function category($id)
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $firstPage = 8;
        $images = $this->database->getPaginatedFrom('images', 'category_id', $id, $page, $firstPage);
        $pagination = paginate(
            $this->database->getCount('images', 'category_id',$id),
            $page,
            $firstPage,
            '/category/$id?page=(:num)'
        );
        $category = $this->database->find('categories', $id);

        echo $this->view->render('category',[
            'images'   =>  $images,
            'pagination'    =>  $pagination,
            'category'  =>  $category
        ]);
    }

    /**
     * Получаем все картинки по id пользователя
     *
     * @param $id
     */
    public function user($id)
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $firstPage = 8;

        $images = $this->database->getPaginatedFrom('images', 'user_id' ,$id, $page, $firstPage);

        $pagination = paginate(
            $this->database->getCount('images', 'user_id',$id),
            $page,
            $firstPage,
            '/user/$id?page=(:num)'
        );

        $user = $this->database->find('users', $id);

        echo $this->view->render('user', [
            'images' =>  $images,
            'user'  =>  $user,
            'pagination'    =>  $pagination,
        ]);
    }


}