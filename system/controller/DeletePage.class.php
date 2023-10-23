<?php
class DeletePage extends AbstractPage{
    public $templateName = 'delete';
    public function execute(){
        try {
            $this->deleteStock();
            
        } catch (Excepiton $e) {
            
        }
    }
    private function deleteStock(){
        $id = $_GET['stockId'] ?? null;
        if($id == ''){
            $this->data = 'id '.$id.' is not in database ';
        }else{
            $id = intval($id);
            $token = $_GET['token'] ?? null;
            $tableName = 'stock';

            if($this->checkIfUserExists($token) == true){
                $sql = "DELETE FROM stocks WHERE id = '$id'";
                $result = AppCore::getDB()->sendQuery($sql);
                if(!$result){
                    throw new DatabaseException("Error Processing Request", 1);
                    $this->data = 'fail with db';
                }
                else
                {
                    $this->createLog($this->templateName, $tableName, $token);
                    $this->data = 'stock is deleted';
                }
            }
            else{
                header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
                $this->data = 'User with token: '.$token.' don\'t exist. Create user or use correct token';
            }
        }
    }
}