<?php 
	/*
		@author: captaincode0
	*/
	include __dir__."/../src/PDOServiceProvider.php";

	class PostgreTest {
		public static function test(){
			echo "Testing PostgreSQLPDOController\n";

			$pgsqlcontroller = PDOServiceProvider::register(
				"postgre",
				array(
					"user" => "postgres",
					"host" => "127.0.0.1",
					"pass" => "1234",
					"db" => "test_pdo_postgre",
					"port" => "5432"
				)
			);

			$email = "developerdiego0@gmail.com";
			$pass = md5("diego");

			echo "Inserting to test_pdo_mysql.users the next values email=$email and password=$pass \n";
			$pgsqlcontroller->exec("insert into users(email, password) values('$email','$pass')");
			
			echo "Getting the stdout from the next query [select * from users]\n";
			print_r($pgsqlcontroller->getMatrix("select * from users"));
			
			echo "\n";
		}
	}

	PostgreTest::test();
?>
