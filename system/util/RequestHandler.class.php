<?php
include('system/exception/SystemException.class.php');
require_once('system/controller/AbstractPage.class.php');
class RequestHandler{
public function __construct($className){
   $className = $className . 'Page';

   if(is_file('system/controller/'.$className.'.class.php')){
      require_once('system/controller/'.$className.'.class.php');
      new $className();
   }else{
      $className = substr($className, 0, -4);
      throw new Exception("Error ".$className.' Page not exist. Pages: Read, Create, Update, Delete', 1);
      
   }
}
public static function handle(){
   $request = $_GET['page'] ?? 'Index';//dohvaca stranicu
   new RequestHandler($request);//instancira klasu
}

}

?>