<?php 
class AveragePage extends AbstractPage{
    public $templateName = 'average';
    public function execute(){
        $year = $_GET['date'] ?? '';
        if($year != ''){

            $this->getAverage($year);
        }else {
            $this->data = 'you must enter valid date';
        }
    }
    private function getAverage($year){
        $sql = "SELECT * FROM monthly WHERE year(date) = $year";
        $result = AppCore::getDB()->sendQuery($sql);
        $average = 0;
        $rowCnt = AppCore::getDB()->numRows($result);
        while($row = AppCore::getDB()->fetchArray($result)){
            $average += intval($row['closed']);
            
        }
        $average = $average/$rowCnt;
        return $this->data = $average;
    }
}
?>