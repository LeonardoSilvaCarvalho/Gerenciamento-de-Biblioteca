<?php

use PHPUnit\Framework\TestCase;
use App\Controller\AuthController;
use MF\Model\Container;

class AuthControllerTest extends TestCase
{
    protected $authController;

    protected function setUp(): void
    {
        $this->authController = new AuthController();
    }

    public function testAutenticarComSucesso()
    {
        $usuarioMock = $this->createMock(\App\Models\Usuarios::class);

        $usuarioMock->method('__get')
            ->willReturnMap([
                ['id', 2],
                ['email', 'fernanda@gmail.com'],
                ['senha', '1234'],
                ['nome', 'fernanda'],
                ['tipo_usuario', 'gerente']
            ]);

        $usuarioMock->method('autenticar')
            ->with($this->equalTo('fernanda@gmail.com'), $this->equalTo('1234'))
            ->willReturn(true);

        $authControllerMock = $this->getMockBuilder(\App\Controller\AuthController::class)
            ->onlyMethods(['redirect'])
            ->getMock();

        $authControllerMock->expects($this->once())
            ->method('redirect')
            ->with($this->equalTo('/home'));

        Container::getModel('Usuarios');

        $_POST['email'] = 'fernanda@gmail.com';
        $_POST['senha'] = '1234';

        $authControllerMock->autenticar();
    }


    public function testAutenticarComErro()
    {

        $usuarioMock = $this->createMock(\App\Models\Usuarios::class);
        $usuarioMock->method('__get')->willReturnMap([['id', null], ['nome', null]]);
        $usuarioMock->method('autenticar')->willReturn(false);


        Container::getModel('Usuarios', $usuarioMock);


        $_POST['email'] = 'teste@gmail.com';
        $_POST['senha'] = 'senha123';


        $this->authController->autenticar();


        $this->assertContains('Location: /?login=erro', xdebug_get_headers());
    }

    public function testSair()
    {

        $_SESSION['id'] = 1;
        $_SESSION['nome'] = 'teste';
        $_SESSION['tipo_usuario'] = 'teste';

        $this->authController->sair();

        session_start();

        session_destroy();

        $this->assertEmpty($_SESSION);
        $this->assertContains('Location: /', xdebug_get_headers());
    }
}
