<?php
class UpdatePage extends AbstractPage{
    public $templateName = 'update';
    public function execute(){
        $token = $_GET['token'] ?? '';
        if($this->checkIfUserExists($token)==true){
            $this->updateStock();
        }else{
            $this->data = 'wrong token: '.$token;
        }

    }
    
    private function updateStock(){
        $token = $_GET['token'];
        $id = $_GET['id'] ?? '';
        $id = intval($id);
        $symbol = $_GET['symbol'] ?? '';
        $tableName = 'stocks';
        if($this->checkIfIdExists($tableName) == true){
            if($symbol == ''){
                $this->data = 'symbol can\'t be empty';
            }else {
                $sql = "UPDATE stocks SET symbol = '$symbol' WHERE id = $id";
                $result = AppCore::getDB()->sendQuery($sql);
                $this->createLog($this->templateName, $tableName, $token);
                $this->data = 'stock is updated';
            }
            
        }else{
            $this->data = 'wrong stock id: '.$id.'';
        }
    }
    private function checkIfIdExists($tableName){
        $id = $_GET['id'] ?? '';
        $id = intval($id);
        $sql = "SELECT * FROM $tableName WHERE id = $id";
        $result = AppCore::getDB()->sendQuery($sql);
        $rowCnt = AppCore::getDB()->numRows($result);
        if($rowCnt == 0){
            $this->data = 'wrong id';
            return false;
        }else{
            return true;
        }
    }
    
}

?>