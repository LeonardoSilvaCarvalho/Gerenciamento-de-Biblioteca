<?php
namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap
{
    protected function initRoutes()
    {
        //Login
        $routes['login'] = [
            'route' => '/',
            'controller' => 'LoginController',
            'action' => 'index'
        ];
        // authentication – login
        $routes['autenticar'] = [
            'route' => '/autenticar',
            'controller' => 'AuthController',
            'action' => 'autenticar'
        ];
        //home
        $routes['home'] = [
            'route' => '/home',
            'controller' => 'UsuariosController',
            'action' => 'direcionar'
        ];
        $routes['sair'] = [
            'route' => '/sair',
            'controller' => 'AuthController',
            'action' => 'sair'
        ];

        //books
        $routes['livros'] = [
            'route' => '/livros',
            'controller' => 'LivrosController',
            'action' => 'Livros'
        ];
        $routes['cadastrarlivro'] = [
            'route' => '/cadastrarlivro',
            'controller' => 'LivrosController',
            'action' => 'CadastrarLivros'
        ];
        $routes['registrarlivro'] = [
            'route' => '/registrarlivro',
            'controller' => 'LivrosController',
            'action' => 'registrarlivro'
        ];
        $routes['deletarlivro'] = [
            'route' => '/deletarlivro/{id}',
            'controller' => 'LivrosController',
            'action' => 'deletarlivro',
        ];
        $routes['emprestar'] = [
            'route' => '/emprestar/{idlivro}/{idusuario}',
            'controller' => 'LivrosController',
            'action' => 'emprestar',
        ];

        //lending
        $routes['emprestimos'] = [
            'route' => '/emprestimos',
            'controller' => 'EmprestimosController',
            'action' => 'Emprestimos'
        ];
        //return
        $routes['devolver'] = [
            'route' => '/livros/solicitarDevolucao/{idLivro}',
            'controller' => 'LivrosController',
            'action' => 'solicitarDevolucao'
        ];
        $routes['confirmardevolver'] = [
            'route' => '/livros/confirmarDevolucao/{idLivro}/{idUsuario}',
            'controller' => 'LivrosController',
            'action' => 'confirmarDevolucao'
        ];

        //users
        $routes['usuarios'] = [
            'route' => '/usuarios',
            'controller' => 'UsuariosController',
            'action' => 'Usuarios'
        ];
        $routes['inscreverse'] = [
            'route' => '/inscreverse',
            'controller' => 'UsuariosController',
            'action' => 'increverse'
        ];
        $routes['registrar'] = [
            'route' => '/registrar',
            'controller' => 'UsuariosController',
            'action' => 'registrar'
        ];
        $routes['deletarusuario'] = [
            'route' => '/deletarusuario/{id}',
            'controller' => 'UsuariosController',
            'action' => 'deletarusuario',
        ];

        $this->setRoutes($routes);
    }
}
