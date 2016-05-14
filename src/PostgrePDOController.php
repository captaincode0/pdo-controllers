<?php
	/*
		@author: captaincode0
	*/
	class PostgrePDOController extends PDOController{
		public function __construct($config){
			parent::__construct("pgsql", $config);
		}
	} 
?>