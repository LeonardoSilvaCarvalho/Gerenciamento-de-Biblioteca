<?php

namespace App\Models;

use MF\Model\Model;

class Livros extends Model
{
    private $id;
    private $titulo;
    private $autor;
    private $isbn;
    private $disponivel;

    public function getLivros()
    {
        $query = "SELECT id, titulo, autor, isbn, disponivel  FROM livros";
        return $this->db->query($query)->fetchAll();
    }

    public function __get($atributo)
    {

        if (isset($this->$atributo)) {
            return $this->$atributo;
        }
        return null;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function salvar()
    {
        if (!$this->getDisponivel('disponivel')) {
            $this->setDisponivel('disponivel', 1);
        }

        $query = "INSERT INTO livros(titulo, autor, isbn, disponivel) VALUES (:titulo, :autor, :isbn, :disponivel)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':titulo', $this->__get('titulo'));
        $stmt->bindValue(':autor', $this->__get('autor'));
        $stmt->bindValue(':isbn', $this->__get('isbn'));
        $stmt->bindValue(':disponivel', $this->__get('disponivel'));
        $stmt->execute();

        $this->setId($this->db->lastInsertId());

        return $this;
    }

    public function deletar()
    {
        $query = "DELETE FROM livros WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            echo 'Erro ao excluir';
            return false;
        }
    }

    public function validarCadastro()
    {
        $validar = true;

        // Validar título
        if (strlen($this->getTitulo()) < 3) {
            $validar = false;
        }

        // Validar autor
        if (strlen($this->getAutor()) < 3) {
            $validar = false;
        }

        // Validar ISBN
        if (empty($this->getIsbnlivro() || strlen($this->getIsbnlivro() < 10))) {
            $validar = false;
        }

        return $validar;
    }

    public function getIsbn()
    {
        $query = "SELECT id, titulo, autor, isbn FROM livros WHERE isbn = :isbn";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':isbn', $this->getIsbnlivro());
        $stmt->execute();

        // Evitar problemas com valores não encontrados, retornando false se não encontrar
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: false;
    }

    public function atualizarDisponibilidade($id, $disponivel)
    {
        $query = "UPDATE livros SET disponivel = :disponivel WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':disponivel', $disponivel, \PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function getLivrosById()
    {
        $query = "SELECT * FROM livros WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->getId());
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getLivrosFiltrados($search)
    {
        $query = "SELECT l.*, l.titulo AS nome_livro FROM livros l";

        if (!empty($search)) {
            $query .= " WHERE l.titulo LIKE :search";
        }

        $stmt = $this->db->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Getters e Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getAutor()
    {
        return $this->autor;
    }

    public function setAutor($autor)
    {
        $this->autor = $autor;
    }

    public function getIsbnlivro()
    {
        return $this->isbn;
    }

    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    }

    public function getDisponivel()
    {
        return $this->disponivel;
    }

    public function setDisponivel($disponivel, $valor)
    {
        $this->disponivel = $valor;
    }



}
