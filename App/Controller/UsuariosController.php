<?php

namespace App\Controller;

use MF\Controller\Action;
use MF\Model\Container;

class UsuariosController extends Action
{

    public function Usuarios()
    {
        session_start();
        if ($_SESSION['tipo_usuario'] === 'gerente') {

            $search = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';

            $usuarios = Container::getModel('Usuarios');
            if (!empty($search)) {
                $this->view->usuarios = $usuarios->getUsuariosFiltrados($search);
            } else {
                $this->view->usuarios = $usuarios->listUsuario();
            }

            $this->render('/usuarios', 'dashboard');
        } else {
            header('Location: /home');
        }

    }

    public function increverse()
    {
        session_start();
        if ($_SESSION['tipo_usuario'] === 'gerente') {
            $this->view->usuario = [
                'nome' => '',
                'cpf' => '',
                'email' => '',
                'senha' => '',
                'tipo_usuario' => '',
            ];

            $this->view->erroCadastro = false;
            $this->render('inscreverse', 'dashboard');
        } else {
            header('Location: /home');
        }
    }

    public function registrar()
    {
        session_start();
        if ($_SESSION['tipo_usuario'] === 'gerente') {
            $usuario = Container::getModel('Usuarios');
            $usuario->__set('nome', $_POST['nome']);
            $usuario->__set('cpf', $_POST['cpf']);
            $usuario->__set('email', $_POST['email']);
            $usuario->__set('senha', $_POST['senha']);
            $usuario->__set('tipo_usuario', $_POST['tipo_usuario']);

            if ($usuario->validarCadastro()) {
                if ($usuario->cpfExiste() == 0
                    || $usuario->getUsuarioPorEmailECPF() == 0
                ) {
                    $usuario->salvar();
                    $this->view->usuarios = Container::getModel('Usuarios')->listUsuario();
                    $this->view->success = 'Usuario cadastrado';
                    $this->render('usuarios', 'dashboard');
                }
            } else {
                $this->view->usuarios = [
                    'nome' => $_POST['nome'],
                    'cpf' => $_POST['cpf'],
                    'email' => $_POST['email'],
                    'senha' => $_POST['senha'],
                    'tipo_usuario' => $_POST['tipo_usuario'],
                ];
                $this->view->error = 'Erro ao cadastrar usuario';
                $this->render('inscreverse', 'dashboard');
            }
        } else {
            header('Location: /home');
        }
    }

    public function deletarusuario($id)
    {
        session_start();
        $this->verificaPermissao(['gerente']);
        if (is_null($id)) {
            echo 'id nao encontrado';
            return;
        }

        $usuario = Container::getModel('Usuarios');
        $usuario->__set('id', $id);
        $usuario->deletar();


        $this->view->usuarios = $usuario->listUsuario();
        $this->view->success = 'Usuario Excluido com sucesso';
        $this->render('usuarios', 'dashboard');
    }

    public function direcionar()
    {

        session_start();
        if ($_SESSION['tipo_usuario'] === 'gerente') {
            $this->view->devolucoesPendentes = Container::getModel('Emprestimos')->verificarDevolucaoPendente();
        }

        if ($_SESSION['id'] != '' && $_SESSION['nome'] != '') {
            $this->render('/home', 'dashboard');
        } else {
            header('Location: /?login=erro');
        }

    }

}
