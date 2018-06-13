<?php

namespace App\Controllers;


use App\Models\Image;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;

class ImagesController extends Controller
{
    protected $image;

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
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $firstPage = 8;

        $images = $this->database->getPaginatedFrom('images', 'user_id' ,$this->auth->getUserId(), $page, $firstPage);

        $pagination = paginate(
            $this->database->getCount('images', 'user_id', $this->auth->getUserId()),
            $page,
            $firstPage,
            '/images?page=(:num)'
        );

        echo $this->view->render('images/index', ['images'   =>  $images, 'pagination'    =>  $pagination]);
    }

    public function create()
    {
        $categories = $this->database->all('categories');
        echo $this->view->render('images/create', ['categories'  =>  $categories]);
    }

    public function store()
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty())
            ->key('description', Validator::stringType()->notEmpty())
            ->key('category_id', Validator::intVal())
            ->keyNested('image.tmp_name', Validator::image());

        $this->validate($validator);
        $image = $this->image->uploadImage($_FILES['image']);
        $sizes = $this->image->getSizes($image);
        $data = [
            'title' =>  $_POST['title'],
            'description' =>  $_POST['description'],
            'image' =>  $image,
            'sizes' =>  $sizes,
            'category_id' =>  $_POST['category_id'],
            'user_id'   =>  $this->auth->getUserId(),
            'date'  =>  time(),
        ];
        $this->database->create('images', $data);

        flash()->success(['Картинка успешно загружена']);

        return back();
    }

    /**
     * Редактирование картинок
     *
     * @param $id
     */
    public function edit($id)
    {
        $image = $this->database->find('images', $id);
        if(intval($image['user_id']) !== $this->auth->getUserId()) {
            flash()->error(['Вы можете редактировать только свои фотографии!']);
            return redirect('/images');
        }

        $categories = $this->database->all('categories');
        echo $this->view->render('images/edit', ['image' =>  $image, 'categories'    =>  $categories]);
    }

    /**
     * @param $id
     */
    public function update($id)
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty())
            ->key('description', Validator::stringType()->notEmpty())
            ->key('category_id', Validator::intVal())
            ->keyNested('image.tmp_name', Validator::optional(Validator::image()));

        $this->validate($validator);
        $image = $this->database->find('images', $id);

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

        flash()->success(['Запись успешно обновлена']);

        return back();
    }

    /**
     * @param $id
     */
    public function show($id)
    {
        $image = $this->database->find('images', $id);
        $user = $this->database->find('users', $image['user_id']);
        $userImages = $this->database->whereAll('images', 'user_id', $user['id'], 4);

        echo $this->view->render('images/show', [
            'image' => $image,
            'user' => $user,
            'userImages' => $userImages,
        ]);
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $image = $this->database->find('images', $id);
        if(intval($image['user_id']) !== $this->auth->getUserId()) {
            flash()->error(['Вы можете редактировать только свои фотографии!']);
            return redirect('/images');
        }
        $this->image->deleteImage($image['image']);
        $this->database->delete('images', $id);

        return back();
    }

    /**
     * @param $id
     */
    public function download($id)
    {
        $image = $this->database->find('images',$id);
        echo $this->view->render('images/download', [
            'image' =>  $image
        ]);
    }

    /**
     * @param $validator
     */
    private function validate($validator)
    {
        try {
            $validator->assert(array_merge($_POST, $_FILES));

        } catch (ValidationException $exception) {
            $exception->findMessages($this->getMessages());
            flash()->error($exception->getMessages());

            return back();
        }
    }

    /**
     * @return array
     */
    private function getMessages()
    {
        return [
            'title' => 'Введите название',
            'description'   =>  'Введите описание',
            'category_id'   =>  'Выберите категорию',
            'image' =>  'Неверный формат картинки'
        ];
    }

}