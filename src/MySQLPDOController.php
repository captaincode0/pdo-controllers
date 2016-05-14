<?php
	/*
		@author: captaincode0
	*/
	class MySQLPDOController extends PDOController{
		public function __construct($config){
			parent::__construct("mysql", $config);
		}

		//Se pueden factorizar las clases para cada uno de los controladores, solo se debe tener cuidado al hacer herencia
	} 
?> 