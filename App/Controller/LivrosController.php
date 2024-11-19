<?php

namespace App\Controller;

use MF\Controller\Action;
use MF\Model\Container;

class LivrosController extends Action
{

    public function Livros()
    {
        session_start();
        $this->verificaPermissao(['gerente', 'professor', 'aluno']);
        $this->view->success = false;

        $search = $_GET['pesquisar'] ?? '';
        $livrosModel = Container::getModel('Livros');

        $this->view->livros = !empty($search) ? $livrosModel->getLivrosFiltrados($search) : $this->getLivrosList();

        $this->view->usuario = $this->recuperaUsuario();

        if ($_SESSION['tipo_usuario'] === 'gerente') {
            $this->view->devolucoesPendentes = Container::getModel('Emprestimos')->verificarDevolucaoPendente();
        }

        $this->render('livros', 'dashboard');
    }

    public function recuperaUsuario()
    {
        $usuario = Container::getModel('Usuarios');
        return $usuario->listUsuario();

    }

    public function CadastrarLivros()
    {
        session_start();
        $this->verificaPermissao(['gerente', 'professor']);
        $this->view->livros = ['titulo' => '', 'autor' => '', 'isbn' => ''];
        $this->view->erroCadastro = false;
        $this->render('cadastrarlivro', 'dashboard');
    }

    public function registrarlivro()
    {
        session_start();
        $this->verificaPermissao(['gerente', 'professor']);

        $livro = Container::getModel('Livros');
        $livro->setTitulo($_POST['titulo']);
        $livro->setAutor($_POST['autor']);
        $livro->setIsbn($_POST['isbn']);

        if ($livro->validarCadastro() && !$livro->getIsbn()) {
            $livro->salvar();
            $this->view->livros = $this->getLivrosList();
            $this->recuperaUsuario();
            $this->view->success = 'Livro cadastrado com sucesso';
            $this->render('livros', 'dashboard');
        } else {
            $this->view->livros = [
                'titulo' => $_POST['titulo'],
                'autor' => $_POST['autor'],
                'isbn' => $_POST['isbn'],
            ];
            $this->view->erroCadastro = true;
            $this->recuperaUsuario();
            $this->render('cadastrarlivro', 'dashboard');
        }
    }

    public function deletarlivro($id)
    {
        session_start();
        $this->verificaPermissao(['gerente']);
        if (is_null($id)) {
            echo 'id nao encontrado';
            return;
        }

        $livro = Container::getModel('Livros');
        $livro->__set('id', $id);
        $livro->deletar();


        $this->view->livros = $this->getLivrosList();
        $this->recuperaUsuario();
        $this->view->success = 'Livro Excluido com sucesso';
        $this->render('livros', 'dashboard');
    }

    public function emprestar($idlivro, $idusuario = null)
    {
        session_start();

        $livro = Container::getModel('Livros');
        $livro->__set('id', $idlivro);

        $emprestimo = Container::getModel('Emprestimos');
        $emprestimo->setLivroId($idlivro);

        if ($_SESSION['tipo_usuario'] === 'gerente') {
            if ($idusuario) {
                $usuario = Container::getModel('Usuarios');
                $usuario->__set('id', $idusuario);
                $emprestimo->setUsuarioId($idusuario);
            } else {
                $this->view->error = 'Selecione um usuário para o empréstimo.';
                $this->view->livros = $this->getLivrosList();
                header('Location: /livros');
                return;
            }
        } else {
            $usuario = Container::getModel('Usuarios');
            $usuario->__set('id', $_SESSION['id']);
            $emprestimo->setUsuarioId($_SESSION['id']);
        }

        if ($emprestimo->verificarEmprestimo()) {
            $this->view->error = 'Este livro está indisponível';
        } else {
            $dataEmprestimo = date('Y-m-d H:i:s');
            $emprestimo->setDataEmprestimo($dataEmprestimo);

            $dataDevolucao = date('Y-m-d H:i:s', strtotime($dataEmprestimo. ' + 10 days'));
            $emprestimo->setDataDevolucao($dataDevolucao);

            if ($emprestimo->registrarEmprestimo()) {
                $livro->atualizarDisponibilidade($idlivro, false);
                $this->view->success = 'Empréstimo registrado com sucesso!';
            } else {
                $this->view->error = "Erro ao registrar o empréstimo";
            }
        }

        $this->view->livros = $this->getLivrosList();
        $this->recuperaUsuario();
        $this->render('/livros', 'dashboard');
    }

    public function solicitarDevolucao($idLivro)
    {
        session_start();
        $this->verificaPermissao(['professor', 'aluno']);

        $emprestimo = Container::getModel('Emprestimos');
        $emprestimo->setLivroId($idLivro);
        $emprestimo->setUsuarioId($_SESSION['id']);

        $search = $_GET['pesquisar'] ?? '';

        if ($emprestimo->verificarEmprestimo()) {
            $emprestimo->solicitarDevolucao();
            $this->view->success = 'Solicitação de devolução enviada com sucesso!';
        } else {
            $this->view->error = 'Erro ao solicitar devolução. Empréstimo não encontrado ou já devolvido.';
        }

        if ($_SESSION['tipo_usuario'] === 'gerente') {
            $emprestimos = $emprestimo->getEmprestimosFiltrados($search);
        } else {
            $emprestimos = $emprestimo->getEmprestimosUsuario($_SESSION['id']);
        }

        $this->view->emprestimos = $emprestimos;

        $this->view->livros = $this->getLivrosList();
        $this->render('emprestimos', 'dashboard');
    }

    public function confirmarDevolucao($idLivro, $idUsuario)
    {
        session_start();
        $this->verificaPermissao(['gerente']);

        $emprestimo = Container::getModel('Emprestimos');
        $emprestimo->setLivroId($idLivro);
        $emprestimo->setUsuarioId($idUsuario);

        $search = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';

        if ($emprestimo->registrarDevolucao()) {
            $livro = Container::getModel('Livros');
            $livro->atualizarDisponibilidade($idLivro, true);
            $this->view->success = 'Devolução confirmada com sucesso!';
        } else {
            $this->view->error = 'Erro ao confirmar a devolução.';
        }

        if ($_SESSION['tipo_usuario'] === 'gerente') {
            $emprestimos = $emprestimo->getEmprestimosFiltrados($search);
        } else {
            $emprestimos = $emprestimo->getEmprestimosUsuario($_SESSION['id']);
        }

        $this->view->emprestimos = $emprestimos;

        $this->view->livros = $this->getLivrosList();
        $this->recuperaUsuario();
        $this->render('emprestimos', 'dashboard');
    }

    private function getLivrosList()
    {
        $livros = Container::getModel('Livros');
        return $livros->getLivros();
    }

}
