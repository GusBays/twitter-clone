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
    public function salvaer() {

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


    //recuperar um usuario por email

}

?>