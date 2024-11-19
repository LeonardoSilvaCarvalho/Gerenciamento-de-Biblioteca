<?php

namespace Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\LivrosController;
use MF\Model\Container;

class LivrosControllerTest extends TestCase
{
    public function testLivros()
    {
        $livrosModelMock = $this->createMock(\App\Models\Livros::class);
        $livrosModelMock->method('getLivros')->willReturn([
            ['id' => 1, 'titulo' => 'Livro Teste', 'autor' => 'Autor Teste', 'isbn' => '123456789']
        ]);

        Container::getModel('Livros', $livrosModelMock);

        $controller = new LivrosController();

        $_SESSION['tipo_usuario'] = 'gerente';

        $controller->Livros();

        $this->assertNotEmpty($controller->view->livros);
        $this->assertIsArray($controller->view->livros);
        $this->assertCount(1, $controller->view->livros);
        $this->assertEquals('Livro Teste', $controller->view->livros[0]['titulo']);
    }

    public function testRegistrarLivro()
    {
        $livrosModelMock = $this->createMock(\App\Model\Livros::class);
        $livrosModelMock->method('validarCadastro')->willReturn(true);
        $livrosModelMock->method('getIsbn')->willReturn(0);

        Container::getModel('Livros', $livrosModelMock);

        $_POST['titulo'] = 'Livro de Teste';
        $_POST['autor'] = 'Autor Teste';
        $_POST['isbn'] = '987654321';

        $controller = new LivrosController();

        $_SESSION['tipo_usuario'] = 'gerente';

        $controller->registrarlivro();

        $this->assertTrue($controller->view->success);
        $this->assertEquals('Livro cadastrado com sucesso', $controller->view->success);
    }

    public function testDeletarLivro()
    {

        $livrosModelMock = $this->createMock(\App\Model\Livros::class);
        $livrosModelMock->method('deletar')->willReturn(true);

        Container::getModel('Livros', $livrosModelMock);

        $_SESSION['tipo_usuario'] = 'gerente';

        $controller = new LivrosController();

        $controller->deletarlivro(1);

        $this->assertTrue($controller->view->success);
        $this->assertEquals('Livro Excluido com sucesso', $controller->view->success);
    }

    public function testEmprestarLivro()
    {
        $livrosModelMock = $this->createMock(\App\Model\Livros::class);
        $livrosModelMock->method('atualizarDisponibilidade')->willReturn(true);

        $emprestimoModelMock = $this->createMock(\App\Model\Emprestimos::class);
        $emprestimoModelMock->method('verificarEmprestimo')->willReturn(false);
        $emprestimoModelMock->method('registrarEmprestimo')->willReturn(true);

        Container::getModel('Livros', $livrosModelMock);
        Container::getModel('Emprestimos', $emprestimoModelMock);

        $_SESSION['tipo_usuario'] = 'gerente';

        $controller = new LivrosController();

        $controller->emprestar(1);

        $this->assertTrue($controller->view->success);
        $this->assertEquals('Empréstimo registrado com sucesso!', $controller->view->success);
    }


}
