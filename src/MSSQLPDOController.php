<?php
	/*
		@author: captaincode0
	*/
	class MSSQLPDOController extends PDOController{
		public function __construct($config){
			parent::__construct("sqlsrv", $config);
		}
	}
?>