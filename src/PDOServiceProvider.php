<?php
	/*
		                                 __                __                                      __           
		                                /  |              /  |                                    /  |          
		  _______   ______    ______   _$$ |_     ______  $$/  _______    _______   ______    ____$$ |  ______  
		 /       | /      \  /      \ / $$   |   /      \ /  |/       \  /       | /      \  /    $$ | /      \ 
		/$$$$$$$/  $$$$$$  |/$$$$$$  |$$$$$$/    $$$$$$  |$$ |$$$$$$$  |/$$$$$$$/ /$$$$$$  |/$$$$$$$ |/$$$$$$  |
		$$ |       /    $$ |$$ |  $$ |  $$ | __  /    $$ |$$ |$$ |  $$ |$$ |      $$ |  $$ |$$ |  $$ |$$    $$ |
		$$ \_____ /$$$$$$$ |$$ |__$$ |  $$ |/  |/$$$$$$$ |$$ |$$ |  $$ |$$ \_____ $$ \__$$ |$$ \__$$ |$$$$$$$$/ 
		$$       |$$    $$ |$$    $$/   $$  $$/ $$    $$ |$$ |$$ |  $$ |$$       |$$    $$/ $$    $$ |$$       |
		 $$$$$$$/  $$$$$$$/ $$$$$$$/     $$$$/   $$$$$$$/ $$/ $$/   $$/  $$$$$$$/  $$$$$$/   $$$$$$$/  $$$$$$$/ 
		                    $$ |                                                                                
		                    $$ |                                                                                
		                    $$/                                                                                 
	 */

	include "PDOController.php";
	include "MySQLPDOController.php";
	include "PostgrePDOController.php";
	include "MSSQLPDOController.php";
	include "PDOControllerNotBuildedException.php";

	class PDOServiceProvider{
		/**
		 * [$_iregister internal controller register]
		 * @var array
		 */
		public static $_iregister = array();

		/**
		 * [$_instances internal instance matrix counter]
		 * @var array
		 */
		public static $_instances = array(
			"mysql" => 0,
			"mssql" => 0,
			"postgre" => 0
		);


		/**
		 * [$pdocontroller current pdo controller]
		 * @var [PDOController]
		 */
		private $pdocontroller;

		/**
		 * [$config controller current configuration]
		 * @var [array]
		 */
		private $config;

		/**
		 * [$prefix current description of pdo controller]
		 * @var [array]
		 */
		private $prefix;

		/**
		 * [$_pdocontroller current name of pdo controller]
		 * @var [string]
		 */
		public static $_pdocontroller;
		
		/**
		 * [__construct ]
		 * @param [string] $pdocontroller [pdo controller name]
		 * @param [array] $config        [pdo controller configuration]
		 */
		public function __construct($pdocontroller, $config){
			$this->pdocontroller = $pdocontroller;
			$this->config = $config;
			self::$_pdocontroller = $this->pdocontroller;
		}

		/**
		 * [register build an register one pdo controller]
		 * @param  [string] $w [pdo controller name]
		 * @param  [array] $x [pdo controller configuration]
		 * @return [PDOController]    [the current controller registered]
		 */
		public static function register($w, $x){
			$provider = new PDOServiceProvider($w, $x);
			$controller = $provider->build();

			if($controller)
				return $controller;
			else
				echo "La instancia del controlador pdo de ".self::$_pdocontroller." no fue construida exitosamente <br>";
		}

		/**
		 * [factoryControllers get the pdo controllers factory]
		 * @return [array] [array of pdo controllers by instance, for example: mysql0, mysql1, etc]
		 */
		public function factoryControllers(){
			$tmparray = PDOServiceProvider::_iregister;
			return $tmparray; 
		}

		/**
		 * [getInstance get the pdo controller instance by name]
		 * @param  [string] $z [instance name]
		 * @return [PDOController]    [instance of PDOController]
		 */
		public function getInstance($z){
			return (in_array($z, self::$_instances))?self::$_instances[$z]:false;
		}

		/**
		 * [build make the current pdo controller instance with the global counter]
		 * @return [PDOController] [current pdo controller object]
		 */
		public function build(){
			try{
				$this->prefix = $this->pdocontroller.self::$_instances[$this->pdocontroller];
				$ccontroller;

				if($this->pdocontroller === "mysql")
					$ccontroller = new MySQLPDOController($this->config);
				else if($this->pdocontroller === "mssql")
					$ccontroller = new MSSQLPDOController($this->config);
				else if($this->pdocontroller === "postgre")
					$ccontroller = new PostgrePDOController($this->config);
				else
					throw new PDOControllerNotBuildedException();

				self::$_iregister[] = array($this->prefix => $ccontroller);
				self::$_instances[$this->pdocontroller] = ++self::$_instances[$this->pdocontroller];
				
				return $ccontroller;
			}
			catch(PDOControllerNotBuildedException $ex){
				echo $ex->getMessage()." <br>";
				return false;
			}		
		}
	}
?>