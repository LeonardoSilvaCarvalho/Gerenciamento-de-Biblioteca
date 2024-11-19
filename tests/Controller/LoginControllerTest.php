<?php

use PHPUnit\Framework\TestCase;
use App\Controller\LoginController;
use MF\Model\Container;

class LoginControllerTest extends TestCase
{
    private $loginController;

    protected function setUp(): void
    {
        $this->loginController = new LoginController();
    }

    public function testIndexSemLoginParametro()
    {

        $_GET['login'] = '';

        $loginControllerMock = $this->getMockBuilder(\App\Controller\LoginController::class)
            ->onlyMethods(['render'])
            ->getMock();

        $loginControllerMock->expects($this->once())
            ->method('render')
            ->with('index');

        $loginControllerMock->index();

        $this->assertEquals('', $loginControllerMock->view->login);
    }

    public function testIndexComLoginParametro()
    {
        $_GET['login'] = 'erro';

        $loginControllerMock = $this->getMockBuilder(\App\Controller\LoginController::class)
            ->onlyMethods(['render'])
            ->getMock();

        $loginControllerMock->expects($this->once())
            ->method('render')
            ->with('index');

        $loginControllerMock->index();

        $this->assertEquals('erro', $loginControllerMock->view->login);
    }

}

