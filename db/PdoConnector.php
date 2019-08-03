<?php
require_once('conf\config.php');

//singleton für datenbank
class PdoConnector
{
    private static $pdo = null;
    private static $dsn = 'mysql:host='.HOST.';dbname='.DB;

    private function __construct(){

    }
    
    public static function getConn(){
        if(!isset(self::$pdo)){
            self::$pdo = new PDO(self::$dsn, USER, PWD);
            //set default fetchmode to object
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            echo "created new instance of pdo<br>";
        }

        return self::$pdo;
    }
}

?>