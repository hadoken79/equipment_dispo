<?php
require_once('conf\config.php');


class DBHandler{
   
   public $db;

   public function __construct(){
        $this->db = new mysqli(HOST, USER, PWD, DB, 3306);
       if($this->db->connect_errno){
        echo "Fehler bei der Verbindung zur Datenbank".$db->connect_error;
        }
   }

    public function getContent($_query){
        
        $result = $this->db->query($_query);
        $content = $result->fetch_all(MYSQLI_ASSOC);
       
        //memory frei geben
        $result->free();
        //verbindung trennen
        $this->db->close();
        return $content;
    }

}


?>