<?php

namespace App\Models;

use Intervention\Image\ImageManagerStatic;

class Image
{
    private $folder;

    /**
     * ImageManager constructor.
     */
    public function __construct()
    {
        $this->folder = config('uploadsFolder');
    }

    /**
     * Загрузка картинок
     *
     * @param $image
     * @param null $currentImage
     * @return null|string
     */
    public function uploadImage($image, $currentImage = null)
    {
        if (!is_file($image['tmp_name']) && !is_uploaded_file($image['tmp_name'])) {
            return $currentImage;
        }
//        dd($image);

        $this->deleteImage($currentImage);

        $filename = strtolower(str_random(11)) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);

        $image = ImageManagerStatic::make($image['tmp_name']);

//        dd($image);
        $image->save($this->folder . $filename);

        return $filename;
    }

    /**
     * Проверка наличия картинки
     *
     * @param $path
     * @return bool
     */
    public function checkImageExists($path)
    {
        if (null != $path && is_file($this->folder . $path) && file_exists($this->folder . $path)) {
            return true;
        }
    }

    /**
     * Удаление загруженной картинки
     *
     * @param $image
     */
    public function deleteImage($image) {
        if ($this->checkImageExists($image)) {
            unlink($this->folder . $image);
        }
    }

    /**
     * Получение картинки для пользователя
     *
     * @param $image
     * @return string
     */
    public function getImage($image)
    {
        if ($this->checkImageExists($image)) {
            return '/' . $this->folder . $image;
        }

        return '/img/default.png';

    }

    /**
     * Получение размеров изображения
     *
     * @param $image
     * @return string
     */
    public function getSizes($image)
    {
        if (!$this->checkImageExists($image)) {
            return 'Такого изображения не существует';
        }

        list($width, $height) = getimagesize($this->folder . $image);
        return $width . '*' . $height;
    }
}