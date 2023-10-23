<?php
class MySQLiDatabase{
    protected $host;
    protected $user;
    protected $password;
    protected $database;
    public $MySQLi;

    public function __construct($host, $user, $password, $database){
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
    }
     function connect(){
        $this->MySQLi = new MySQLi($this->host, $this->user, $this->password, $this->database);
        if (mysqli_connect_errno()) {
            throw new Exception("Error Processing Request");
            
        }
    }
    
     function sendQuery($query){
       
        return $this->MySQLi->query($query);
       
    }
    function fetchArray($result){
        return $result->fetch_array();
    }
    function numRows($result){
        return $result->num_rows;
    }
    
}

?>