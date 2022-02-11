<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
require_once("config.php");

class Db{
    //The database connection
    protected $connection;

    function __construct(){        
        $this->connect();
    }

    /**
     * Conexion a la base de datos
     * 
     * @return bool on success or false on failure
     */
    public function connect(){
        //Intento de conexion a la base de datos
        if(!isset($this->connection))
        {
            $this->connection = new mysqli('localhost', 'root', 'mysql', 'dbmycontrol');
        }

        // Si la conexion no fue exitosa, cachamos el error
        if($this->connection === false)
        {
            //en caso de error retornamos false
            return false;
        }
        mysqli_set_charset($this->connection,'utf8');
        return $this->connection;
    }

    public function getConnection(){
        return $this->connection;
    }

    public function close_conn(){
         mysqli_close($this->connection);
    }

/*
    function __destruct()
    {
        if(isset($this->connection))
         if(mysql_close($this->connection))
          $this->connection = null; 
    }
*/
}
$db = new Db();
?>    