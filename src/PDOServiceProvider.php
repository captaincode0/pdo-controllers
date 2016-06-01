<?php
	/*
		@author: captaincode0
	*/

	include "PDOController.php";
	include "MySQLPDOController.php";
	include "PostgrePDOController.php";
	include "MSSQLPDOController.php";
	include "PDOControllerNotBuildedException.php";

	class PDOServiceProvider{
		public static $_iregister = array(); //registro global de instancias del controlador
		public static $_instances = array( //número de instancias del controlador actual
			"mysql" => 0,
			"mssql" => 0,
			"postgre" => 0
		);
		private $pdocontroller; //nombre del controlador actual
		private $config; //configuraciones del controlador como usuario, host, base de datos y sus propiedades
		private $prefix; //prefijo actual del controlador mysql0, mysql1, postgre0
		public static $_pdocontroller;

		/*
			El constructor de esta clase recibe el nombre del controlador que se desea construir y
			su arreglo de configuraciones
				$pdocontroller: mssql, postgre, mysql
				$config: configuraciones del driver, para más detalles, vea la sección de PDO Drivers en el sitio de php.
		*/
		public function __construct($pdocontroller, $config){
			$this->pdocontroller = $pdocontroller;
			$this->config = $config;
			self::$_pdocontroller = $this->pdocontroller;
		}

		/*
			@{magic method}
			__get(name, value): retorna el valor de la propiedad de la clase que se está obteniendo, 
				solo sirve para variables de instancia --atributos
		*/ 
		public function __get($w){
			if($w === "pdocontroller")
				return $this->pdocontroller;
			else if($w  === "config")
				return $this->config;
		} 

		/*
			@{magic method}
			__set(name, value): verifica que la propiedad que está siendo asignada en tiempo de ejecución, 
				y retorna la igualación de su valor.

			Para que tanto encapsulamiento :v :v
		*/
		public function __set($w, $z){
			if($w === "pdocontroller"
				&& is_string($z)
				&& in_array($z, array("mysql", "mssql", "postgre")))
				return $this->pdocontroller = $z;
			else if($w === "config" 
				&& is_array($z))
				return $this->config = $z;
		}

		/*
			@{static}
			register(SGBDName, Configurations): construye un controlador PDO 
		*/

		public static function register($w, $x){
			$provider = new PDOServiceProvider($w, $x);
			$controller = $provider->build();

			if($controller)
				return $controller;
			else
				echo "La instancia del controlador pdo de ".self::$_pdocontroller." no fue construida exitosamente <br>";
		}

		public function factoryControllers(){
			$tmparray = PDOServiceProvider::_iregister;
			return $tmparray; 
		}

		public function getInstance($z){
			return (in_array($z, self::$_instances))?self::$_instances[$z]:false;
		}

		/*
			@{self}
			build: construye la instancia del controlador añadiendo un prefijo, ej. mysql0, mysql1, mssql0 que es un contador
			que contiene el número de instancias de controladores pdo que se han hecho.
		*/
		public function build(){
			try{
				$this->prefix = $this->pdocontroller.self::$_instances[$this->pdocontroller];
				$ccontroller; //controlador actual

				if($this->pdocontroller === "mysql")
					$ccontroller = new MySQLPDOController($this->config);
				else if($this->pdocontroller === "mssql")
					$ccontroller = new MSSQLPDOController($this->config);
				else if($this->pdocontroller === "postgre")
					$ccontroller = new PostgrePDOController($this->config);
				else
					throw new PDOControllerNotBuildedException();

				//introduce al registro interno [iregister] el controlador principal
				self::$_iregister[] = array($this->prefix => $ccontroller);

				//incrementa el número de instancias
				self::$_instances[$this->pdocontroller] = ++self::$_instances[$this->pdocontroller];
				
				//retorna el controlador actual
				return $ccontroller;
			}
			catch(PDOControllerNotBuildedException $ex){
				echo $ex->getMessage()." <br>";
				return false;
			}		
		}
	}
?>