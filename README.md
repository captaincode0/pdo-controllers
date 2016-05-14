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
```php
    include include __dir__."/../src/PDOServiceProvider.php";

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

[model]: https://raw.githubusercontent.com/captaincode0/pdo-controllers/master/model.png
##Referencias
[The PHP manual PDO section](http://php.net/manual/en/book.pdo.php)
[What is DSN?](https://es.wikipedia.org/wiki/Data_Source_Name)

