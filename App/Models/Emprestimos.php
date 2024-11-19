<?php

namespace App\Models;

use MF\Model\Model;

class Emprestimos extends Model
{
    private $livro_id;
    private $usuario_id;
    private $dataEmprestimo;
    private $dataDevolucao;

    public function getTodosEmprestimos()
    {
        $query = "SELECT e.*, l.titulo AS nome_livro, u.nome AS nome_usuario 
                  FROM emprestimos e
                  JOIN livros l ON e.livro_id = l.id
                  JOIN usuarios u ON e.usuario_id = u.id
                  WHERE e.devolvido = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getEmprestimosFiltrados($search)
    {
        $query = "SELECT e.*, l.titulo AS nome_livro, u.nome AS nome_usuario 
              FROM emprestimos e
              JOIN livros l ON e.livro_id = l.id
              JOIN usuarios u ON e.usuario_id = u.id
              WHERE e.devolvido = 0";

        if (!empty($search)) {
            $query .= " AND u.nome LIKE :search";
        }

        $stmt = $this->db->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($result)) {
            return [];
        }
        return $result;
    }

    public function getEmprestimosUsuario($usuario_id)
    {
        $query = "SELECT e.*, l.titulo AS nome_livro 
                  FROM emprestimos e
                  JOIN livros l ON e.livro_id = l.id
                  WHERE e.usuario_id = :usuario_id AND e.devolvido = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function registrarEmprestimo()
    {
        $query = "INSERT INTO emprestimos(livro_id, usuario_id, dataEmprestimo, dataDevolucao) 
              VALUES (:livro_id, :usuario_id, :dataEmprestimo, :dataDevolucao)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':livro_id', $this->getLivroId());
        $stmt->bindValue(':usuario_id', $this->getUsuarioId());
        $stmt->bindValue(':dataEmprestimo', $this->getDataEmprestimo());
        $stmt->bindValue(':dataDevolucao', $this->getDataDevolucao());
        return $stmt->execute();
    }

    public function solicitarDevolucao()
    {
        $query = "UPDATE emprestimos SET devolucao_solicitada = 1 
                  WHERE livro_id = :livro_id AND usuario_id = :usuario_id AND devolvido = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':livro_id', $this->getLivroId());
        $stmt->bindValue(':usuario_id', $this->getUsuarioId());
        return $stmt->execute();
    }

    public function registrarDevolucao()
    {
        $query = "UPDATE emprestimos SET devolvido = 1, devolucao_solicitada = 0 
                  WHERE livro_id = :livro_id AND usuario_id = :usuario_id AND devolvido = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':livro_id', $this->getLivroId());
        $stmt->bindValue(':usuario_id', $this->getUsuarioId());
        return $stmt->execute();
    }

    public function verificarEmprestimo()
    {
        $query = "SELECT * FROM emprestimos WHERE livro_id = :livro_id AND usuario_id = :usuario_id AND devolvido = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':livro_id', $this->getLivroId());
        $stmt->bindValue(':usuario_id', $this->getUsuarioId());
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function verificarDevolucaoPendente()
    {
        $query = "SELECT * FROM emprestimos WHERE devolucao_solicitada = 1";
        $stmt = $this->db->query($query);
        return $stmt->rowCount() > 0;
    }

    public function getLivroId()
    {
        return $this->livro_id;
    }

    public function setLivroId($livro_id)
    {
        $this->livro_id = $livro_id;
    }

    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }

    public function getDataEmprestimo()
    {
        return $this->dataEmprestimo;
    }

    public function setDataEmprestimo($dataEmprestimo)
    {
        $this->dataEmprestimo = $dataEmprestimo;
    }

    public function getDataDevolucao()
    {
        return $this->dataDevolucao;
    }

    public function setDataDevolucao($dataDevolucao)
    {
        $this->dataDevolucao = $dataDevolucao;
    }

}
