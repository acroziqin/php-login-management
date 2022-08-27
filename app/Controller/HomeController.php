<?php

namespace KrisnaBeaute\BelajarPhpMvc\Controller;

use KrisnaBeaute\BelajarPhpMvc\App\View;

class HomeController
{
    function index(): void
    {
        View::render('Home/index', [
            'title' => 'PHP Login Management'
        ]);
    }
}