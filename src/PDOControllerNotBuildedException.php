<?php
	/*
		@author: captaincode0
	*/
	class PDOControllerNotBuildedException extends Exception{
		public function __construct($message = "El controlador no puede ser construido, porque los parámetros para construirlo son erroneos", $code = 10, Exception $previus = null){
			parent::__construct($message, $code, $previus);
		}

		public function __toString(){
			return __class__."[{$this->code}]: {$this->message}\n";
		}
	}