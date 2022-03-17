<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {
    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    //atributos privados necessitam metodos magicos pra manipular
    public function __get($attr) {
        return $this->$attr;
    }
        
    public function __set($attr, $value) {
        $this->$attr = $value;
    }

    public function salvar() {
        $query = "
        INSERT INTO tweets(id_usuario, tweet)
        VALUES (:id_usuario, :tweet)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;
    }

    public function getAll() {

        $query = "
            select
                t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%y %H:%i') as data  
            from 
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            where
                t.id_usuario = :id_usuario
            OR
                t.id_usuario in (
                    SELECT
                        id_usuario_seguindo
                    FROM
                        usuarios_seguidores
                    WHERE
                        id_usuario = :id_usuario
                )
            order by
                t.data desc
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    //recuperar tweets com paginação
    public function getPaginate($limit, $offset) {

        $query = "
            select
                t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%y %H:%i') as data  
            from 
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            where
                t.id_usuario = :id_usuario
            OR
                t.id_usuario in (
                    SELECT
                        id_usuario_seguindo
                    FROM
                        usuarios_seguidores
                    WHERE
                        id_usuario = :id_usuario
                )
            order by
                t.data desc
            limit
                $limit
            offset
                $offset
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

?>