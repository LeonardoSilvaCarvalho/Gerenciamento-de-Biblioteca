<?php

namespace Tests\Models;

use App\Models\Livros;
use PHPUnit\Framework\TestCase;

class LivrosTest extends TestCase
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

    public function testGetLivros()
    {
        $livros = new Livros($this->db);
        $result = $livros->getLivros();


        $this->assertIsArray($result);


        foreach ($result as $livro) {
            $this->assertArrayHasKey('id', $livro);
            $this->assertArrayHasKey('titulo', $livro);
            $this->assertArrayHasKey('autor', $livro);
            $this->assertArrayHasKey('isbn', $livro);
            $this->assertArrayHasKey('disponivel', $livro);
        }
    }

    public function testSalvarLivro()
    {
        $livros = new Livros($this->db);


        $livros->__set('titulo', 'Livro de Teste');
        $livros->__set('autor', 'Autor Teste');
        $livros->__set('isbn', '1345682');

        $livros->salvar();

        $livroId = $livros->__get('id');
        $this->assertNotNull($livroId, 'ID do livro não foi atribuído após salvar.');

        $result = $livros->getLivrosById();

        $this->assertNotEmpty($result, 'O livro não foi encontrado no banco de dados.');
        $this->assertEquals('Livro de Teste', $result['titulo']);
        $this->assertEquals('Autor Teste', $result['autor']);
        $this->assertEquals('1345682', $result['isbn']);
    }

    public function testDeletarLivro()
    {
        $livros = new Livros($this->db);

        $livros->__set('titulo', 'Livro para Deletar');
        $livros->__set('autor', 'Autor Teste');
        $livros->__set('isbn', '9876543210');
        $livros->salvar();

        $livroId = $livros->__get('id');

        $livros->__set('id', $livroId);
        $livros->deletar();

        $livroDeletado = $livros->getLivrosById();
        $this->assertEmpty($livroDeletado);
    }

    public function testValidarCadastro()
    {
        $livros = new Livros($this->db);

        $livros->__set('titulo', 'Livro Teste');
        $livros->__set('autor', 'Autor Teste');
        $livros->__set('isbn', '1234567890');
        $validar = $livros->validarCadastro();
        $this->assertTrue($validar);

        $livros->__set('titulo', 'Te');
        $validar = $livros->validarCadastro();
        $this->assertFalse($validar);

        $livros->__set('autor', 'A');
        $validar = $livros->validarCadastro();
        $this->assertFalse($validar);
    }

    public function testGetIsbn()
    {
        $livros = new Livros($this->db);

        $livros->__set('titulo', 'Livro de Teste ISBN');
        $livros->__set('autor', 'Autor Teste');
        $livros->__set('isbn', '1234567890');
        $livros->salvar();

        $livroEncontrado = $livros->getIsbn();
        $this->assertNotEmpty($livroEncontrado);
        $this->assertEquals('1234567890', $livroEncontrado['isbn']);
    }

    public function testAtualizarDisponibilidade()
    {
        $livros = new Livros($this->db);

        $livros->__set('titulo', 'Livro de Teste Disponibilidade');
        $livros->__set('autor', 'Autor Teste');
        $livros->__set('isbn', '1234567890');
        $livros->salvar();

        $livroId = $livros->__get('id');

        $livros->atualizarDisponibilidade($livroId, false);

        $livroAtualizado = $livros->getLivrosById();
        $this->assertEquals(0, $livroAtualizado['disponivel']);
    }

    public function testGetLivrosFiltrados()
    {
        $livros = new Livros($this->db);

        $livros->__set('titulo', 'Livro Teste A');
        $livros->__set('autor', 'Autor Teste');
        $livros->__set('isbn', '1234567890');
        $livros->salvar();

        $livros->__set('titulo', 'Livro Teste B');
        $livros->__set('autor', 'Autor Teste');
        $livros->__set('isbn', '0987654321');
        $livros->salvar();

        $result = $livros->getLivrosFiltrados('A');

        $this->assertNotEmpty($result);
        foreach ($result as $livro) {
            $this->assertStringContainsString('A', $livro['nome_livro']);
        }
    }


}
