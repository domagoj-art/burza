<?php
include 'options.inc.php';
spl_autoload_register(function($className){
    $pathToFile = PAGE_URL. 'system/util/' . $className . '.class.php';
    
		//include $pathToFile;
        try {
            include 'util/' . $className . '.class.php';
        } catch (Exception $e) {
            echo 'aaa';
        }
		//include 'util/' . $className . '.class.php';
	
}); 

class AppCore{
   protected static $dbObj;
   public function __construct(){
    $this->initDB();
        //router
        //require_once('util/RequestHandler.class.php');
        try {
            RequestHandler::handle();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
   }
   protected function initDB(){
    //$localhost=$username=$password=$database='';
        require_once('config.inc.php');
        //create DB connection
        require_once('system/model/MySQLiDatabase.class.php');
        self::$dbObj = new MySQLiDatabase($host,$user,$password,$database);
        
   }
   public static function getDB(){
    return self::$dbObj;
   }
   public function handleException(Exception $e){
    $e->show();
   }
   public function initOptions(){
    include 'options.inc.php';
   }
   

   function autoLoad($className) //nezz ovu metodu napravit 
{
	/*$pathToFile = PAGE_URL. 'util/' . $className . '.class.php';

	if (file_exists($pathToFile)) {
		require $pathToFile;
	}*/
    
}

//spl_autoload_register('autoLoad'); 
}
?>