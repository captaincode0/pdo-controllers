<?php 
	/*
		@author: captaincode0
	*/
	//test writen for php console
	include __dir__."/../src/PDOServiceProvider.php";

	class MySQLTest{
		public static function test(){
			echo "Testing MySQLPDOController module\n";
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

			$email = "developerdiego0@gmail.com";
			$pass = md5("diego");

			echo "Inserting to test_pdo_mysql.users the next values email=$email and password=$pass \n";
			$mysqlpdocontroller->exec("insert into users(email, password) values('$email','$pass')");
			
			echo "Getting the stdout from the next query [select * from users]\n";
			print_r($mysqlpdocontroller->getMatrix("select * from users"));
			
			echo "\n";
		}
	}

	MySQLTest::test();
?>
