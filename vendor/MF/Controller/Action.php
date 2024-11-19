<?php

namespace MF\Controller;

abstract class Action {

    public $view;

    public function __construct(){
        $this->view = new \stdClass();
    }

    protected function render($view, $layout = 'layout') {
        $this->view->page = $view;
        if(file_exists("../App/Views/".$layout.".phtml")){
            require_once "../App/Views/".$layout.".phtml";
        }else {
            $this->content();
        }
    }

    protected function content(){
        $classAtual = get_class($this);
        $classAtual = str_replace('App\\Controller\\', '', $classAtual);
        $classAtual = strtolower(str_replace('Controller', '', $classAtual));

        // Mapeamento de diretórios de visualizações
        $viewDirectories = [
            'emprestimos' => 'emprestimos',

        ];

        // Verifica se há um mapeamento específico para o arquivo de visualização
        $viewDirectory = isset($viewDirectories[$this->view->page]) ? $viewDirectories[$this->view->page] : $classAtual;

        $viewPath = "../App/Views/".$viewDirectory."/".$this->view->page.".phtml";

        if (!file_exists($viewPath)) {
            die("O arquivo de visualização não foi encontrado: " . $viewPath);
        }

        require_once $viewPath;
    }

    protected function verificaPermissao($permitidos)
    {
        $tipoUsuario = $_SESSION['tipo_usuario'] ?? null;
        if (!in_array($tipoUsuario, $permitidos)) {
            header("Location: /acesso-negado");
            exit;
        }
    }

}