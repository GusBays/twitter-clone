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

        $tweets = $tweet->getAll();

        //cria um atributo dinamico na view que recebe os tweets do array
        $this->view->tweets = $tweets;

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
            $usuarios = $usuario->getAll();

        }
         
        $this->view->usuarios = $usuarios;

        $this->render('quemSeguir');
    }
}

?>