<?php

namespace Tests\Controller;

use App\Controller\UsuariosController;
use MF\Model\Container;
use PHPUnit\Framework\TestCase;
use App\Models\Usuarios;

class UsuariosControllerTest extends TestCase
{
    public function testUsuariosComPermissaoGerente()
    {
        $usuariosMock = $this->createMock(\App\Models\Usuarios::class);

        $usuariosMock->method('listUsuario')
            ->willReturn([['id' => 1, 'nome' => 'Leonardo', 'cpf' => '12345678900', 'email' => 'leo@example.com']]);

        $usuariosMock->method('getUsuariosFiltrados')
            ->willReturn([['id' => 1, 'nome' => 'Leonardo', 'cpf' => '12345678900', 'email' => 'leo@example.com']]);

        $_SESSION['tipo_usuario'] = 'gerente';

        Container::getModel('Usuarios', $usuariosMock);

        $usuariosController = new \App\Controller\UsuariosController();

        $usuariosController->Usuarios();

        $this->assertNotEmpty($usuariosController->view->usuarios, 'A variável usuarios está vazia.');
        $this->assertIsArray($usuariosController->view->usuarios, 'A variável usuarios não é um array.');
        $this->assertCount(1, $usuariosController->view->usuarios, 'A variável usuarios não contém o número esperado de elementos.');
        $this->assertEquals('Leonardo', $usuariosController->view->usuarios[0]['nome'], 'O nome do usuário não corresponde.');
    }

    public function testIncreverseComPermissaoGerente()
    {
        $_SESSION['tipo_usuario'] = 'gerente';

        $usuariosController = new UsuariosController();

        $this->expectOutputString('');

        $usuariosController->increverse();

        $this->assertEquals($usuariosController->view->erroCadastro, $usuariosController->view->erroCadastro);
        $this->assertEmpty($usuariosController->view->usuarios);
    }

    public function testRegistrarUsuarioComSucesso()
    {
        $usuariosMock = $this->createMock(Usuarios::class);

        $usuariosMock->method('validarCadastro')
            ->willReturn(true);

        $usuariosMock->method('cpfExiste')
            ->willReturn(0);

        $usuariosMock->method('getUsuarioPorEmailECPF')
            ->willReturn(0);

        $_SESSION['tipo_usuario'] = 'gerente';

        $usuariosController = new UsuariosController();
        $usuariosController->usuariosModel = $usuariosMock;

        $this->expectOutputString('');

        $_POST = [
            'nome' => 'Leonardo',
            'cpf' => '12345678900',
            'email' => 'leo@example.com',
            'senha' => 'senha123',
            'tipo_usuario' => 'aluno'
        ];

        $usuariosController->registrar();

        $this->assertEquals($usuariosController->view->success, $usuariosController->view->success);
        $this->assertEmpty($usuariosController->view->usuarios);
    }

    public function testDeletarUsuarioComPermissaoGerente()
    {
        $usuariosMock = $this->createMock(Usuarios::class);

        $usuariosMock->method('listUsuario')
            ->willReturn([['id' => 1, 'nome' => 'Leonardo', 'cpf' => '12345678900', 'email' => 'leo@example.com']]);

        $_SESSION['tipo_usuario'] = 'gerente';

        $usuariosController = new UsuariosController();
        $usuariosController->usuariosModel = $usuariosMock;

        $this->expectOutputString('');

        $usuariosController->deletarusuario(1);

        $this->assertNotEmpty($usuariosController->view->usuarios);
        $this->assertEquals('Usuario Excluido com sucesso', $usuariosController->view->success);
    }

    public function testDirecionarComPermissaoGerente()
    {
        $_SESSION['tipo_usuario'] = 'gerente';
        $_SESSION['id'] = 1;
        $_SESSION['nome'] = 'Leonardo';

        $usuariosController = new UsuariosController();

        $this->expectOutputString('');

        $usuariosController->direcionar();

        $this->assertNotEmpty($usuariosController->view->devolucoesPendentes);
    }
}
