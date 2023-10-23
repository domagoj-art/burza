<?php
class CreatePage extends AbstractPage{
    public $templateName = 'create';
    public function execute(){
        $this->choseFunction();
    }
    private function choseFunction(){
        $function = $_GET['function'];
        if($function == 'user'){
            $this->createUser();
        }
        elseif($function == 'stock'){
            $this->createStock();
        }
        else{
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Nt Found");
            $this->data = '';
        }
    }
    private function createUser(){
        $tableName = 'user';
        $name = $_GET['name'] ?? '';
        $lastname = $_GET['lastname'] ?? '';
        if($name == '' || $lastname == ''){
            $this->data = 'To create token you have to enter your name and lastname';
        }else{
            $createdToken = $this->randomNumber();
            $token = $this->checkIstokenFree($createdToken);
            $sql = "INSERT INTO users (name, lastname, token)
                    VALUES('$name','$lastname','$token')";
            $result = AppCore::getDB()->sendQuery($sql);
            if(!$result){
                $this->data = 'failed to create user';
                header($_SERVER["SERVER_PROTOCOL"] . " failed to create user"); // prođi sve protokole
            }else {
                $this->createLog($this->templateName, $tableName, $token);
                $this->data = 'Your token is: '.$token;
                
            }
        }

    }
    private function createStock(){
        $stock = $_GET['symbol'];
        $token = $_GET['token'];
        $tableName = 'stock';
        if($this->checkIfUserExists($token) == true){
            $sql = "INSERT INTO stocks (symbol, token)
                    VALUES('$stock', $token)";
            $result = AppCore::getDB()->sendQuery($sql);
            if(!$result){
                throw new DatabaseException("Error Processing Request", 1);
                
            }
            else {
                $this->createLog($this->templateName, $tableName, $token);
                $this->data = 'You are created stock successfuly';
            }
        }
        else{
            $this->data = '';
            
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
        }
        

    }
    private function randomNumber(){
        return rand(100, 100000);
    }
    private function checkIstokenFree($createdToken){
        $sql = "SELECT * FROM users WHERE token = $createdToken";
        $result = AppCore::getDB()->sendQuery($sql);
        $rowCnt = AppCore::getDB()->numRows($result);

        if ($rowCnt == 0) {
            return $createdToken;
        }
        else{
            $createdToken = $this->randomNumber();
            checkIstokenFree($createdToken);
        }
    }
    
}