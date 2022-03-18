<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action { 

    public function timeline() {
            
        $this->validaAutenticacao();

        //recuperar os tweets e encaminha pra view timeline
        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);

        //define limite de 10 tweets por pagoina, verifica a pagina que o user está, e calcula o deslocamento no offset
        $limit = 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        //$tweets = $tweet->getAll();

        //pegar tweets por pagina
        $tweets = $tweet->getPaginate($limit, $offset);

        //pega o numero de tweets para usar na view depois
        $total_tweets = $tweet->getTotalRegistros();

        //cria o atributo dinamico pra usar na view como numero total de paginas, usando os tweets dividido pelo limite
        $this->view->total_de_paginas = ceil($total_tweets['total'] / $limit);

        //cria um atributo dinamico na view para pegar a página ativa e estilizar nos botoes
        $this->view->page_active = $page;
        
        //cria um atributo dinamico na view que recebe os tweets do array
        $this->view->tweets = $tweets;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        //cria variaveis na view para poder acessar as informações direto lá
        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');
    }

    public function tweet() {

        $this->validaAutenticacao();
            
        //cria a instancia com a conexao do db feita
        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header('Location: /timeline');
    }

    public function validaAutenticacao() {

        session_start();

        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['id']) || $_SESSION['id'] == '') {
            header('Location: /?login=erro');
        } else {
            
        }
    }

    public function quemSeguir() {

        $this->validaAutenticacao();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = array();

        if($pesquisarPor != '') {
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();

        }
         
        $this->view->usuarios = $usuarios;

        $usuarioAutenticado = Container::getModel('Usuario');
        $usuarioAutenticado->__set('id', $_SESSION['id']);

        //cria variaveis na view para poder acessar as informações direto lá
        $this->view->info_usuario = $usuarioAutenticado->getInfoUsuario();
        $this->view->total_tweets = $usuarioAutenticado->getTotalTweets();
        $this->view->total_seguindo = $usuarioAutenticado->getTotalSeguindo();
        $this->view->total_seguidores = $usuarioAutenticado->getTotalSeguidores();


        $this->render('quemSeguir');
    }

    public function acao() {

        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario_seguindo);

        } else if ($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($id_usuario_seguindo);

        } 

        header('Location: /quem_seguir');
    }

    public function deletar() {

        $this->validaAutenticacao();
        
        //cria uma instancia de tweet
        $tweet = Container::getModel('Tweet');

        //seta o id do tweet com o que veio do input do botao na view timeline
        $tweet->__set('id', $_POST['id']);

        //chama a função pra deletar o tweet passando o id setado antes
        $tweet->deleteTweet($tweet->__get('id'));

        //retorna pra timeline atualizada
        header('Location: /timeline');
    }
}

?>