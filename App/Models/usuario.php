<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {

    private $id;
    private $nome;
    private $email;
    private $senha;

    //atributos privados necessitam metodos magicos pra manipular
    public function __get($attr) {
        return $this->$attr;
    }
    
    public function __set($attr, $value) {
        $this->$attr = $value;
    }

    //salvar
    public function salvar() {

        $query = "
        INSERT INTO
            usuarios(nome, email, senha)
        VALUES
            (:nome, :email, :senha)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        return $this;
    }

    //validar se o cadastro pode ser feito
    public function validarCadastro() {
        $valido = true;

        if(strlen($this->__get('nome')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    //recuperar um usuario por email
    public function getUsuarioPorEmail() {
		$query = "
		SELECT nome, email FROM
			usuarios
		WHERE
			email = :email
		";

		$stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

    public function autenticar() {
        $query = "
        SELECT id, nome, email FROM
            usuarios
        WHERE
            email = :email and senha = :senha
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        //PRECISA UMA CORREÇÃO AQUI, LOG: 
        //Notice: Trying to access array offset on value of type bool in C:\www\twitter-clone\App\Models\Usuario.php on line 92
        if($usuario['id'] != ''&& $usuario['nome'] != '') {
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
        }

        return $this;
    }

    public function getAll() {
        
        $query = '
        SELECT 
            id, nome, email
        FROM
            usuarios
        WHERE
            nome like :nome
        ';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}

?>