#pdo-controllers
PDO --PHP Document Object, se encarga de la generalización de las conexiones a las bases de datos,
mediante un conjunto de controladores nativos escritos en PHP y otros lenguajes como c++,
por lo tanto se puede usar para hacer múltiples conexiones a bases de datos de diferentes gestores, 
como: mysql, postgre, microsoft sql server, oracle database, etc.


##Modelo
El diagrama de clases es el siguiente
![alt text][model]

Tal como se puede apreciar en la imagen anterior las clases `MySQLPDOController`, `MSSQLPDOController` y `PostgreSQLController` se extienden de PDOController y se aplica el patrón de inversión de control en cada una de las clases, porque pueden ser ampliadas --añadiendo más métodos y atributos a las mismas.

Estas clases son almacenadas usando el patrón register en PDOServiceProvider que almacena todas las instancias en la variable estática `_iregister` y usa un contador de instancoias `_instances` para construir el profijo de cada una de las instancias, por ejemplo mysql0, mysql1, mysql2.

##Despliegue
Para hacer despliegue de esta librería siga los siguientes pasos:

1. Incluir el archivo PDOServiceProvider.php en su aplicación.
2. Llamar al método estático register y pasarle el nombre del controlador a usar, en este caso mysql y el arreglo de configuraciones --para mas información consulte la documentación de PDO.
3. Validar si retorna un objeto del tipo `MySQLPDOController`.
4. Hacer las consultas e inserciones con el controlador actual.

```php
    include__dir__."/../src/PDOServiceProvider.php";

    class MyApplication{
        public function run(){
            //para usarlo con mysql
            $mysqlpdocontroller = PDOServiceProvider::register(
                "mysql",
                array(
                    "user" => "root",
                    "password" => "easy",
                    "host" => "localhost",
                    "db" => "database name",   
                    "port" => "32014",
                    "dsnfragment" => "charset=utf8" //fragmento del DSN
                )
            );

            //si el controlador fue construido entonces lo usamos
            if($mysqlpdocontroller){
                $matrix = $mysqlpdocontroller->getMatrix("select * from users");
                $matrix = $mysqlpdocontroller->filterMatrix($matrix, array("id", "email", "password", "token", "active", "twitter", "facebook"));  
                echo json_encode($matrix);
            }
            else
                echo "<h1>El controlador no puede ser construido</h1>";
        }
    }

    $app = new Application();
    $app->run();
```

##Métodos y atributos de las clases

Archivo `src/PDOController.php`

Atributo | Descripción
--- | --- 
user | el usuario de la base de datos
host | la computadora en donde reside el servidor
port | el puerto del servicio
pass | la contraseña para el usuario de la base de datos
db | el nombre de la base de datos
pdoconfig | arreglo de configuración para el objeto PDO que se usará con el gestor seleccionado
dsnfragment | fragmento para el DSN

Método | Descripción | Retorno
--- | --- | ---
__construct($dsnprefix: String, $config: Array) --constructor | recibe el prefijo dsn que es el nombre del SGBD a usar y un arreglo de configuraciones, posteriormente desempaca sus valores en las variables de instancias de la clase | NonReturn
getDSN | crea y obtiene el DSN actual | String
build | crea el objeto PDO con las configuraciones pre establecidas, para crear la conexión actual | PDO
destroy | destruye la conexión actual | void
getPDOObject | obtiene el objeto pdo actual | PDO 
getMatrix($query: String) | recibe un parámetro query del tipo String y retorna un arreglo con los datos consultados | Array --NativeArray
filterMatrix($matrix: Array, $filter: Array) | filtra una matríz en base a los nombres de sus campos especificados en el parámetro filter | Array
exec($query: String) | hace una consulta y retorna un valor numérico, que representa el número de filas afectadas | Integer

Archivo `src/PDOServiceProvider.php`

Atributo | Descripción
--- | ---
_iregister | variable estática contiene todas las instancias de los controladores
_instances | variable estática que contiene los contadores de las instancias de los controladores
pdocontroller | nombre del controlador pdo a construir
config | arreglo de propiedades y configuraciones para el controlador
prefix | prefijo de la instancia por ejemplo: mysql0, mysql1 


Método | Descripción | Retorno
--- | --- | ---
__construct($pdocontroller: String, $config: Array) | se encarga de inicializar las variables | NonReturn
register($w: String) | método estático que construye un PDOServiceProvider y retorna la construcción de un controlador | PDOController 
factoryControllers | método que factoriza los controladores y los retorna en un arreglo | Array
getInstance($z: String) | busca por llave y retorna una copia de una instancia definida como mysql0 | PDOController
build | construye el controlador actual y le envía las configuraciones | PDOController  

##Prueba con PostgreSQL
> Las pruebas se realizan con la misma tabla en todos los controladores, la finalidad es obtener los mismos datos, para comprobar que esta librería funciona correctamente.

Archivo `unit/db-postgre.sql`

```sql
    create database test_pdo_postgre;
    drop table if exists users;
    create table users (
        id serial not null primary key,
        name varchar(45) not null,
        password char(32) not null,
        active boolean not null default '0'
    );    
``` 

Archivo `unit/PostgreTest.php`;


```php
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
```

Comandos para hacer la prueba desde la terminal, lo mismo en con las demás.

```bash
    user@host:~$ postgresql test_pdo_postgre < unit/db-postgre.sql
    user@host:~$ sudo apt-get install php5-cli php5-pgsql php5-mysql
    user@host:~$ php unit/PostgreTest.php
```

Resultados
![alt text][testpostgre]

##Prueba con MySQL

Archivo `unit/db-mysql.sql`

```sql
    drop database if exists test_pdo_mysql;
    create database if not exists test_pdo_mysql;
    use test_pdo_mysql;

    create table users(
        id int not null primary key auto_increment,
        email varchar(45) not null,
        password char(32) not null,
        active boolean not null default 0
    );    
``` 

Archivo `unit/MySQLTest.php`;

```php
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
```

Resultados
![alt text][testmysql]

##Prueba con PostgreSQL y MySQL

Archivo `unit/MultipleTest.php`

```php
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
```

Resultados
![alt text][testmultiple]
##Referencias
- [The PHP manual PDO section](http://php.net/manual/en/book.pdo.php)
- [What is DSN?](https://es.wikipedia.org/wiki/Data_Source_Name)

[model]: https://raw.githubusercontent.com/captaincode0/pdo-controllers/master/model.png
[testpostgre]: https://raw.githubusercontent.com/captaincode0/pdo-controllers/master/testpostgre.png
[testmysql]: https://raw.githubusercontent.com/captaincode0/pdo-controllers/master/tesmysql.png
[testmultiple]: https://raw.githubusercontent.com/captaincode0/pdo-controllers/master/testmultiple.png
