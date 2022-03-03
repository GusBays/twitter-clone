<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {

    public function autenticar() {

        //chama o metodo container pra instanciar o objeto ja com a conexao do banco
        $usuario = Container::getModel('Usuario');

        //define os atributos da instancia com o que foi recebido via post no form
        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', $_POST['senha']);

        $usuario->autenticar();

        if(!empty($usuario->__get('id')) && !empty($usuario->__get('nome'))) {
            
            session_start();

            //define o ID e nome da sessão de acordo com o usuário que logou
            //serve pra evitar que acesse as rotas protegidas direto pelo link
            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');

            header('Location: /timeline');

        } else {
            //caso de erro na autenticação, redireciona pra pagina com parametro de erro
            header('Location: /?login=erro');
        }
    }

    public function sair() {
        
        session_start();
        session_destroy();

        header('Location: /');
    }
}

?>