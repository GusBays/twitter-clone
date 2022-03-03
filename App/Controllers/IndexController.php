<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		//cria um atributo dinamico na view chamado login, seta ele com o valor recebido
		//se nao vier nada, fica vazio, isso será verificado no index.phtml pra exibir erro
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->render('index');
	}

	public function inscreverse() {

		//define valores default aos campos caso acesse a rota direto
		$this->view->usuario = array(
			'nome' => '',
			'email' => ''
		);

		//define como padrao o erroCadastro false pra que nao exiba mensagem de erro ao acessar a rota
		$this->view->erroCadastro = false;

		$this->render('inscreverse');
	}

	public function registrar() {

		//utiliza o container pra criar a instacia de usuario baseado no modelo feito no framework
		$usuario = Container::getModel('Usuario');

		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));

		//verifica se o cadastro é valido pelo metodo validarCadastro
		// e seleciona no banco os cadastros filtrando por email, se ja existir pula
		if($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {
			
			$usuario->salvar();

			$this->render('cadastro');
		} else {

			//recupera os dados digitados pelo usuario no campos pra não precisar escrever de novo em caso de erro
			$this->view->usuario = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email']
			);

			$this->view->erroCadastro = true;

			$this->render('inscreverse');
		}
	}
}

?>