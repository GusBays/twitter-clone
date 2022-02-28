<?php

namespace MF\Model;

use App\Connection;

class Container {

	//retorna a instancia do objeto solicitado já criado e com a conexão do banco estabelecida
	public static function getModel($model) {
		$class = "\\App\\Models\\".ucfirst($model);
		$conn = Connection::getDb();

		return new $class($conn);
	}
}


?>