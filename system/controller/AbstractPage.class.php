<?php
abstract class AbstractPage{
    public $allStocks = array();
    public function __construct(){
        $this->execute();
        $this->show();
    }
    function show(){//ovo nas vodi do zeljenog template svaki controler ima svoje template
        
        $templateName = $this->templateName;
        $data = $this->data;
        
        include_once('system/view/'. $templateName.'.tpl.php');
    }
    function checkIfUserExists($token){
        $sql = "SELECT * FROM users WHERE token = '$token'";
        $result = AppCore::getDB()->sendQuery($sql);
        $rowCnt = AppCore::getDB()->numRows($result);
        if($rowCnt == 0){
            return false;
        }else{
            return true;
        }
    }
    function checkIfStockExists(){
        
    }
    function curentTime(){
        $timestamp = time();
        $time = date("Y-m-d H:i:s", $timestamp);
        return $time;
    }
    function createLog($templateName, $tableName, $token){
        $date = $this->curentTime();
        $sql = "INSERT INTO logs(action, tableName, date, token)
                VALUES('$templateName', '$tableName', '$date', $token)";
        $result = AppCore::getDB()->sendQuery($sql);
        if(!$result){
            throw new DatabaseException("Error Processing Request", 1);
        }
    }
}