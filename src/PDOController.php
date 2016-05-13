<?php
	class PDOController{
		private $user;
		private $host;
		private $password;
		private $databse;
		private $pdoconfig;

		public function __construct($config){
			//desempacar las configuraciones
			/*
				Añadir estos métodos
				build(); //verifica si se puede construir el objeto PDO actual.
				destroy(); //Destruye el objeto PDO actual.
				getPDOObject(); //retorna el objeto PDO para ser usado en transacciones, procedimientos almacenados y declaraciones.
				getMatrix($query); //obtiene una matriz --un conjunto de arreglos en php, para ser pasados a la vista.
				filterMatrix($matrix, $fields); //filtra una matriz en caso de ser necesario por nombres de las columnas.
				exec($query); //ejecuta una consulta y devuelve un resultado numérico, que son las filas que fueron afectadas.
			*/
		}
	}
?>