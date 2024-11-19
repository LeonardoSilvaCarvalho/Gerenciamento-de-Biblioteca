<?php

namespace Tests\Models;

use App\Models\Emprestimos;
use PHPUnit\Framework\TestCase;
use Mf\Model\Model;

class EmprestimosTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $dsn = 'mysql:host=localhost;dbname=biblioteca';
        $username = 'root';
        $password = 'Bb&1101010';

        try {
            $this->db = new \PDO($dsn, $username, $password);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
            exit;
        }
    }

    public function testGetTodosEmprestimos()
    {
        $emprestimos = new Emprestimos($this->db);
        $result = $emprestimos->getTodosEmprestimos();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testGetEmprestimosFiltrados()
    {
        $emprestimos = new Emprestimos($this->db);
        $result = $emprestimos->getEmprestimosFiltrados($_GET['Fernanda']);

        $this->assertIsArray($result);
        foreach ($result as $emprestimo) {
            $this->assertStringContainsString('Fernanda', $emprestimo['nome_usuario']);
        }
    }

    public function testRegistrarEmprestimo()
    {
        $emprestimos = new Emprestimos($this->db);
        $emprestimos->setLivroId(3);
        $emprestimos->setUsuarioId(2);
        $emprestimos->setDataEmprestimo('2023-11-01');
        $emprestimos->setDataDevolucao('2023-12-01');

        $result = $emprestimos->registrarEmprestimo();
        $this->assertTrue($result);
    }

    public function testSolicitarDevolucao()
    {
        $emprestimos = new Emprestimos($this->db);
        $emprestimos->setLivroId(3);
        $emprestimos->setUsuarioId(2);

        $result = $emprestimos->solicitarDevolucao();
        $this->assertTrue($result);
    }

    public function testRegistrarDevolucao()
    {
        $emprestimos = new Emprestimos($this->db);
        $emprestimos->setLivroId(3);
        $emprestimos->setUsuarioId(2);

        $result = $emprestimos->registrarDevolucao();
        $this->assertTrue($result);
    }

    public function testVerificarEmprestimo()
    {
        $emprestimos = new Emprestimos($this->db);
        $emprestimos->setLivroId(1);
        $emprestimos->setUsuarioId(2);

        $result = $emprestimos->verificarEmprestimo();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
    }

    public function testVerificarDevolucaoPendente()
    {
        $emprestimos = new Emprestimos($this->db);

        $result = $emprestimos->verificarDevolucaoPendente();
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }


}
