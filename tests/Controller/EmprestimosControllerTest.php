<?php

use PHPUnit\Framework\TestCase;
use App\Controller\EmprestimosController;

class EmprestimosControllerTest extends TestCase
{
    private $emprestimosController;
    private $emprestimosModelMock;

    protected function setUp(): void
    {
        $this->emprestimosModelMock = $this->createMock(\App\Models\Emprestimos::class);

        $this->emprestimosController = new EmprestimosController();
    }

    public function testEmprestimosComPermissaoDeAluno()
    {

        $_SESSION['tipo_usuario'] = 'aluno';
        $_SESSION['id'] = 3;

        $emprestimosMock = [[
            'id' => 3,
            'livro_id' => 3,
            'usuario_id' => 3,
            'dataEmprestimo' => '2024-11-10',
            'dataDevolucao' => '2024-11-20'
        ]];

        $this->emprestimosModelMock
            ->expects($this->once())
            ->method('getEmprestimosUsuario')
            ->with(1)
            ->willReturn($emprestimosMock);

        $this->emprestimosController->Emprestimos();

        $this->assertEquals($emprestimosMock, $this->emprestimosController->view->emprestimos);
    }
}
