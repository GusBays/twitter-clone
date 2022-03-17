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

    //listar usuarios na timeline tirando o autenticado
    public function getAll() {
        
        //faz uma consulta e uma sub consulta que conta se o usuario ja está seguindo ou não
        //isso serve pra tratar na view depois qual dos botões serão exibidos, seguir ou deixar de seguir
        //if seguindo_sn > 0, só exibe o deixar de seguir, if == 0, só exibe o seguir
        $query = '
        SELECT 
            u.id, 
            u.nome, 
            u.email,
            (
                SELECT 
                    count(*)
                FROM
                    usuarios_seguidores as us
                WHERE
                    us.id_usuario = :id_usuario 
                AND
                    us.id_usuario_seguindo = u.id
            ) as seguindo_sn
        FROM
            usuarios as u
        WHERE
            u.nome like :nome
        AND
            u.id != :id_usuario
        ';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //seguir usuarios
    public function seguirUsuario($id_usuario_seguindo) {
        $query = "
        INSERT INTO
            usuarios_seguidores(id_usuario, id_usuario_seguindo)
        VALUES
            (:id_usuario, :id_usuario_seguindo)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
    }

    //deixar de seguir
    public function deixarSeguirUsuario($id_usuario_seguindo) {
        $query = "
        DELETE FROM
            usuarios_seguidores
        WHERE
            id_usuario = :id_usuario
        AND
            id_usuario_seguindo = :id_usuario_seguindo
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
    }

    //infomações do usuário
    public function getInfoUsuario() {
        $query = "
        SELECT 
            nome
        FROM
            usuarios
        WHERE
            id = :id_usuario";

        $stmt = $this->db->prepare($query);   
        $stmt->bindValue(':id_usuario', $this->__get('id'));     
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    //total de tweets
    public function getTotalTweets() {
        $query = "
        SELECT 
            count(*) as total_tweets
        FROM
            tweets
        WHERE
            id_usuario = :id_usuario";

        $stmt = $this->db->prepare($query);   
        $stmt->bindValue(':id_usuario', $this->__get('id'));     
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    //total de usuários seguindo
    public function getTotalSeguindo() {
        $query = "
        SELECT 
            count(*) as total_seguindo
        FROM
            usuarios_seguidores
        WHERE
            id_usuario = :id_usuario";

        $stmt = $this->db->prepare($query);   
        $stmt->bindValue(':id_usuario', $this->__get('id'));     
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    //total de seguidores
    public function getTotalSeguidores() {
        $query = "
        SELECT 
            count(*) as total_seguidores
        FROM
            usuarios_seguidores
        WHERE
            id_usuario_seguindo = :id_usuario";

        $stmt = $this->db->prepare($query);   
        $stmt->bindValue(':id_usuario', $this->__get('id'));     
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}

?>