<?php
	class MySQLPDOController extends PDOController{
		public function __construct($config){
			parent::__construct($config);
		}

		//Se pueden factorizar las clases para cada uno de los controladores, solo se debe tener cuidado al hacer herencia
	} 
?> 