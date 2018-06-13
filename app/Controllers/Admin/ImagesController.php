<?php

namespace App\Controllers\Admin;


use App\Models\Image;
use Respect\Validation\Validator;

class ImagesController extends AdminController
{

    private $image;

    /**
     * ImagesController constructor.
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        parent::__construct();

        $this->image = $image;
    }

    public function index()
    {
        $images = $this->database->all('images');

        echo $this->view->render('admin/images/index', ['images' => $images]);
    }

    public function create()
    {
        $categories = $this->database->all('categories');

        echo $this->view->render('admin/categories/index', ['categories' => $categories]);
    }

    public function store()
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty());

        HomeController::validate($validator, $_POST, ['title' => 'Заполните поле "Название"']);

        $image = $this->image->uploadImage($_FILES['image']);
        $sizes = $this->image->getSizes($image);
        $data = [
            'image' =>  $image,
            'title' =>  $_POST['title'],
            'description' =>  $_POST['description'],
            'category_id' =>  $_POST['category_id'],
            'user_id'   =>  $this->auth->getUserId(),
            'sizes' =>  $sizes,
            'date'  =>  time(),
        ];

        $this->database->create('images', $data);

        return redirect('/admin/images');
    }

    public function edit($id)
    {
        $image = $this->database->find('images', $id);
        $categories = $this->database->all('categories');
        echo $this->view->render('admin/images/edit', ['categories'    =>  $categories, 'image'  =>  $image]);
    }

    public function update($id)
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty());

        HomeController::validate($validator, $_POST, ['title' => 'Заполните поле "Название"']);

        $image = $this->database->find('images',$id);

        $image = $this->image->uploadImage($_FILES['image'], $image['image']);
        $sizes = $this->image->getSizes($image);

        $data = [
            'image' =>  $image,
            'title' =>  $_POST['title'],
            'description' =>  $_POST['description'],
            'category_id' =>  $_POST['category_id'],
            'user_id'   =>  $this->auth->getUserId(),
            'sizes'    =>  $sizes,
        ];

        $this->database->update('images', $id, $data);

        return redirect('/admin/images');
    }

    public function delete($id)
    {
        $image = $this->database->find('images', $id);
        $this->image->deleteImage($image['image']);
        $this->database->delete('images', $id);
        return back();
    }

}