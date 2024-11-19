<?php

namespace App\Models;

use MF\Model\Model;

class Usuarios extends Model
{

    private $id;
    private $nome;
    private $cpf;
    private $email;
    private $senha;
    private $tipo_usuario;

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function listUsuario()
    {
        $query = "SELECT * FROM usuarios WHERE tipo_usuario != 'gerente'";
        return $this->db->query($query)->fetchAll();
    }

    public function getUsuariosFiltrados($search)
    {
        $query = "SELECT u.*, u.nome AS nome_usuario 
              FROM usuarios u";

        if (!empty($search)) {
            $query .= " WHERE u.nome LIKE :search";
        }

        $stmt = $this->db->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function salvar()
    {

        $query = "INSERT INTO usuarios(nome, cpf, email, senha, tipo_usuario) VALUES (:nome, :cpf, :email, :senha, :tipo_usuario)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':cpf', $this->__get('cpf'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->bindValue(':tipo_usuario', $this->__get('tipo_usuario'));
        if ($stmt->execute()) {
            $this->__set('id', $this->db->lastInsertId());
        }

        return $this;
    }

    public function deletar()
    {
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'), \PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            echo 'erro ao excluir';
            return false;
        }
    }

    public function validarCadastro()
    {
        $validar = true;

        if (strlen($this->__get('nome')) < 3) {
            $validar = false;
        }

        if (strlen($this->__get('email')) < 3) {
            $validar = false;
        }

        if (strlen($this->__get('cpf')) < 3) {
            $validar = false;
        }

        if (strlen($this->__get('senha')) < 3) {
            $validar = false;
        }

        return $validar;
    }

    public function getUsuarioPorEmailECPF()
    {
        $query = "SELECT nome, email, cpf FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function cpfExiste()
    {
        $query = "SELECT id FROM usuarios WHERE cpf = :cpf";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':cpf', $this->__get('cpf'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function autenticar()
    {
        $query = "SELECT id, nome, email, tipo_usuario FROM usuarios WHERE email = :email AND senha = :senha";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($usuario) {
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
            $this->__set('tipo_usuario', $usuario['tipo_usuario']);
        }

        return $usuario;
    }


}
