<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $model;

    protected $perPage = 10;
    protected $table;

    protected $dataForeign;

    protected $view;
    protected $data;
    protected $user;
    function __construct()
    {
        $this->view = 'pages.';
        $this->user = 1;
    }
}
