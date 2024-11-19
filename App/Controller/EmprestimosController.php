<?php

namespace App\Controller;

use MF\Controller\Action;
use MF\Model\Container;

class EmprestimosController extends Action
{
    public function Emprestimos()
    {
        session_start();
        $this->verificaPermissao(['gerente', 'professor', 'aluno']);
        $this->view->success = false;

        $emprestimosModel = Container::getModel('Emprestimos');

        $search = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';

        if ($_SESSION['tipo_usuario'] === 'gerente' || $_SESSION['tipo_usuario'] === 'professor') {
            $emprestimos = $emprestimosModel->getEmprestimosFiltrados($search);

        } else {
            $emprestimos = $emprestimosModel->getEmprestimosUsuario($_SESSION['id']);
        }

        $this->view->emprestimos = $emprestimos;
        $this->render('emprestimos', 'dashboard');
    }
}
