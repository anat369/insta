<?php

namespace App\Controllers;

use App\Models\Roles;
use Delight\Auth\Auth;
use League\Plates\Engine;
use App\Models\Database;

class Controller
{

    protected $auth;
    protected $view;
    protected $database;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->auth = components(Auth::class);
        $this->view = components(Engine::class);
        $this->database = components(Database::class);
    }

    /**
     * Проверка доступа
     */
    public function checkAccess()
    {
        if ($this->auth->hasRole(Roles::USER)) {
            redirect('/');
        }
    }
}