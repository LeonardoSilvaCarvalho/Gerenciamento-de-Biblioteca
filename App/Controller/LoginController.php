<?php
namespace App\Controller;

use App\Models\Usuarios;
use MF\Controller\Action;
use MF\Model\Container;

class LoginController extends Action
{

    public function index()
    {
        $this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
        $this->render('index');
    }


}
