<?php
	class PDOController{
		private $user;
		private $host;
		private $pass;
		private $db;
		private $pdoconfig;
		private $dsnprefix;
		private $pdoobject;
		private $port;

		/*
			@{constructor}
			__construct($dsnprefix, $config): recide un prefijo dsn como sqlsrv, pgsql, mysql para realizar la conexión.
		*/

		public function __construct($dsnprefix, $config){
			//desempacar las configuraciones
			$this->user = $config["user"];
			$this->host = $config["host"];
			$this->pass = $config["pass"];
			$this->db = $config["db"];
			$this->pdoconfig = isset($config["pdoconfig"])?$config["pdoconfig"]:array();
			$this->dsnprefix = $dsnprefix;
			$this->pdoobject = null;
			$this->port = $config["port"];

			
			/*
				Añadir estos métodos
				build(); //verifica si se puede construir el objeto PDO actual.
				destroy(); //Destruye el objeto PDO actual.
				getPDOObject(); //retorna el objeto PDO para ser usado en transacciones, procedimientos almacenados y declaraciones.
				getMatrix($query); //obtiene una matriz --un conjunto de arreglos en php, para ser pasados a la vista.
				filterMatrix($matrix, $fields); //filtra una matriz en caso de ser necesario por nombres de las columnas.
				exec($query); //ejecuta una consulta y devuelve un resultado numérico, que son las filas que fueron afectadas.
				getDSN(); //obtiene el DSN actual.
			*/
		}

		public function getDSN(){
			if($this->dsnprefix === "mysql" 
				|| $this->dsnprefix === "pgsql")
				return "{$this->dsnprefix}:host={$this->host};dbname={$this->db};port={$this->port};charset=utf8";
			else if($dsnprefix === "sqlsrv")
				return "{$this->dsnprefix}:Server={$this->host};Database={$this->db};Port={$this->port};charset=utf8";
		}

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
				echo $ex->getMessage();
				return false;
			}
			return true; 
		}

		public function destroy(){
			if($this->pdoobject != null)
				$this->pdoobject = null;
		}

		public function getPDOObject(){
			return ($this->pdoobject != null)?$this->pdoobject:false;
		}

		public function getMatrix($query){
			try{
				if(!is_string($query))
					throw new Exception("PDOControllerException, [method: getMatrix]: the parameter query is not a string", 1);
				if(!$this->build()) //crea la conexión actual
					throw new PDOException("PDOControllerException, [method: getMatrix]: pdoobject can't be build", 1);

				$pdost = $this->pdoobject->query($query); //Se obtiene el resultado dela consulta 
				
				if(!$pdost)
					throw new PDOException("PDOControllerException, [method: getMatrix]: the query can't be executed", 1);
				else{
					$matrix = array();

					foreach($pdost as $pdorow)
						$matrix[] = $pdorow;

					$this->destroy(); //cierra la conexión actual
					return $matrix;
				}
			}
			catch(Exception $ex){
				echo $ex->getMessage();
			}
			catch(PDOException $ex){
				echo $ex->getMessage();
			}
		}

		public function filterMatrix($matrix, $fields){
			try{
				if(is_array($matrix) && is_array($fields)){
					$reducedmatrix = array();

					foreach($matrix as $array){
						$tmparray = array();

						for($w=0; $w<count($filter); $w++)
								$tmparray[$filter[$w]] = $array[$filter[$w]];

						$reducedmatrix[] = $tmparray;
					}

					return $reducedmatrix;
				}

				throw new Exception("PDOControllerException, [method: exec]: the parameters are not array", 1);
				
			}
			catch(Exception $ex){
				echo $ex->getMessage();
			}
			catch(PDOException $ex){
				echo $ex->getMessage();
			}
		}

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
				echo $ex->getMessage();
			}
			catch(PDOException $ex){
				echo $ex->getMessage();
			}
		}
	}
?>