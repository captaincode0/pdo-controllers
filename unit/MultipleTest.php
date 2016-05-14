<?php 
	/*
		@author: captaincode0
	*/
	include __dir__."/../src/PDOServiceProvider.php";

	class MultipleTest{
		public static function test(){
			echo "Testing multiple connections\n";

			$pgsqlpdocontroller = PDOServiceProvider::register(
				"postgre",
				array(
					"user" => "postgres",
					"host" => "127.0.0.1",
					"pass" => "1234",
					"db" => "test_pdo_postgre",
					"port" => "5432"
				)
			);

			$mysqlpdocontroller = PDOServiceProvider::register(
				"mysql", 
				array(
					"user" => "root",
					"host" => "localhost",
					"pass" => "data.set",
					"db" => "test_pdo_mysql",
					"port" => "3306",
					"dsnfragment" => "charset=utf8"	
				)	
			);

			echo "Getting the stdout from the next query in mysql [select * from users]\n";
			print_r($mysqlpdocontroller->getMatrix("select * from users"));
			
			echo "Getting the stdout from the next query in postgre [select * from users]\n";
			print_r($pgsqlpdocontroller->getMatrix("select * from users"));

			echo "\n";
		}
	}

	MultipleTest::test();
?>
