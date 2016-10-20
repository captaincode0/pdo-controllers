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
	class PDOController{
		/**
		 * [$user database user name]
		 * @var [string]
		 */
		private $user;

		/**
		 * [$host database host name or ip]
		 * @var [string]
		 */
		private $host;

		/**
		 * [$port database port number]
		 * @var [string]
		 */
		private $port;

		/**
		 * [$pass database user password]
		 * @var [string]
		 */
		private $pass;

		/**
		 * [$db database or schema name]
		 * @var [string]
		 */
		private $db;

		/**
		 * [$pdoconfig internal array for build the pdo configuration]
		 * @var [array]
		 */
		private $pdoconfig;

		/**
		 * [$dsnprefix name of dsn - name of the driver]
		 * @var [string]
		 */
		private $dsnprefix;

		/**
		 * [$pdoobject instance of PDO class]
		 * @var [PDO]
		 */
		private $pdoobject;

		/**
		 * [$dsnfragment remainder or rest of DSN string connection]
		 * @var [type]
		 */
		private $dsnfragment;

		/**
		 * [$_noerrors static flag to hide the errors]
		 * @var boolean
		 */
		private static $_noerrors = false;

		/**
		 * [__construct description]
		 * @param [string] $dsnprefix [name of the driver]
		 * @param [array] $config    [controller configuration]
		 */
		public function __construct($dsnprefix, $config){
			$this->user = $config["user"];
			$this->host = $config["host"];
			$this->pass = $config["pass"];
			$this->db = $config["db"];
			$this->pdoconfig = isset($config["pdoconfig"])?$config["pdoconfig"]:array();
			$this->dsnprefix = $dsnprefix;
			$this->pdoobject = null;
			$this->port = $config["port"];
			$this->dsnfragment = isset($config["dsnfragment"])?$config["dsnfragment"]:"";
		}

		/**
		 * [getDSN build the DSN string and return it accord to DSN prefix]
		 * @return [string] [DSN string]
		 */
		public function getDSN(){
			if($this->dsnprefix === "mysql" 
				|| $this->dsnprefix === "pgsql")
				return "{$this->dsnprefix}:host={$this->host};dbname={$this->db};port={$this->port};{$this->dsnfragment}";
			else if($this->dsnprefix === "sqlsrv")
				return "{$this->dsnprefix}:Server={$this->host};Database={$this->db};Port={$this->port};{$this->dsnfragment}";
		}

		/**
		 * [build make the current PDO instance]
		 * @return [PDO] [one fresh PDO instance]
		 */
		public function build(){
			try{
				if(count($this->pdoconfig) > 0)	
					$this->pdoobject = new PDO(
						$this->getDSN(),
						$this->user,
						$this->pass,
						$this->pdoconfig
					);
				
				else
					$this->pdoobject = new PDO(
						$this->getDSN(),
						$this->user,
						$this->pass
					);
			}
			catch(PDOException $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
				return false;
			}
			return true; 
		}

		/**
		 * [destroy assign the current PDO object or instance to null]
		 * @return [void] []
		 */
		public function destroy(){
			if($this->pdoobject != null)
				$this->pdoobject = null;
		}

		/**
		 * [getPDOObject get the current PDO object]
		 * @return [PDO] [current PDO object]
		 */
		public function getPDOObject(){
			return ($this->pdoobject != null)?$this->pdoobject:false;
		}

		/**
		 * [getMatrix description]
		 * @param  [type] $query [description]
		 * @return [type]        [description]
		 */
		public function getMatrix($query){
			try{
				if(!is_string($query))
					throw new Exception("PDOControllerException, [method: getMatrix]: the parameter query is not a string", 1);
				if(!$this->build())
					throw new PDOException("PDOControllerException, [method: getMatrix]: pdoobject can't be build", 1);

				$pdost = $this->pdoobject->query($query);
				
				if(!$pdost)
					throw new PDOException("PDOControllerException, [method: getMatrix]: the query can't be executed", 1);
				else{
					$tmpmatrix = $pdost->fetch(\PDO::FETCH_ASSOC);

					if(count($tmpmatrix) == 0){
						$this->destroy();
						return false;
					}

					$matrix = array();

					foreach($tmpmatrix as $pdorow)
						$matrix[] = $pdorow;

					$this->destroy(); 
					return $matrix;
				}
			}
			catch(Exception $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
			}
			catch(PDOException $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
			}
		}

		/**
		 * IDEAL FOR PREPARED STATEMENTS
		 * [getMatrixFromStatement get one matrix from ]
		 * @param  [type] $statement [description]
		 * @return [type]            [description]
		 */
		public function getMatrixFromStatement($statement){
			try{
				if($statement)
					throw new Exception("PDOControllerException, [method: getMatrixFromStatement]: The statement need to be a PDOStatement type", 1);

				$tmpmatrix = $statement->fetchAll(\PDO::FETCH_ASSOC);


				if(count($tmpmatrix) === 0)
					return false;


				$matrix = array();

				foreach($tmpmatrix as $row)
					$matrix[] = $row;

				return $matrix;
			}
			catch(Exception $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
			}
		}

		/**
		 * [filterMatrix add a filter for one specific result set]
		 * @param  [array] $matrix [the matrix to filter]
		 * @param  [array] $fields [the filer to apply it contains the name of columns to adquire, the rest is not added]
		 * @return [array]         [the matrix filtered of one empty array if the count of one parameter is zero]
		 */
		public function filterMatrix($matrix, $fields){
			try{
				if(is_array($matrix) && is_array($fields)){
					if(count($matrix) === 0 || count($fields) === 0)
						return array();

					$reducedmatrix = array();

					foreach($matrix as $array){
						$tmparray = array();

						for($w=0; $w<count($fields); $w++)
								$tmparray[$fields[$w]] = $array[$fields[$w]];

						$reducedmatrix[] = $tmparray;
					}

					return $reducedmatrix;
				}

				throw new Exception("PDOControllerException, [method: exec]: the parameters are not array", 1);
				
			}
			catch(Exception $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
			}
			catch(PDOException $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
			}
		}

		/**
		 * [exec execute one command and return the number of rows affected]
		 * @param  [string] $query [the sql query]
		 * @return [int]        [the number of rows affected]
		 */
		public function exec($query){
			try{
				if(!is_string($query))
					throw new Exception("PDOControllerException, [method: exec]: the parameter query is not a string", 1);
				if(!$this->build()) //crea la conexión actual
					throw new PDOException("PDOControllerException, [method: exec]: pdoobject can't be builded", 1);

				$pdost = $this->pdoobject->exec($query);
				$this->destroy(); //destruye la conexión actual

				return $pdost;
			}
			catch(Exception $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
			}
			catch(PDOException $ex){
				echo (self::$_noerrors == true)?$ex->getMessage():"";
			}
		}
	}
?>