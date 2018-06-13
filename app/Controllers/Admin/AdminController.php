<?php

namespace App\Controllers\Admin;

use App\Models\Roles;
use Delight\Auth\Auth;
use League\Plates\Engine;
use App\Models\Database;

class AdminController
{

    protected $auth;
    protected $view;
    protected $database;
    /**
     * AdminController constructor.
     */
    public function __construct()
    {

        $this->auth = components(Auth::class);
        $this->view = components(Engine::class);
        $this->database = components(Database::class);

        if(!$this->auth->hasRole(Roles::ADMIN)) {
            abort(404);
        }
    }
}