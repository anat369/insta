<?php

namespace App\Models;

use Delight\Auth\Role;

final class Roles
{
    const ADMIN = Role::ADMIN;
    const USER = Role::AUTHOR;

    /**
     * @return array
     */
    public static function getRoles()
    {
        return [
            [
                'id' => self::ADMIN,
                'title' => 'Администратор',
            ],
            [
                'id' => self::USER,
                'title' => 'Пользователь',
            ],
        ];
    }

    /**
     * Получаем название роли для пользователя
     *
     * @param $id
     * @return mixed
     */
    public static function getRole($id)
    {
        foreach (self::getRoles() as $role) {
            if ($id === $role['id']) {
                return $role['title'];
            }
        }
    }
}