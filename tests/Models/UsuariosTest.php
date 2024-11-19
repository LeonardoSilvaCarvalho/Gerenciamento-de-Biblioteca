<?php
use PHPUnit\Framework\TestCase;
use App\Models\Usuarios;

class UsuariosTest extends TestCase
{
    private $db;
    private $usuario;

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

        $this->usuario = new Usuarios($this->db);
    }

    public function testSalvar()
    {
        $this->usuario->__set('nome', 'João Silva');
        $this->usuario->__set('cpf', '12345678900');
        $this->usuario->__set('email', 'joao.silva@example.com');
        $this->usuario->__set('senha', 'senha123');
        $this->usuario->__set('tipo_usuario', 'aluno');

        $this->usuario->salvar();

        $this->assertNotNull($this->usuario->__get('id'), 'ID não gerado, falha ao salvar usuário.');
    }

    public function testDeletar()
    {

        $this->usuario->__set('nome', 'Maria Oliveira');
        $this->usuario->__set('cpf', '12345678901');
        $this->usuario->__set('email', 'maria.oliveira@example.com');
        $this->usuario->__set('senha', 'senha123');
        $this->usuario->__set('tipo_usuario', 'aluno');
        $this->usuario->salvar();

        $usuarioId = $this->usuario->__get('id');

        $this->usuario->__set('id', $usuarioId);
        $resultado = $this->usuario->deletar();

        $this->assertTrue($resultado, 'Erro ao excluir usuário.');
    }

    public function testValidarCadastro()
    {

        $this->usuario->__set('nome', 'João Silva');
        $this->usuario->__set('cpf', '12345678900');
        $this->usuario->__set('email', 'joao.silva@example.com');
        $this->usuario->__set('senha', 'senha123');
        $this->assertTrue($this->usuario->validarCadastro(), 'Cadastro válido falhou.');

        $this->usuario->__set('nome', 'Jo');
        $this->assertFalse($this->usuario->validarCadastro(), 'Cadastro inválido não foi detectado.');
    }

    public function testAutenticar()
    {
        $this->usuario->__set('id', '2');
        $this->usuario->__set('nome', 'Fernanda');
        $this->usuario->__set('email', 'fernanda@gmail.com');
        $this->usuario->__set('senha', '1234');
        $this->usuario->__set('tipo_usuario', 'gerente');
        $usuarioAutenticado = $this->usuario->autenticar();

        $this->assertNotNull($usuarioAutenticado, 'Falha na autenticação de usuário.');

        $this->usuario->__set('senha', 'senhaerrada');
        $usuarioAutenticadoErrado = $this->usuario->autenticar();
        $this->assertNotNull($usuarioAutenticadoErrado, 'Autenticação não deveria ter sucesso com dados errados.');
    }

    protected function tearDown(): void
    {
        $this->db = null;
    }
}
