<?php
class ReadPage extends AbstractPage{
    public $templateName = 'read';
    
    
    public function execute(){
        
            
        
            if($this->isStockSuported() == true){
                $this->chooseFunction();
            }else{
                $symbol = $_GET['symbol'];
                $this->data = 'stock '.$symbol.' not suprted';
            }
        
       
    }
   ///////////////////////////////////////////////////////////general///////////////////////////////////////////////////
    private function getStockID(){
        $symbol = $_GET['symbol'] ?? '';
        $sql="SELECT * FROM stocks WHERE symbol = '$symbol'";
        $result=AppCore::getDB()->sendQuery($sql);
        $row = AppCore::getDB()->fetchArray($result);
        $id = $row['id'];
        $id = intval($id);
        return $id;
    }
    private function getStockSymbol(){
        $id = $this->getStockID();
        $sql="SELECT * FROM stocks WHERE id = $id";
        $result=AppCore::getDB()->sendQuery($sql);
        $row = AppCore::getDB()->fetchArray($result);
        return $row['symbol'];
    }
    private function chooseFunction(){
        $function = $_GET['function'] ?? '';
        if($function == 'latest'){
            $tableName = $_GET['interval'] ?? '';
            if($tableName == 'daily'){
                $interval = 'Time Series (60min)';
                $this->displayLatestData($interval, $tableName);
            }elseif($tableName == 'weekly'){
                $interval = 'Weekly Time Series';
                $this->displayLatestData($interval, $tableName);
            }elseif($tableName == 'monthly'){
                $interval = 'Monthly Time Series';
                $this->displayLatestData($interval, $tableName);
            }elseif($tableName == 'yearly'){
                $interval = 'Monthly Time Series';
                $tableName = 'monthly';
                $this->displayLatestData($interval, $tableName);
            }else{
                $this->data = 'Wrong interval='.$tableName.'. Suported intervals are: daily, weekly, monthly, yearly';
            }
        }
        elseif($function == 'all'){
            $tableName = $_GET['interval'] ?? '';
            if($tableName == 'daily'){
                $interval = 'Time Series (60min)';
                $this->displayAllData($interval, $tableName);
            }elseif($tableName == 'weekly'){
                $interval = 'Weekly Time Series';
                $this->displayAllData($interval, $tableName);
            }elseif($tableName == 'monthly'){
                $interval = 'Monthly Time Series';
                $this->displayAllData($interval, $tableName);
            }elseif($tableName == 'yearly'){
                $interval = 'Monthly Time Series';
                $tableName = 'monthly';
                $this->displayAllData($interval, $tableName);
            }else{
                $this->data = 'Wrong interval='.$tableName.'. Suported intervals are: daily, weekly, monthly, yearly';
            }

        }elseif($function == 'search'){
            $data = $_GET['byDate'] ?? '';
            $tableName = $_GET['interval'] ?? '';
            if($this->validateDate($data, 'Y-m-d') == false){
                $this->data = 'wrong date format';
                return;
            }
            if($tableName == 'daily'){
                $interval = 'Time Series (60min)';
                $this->search($interval, $tableName, $data);
            }elseif($tableName == 'weekly'){
                $interval = 'Weekly Time Series';
                $this->search($interval, $tableName, $data);
            }elseif($tableName == 'monthly'){
                $interval = 'Monthly Time Series';
                $this->search($interval, $tableName, $data);
            }elseif($tableName == 'yearly'){
                $interval = 'Monthly Time Series';
                $tableName = 'monthly';
                $this->search($interval, $tableName, $data);
            }else{
                $this->data = 'Wrong interval='.$tableName.'. Suported intervals are: daily, weekly, monthly, yearly';
            }
        }else{
            $this->data ='Wrong function='.$function.'. Spuorted functions are: latest, all, search';
        }
        
    }
    private function specifyUrl($symbol){
        $interval = $_GET['interval'] ?? '';
        if($interval == 'daily'){
            
            return 'https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol='.$symbol.'&interval=60min&apikey=9SPY9Z07HF5B3VWY';
        }
        elseif($interval == 'weekly'){
            $url = 'https://www.alphavantage.co/query?function=TIME_SERIES_WEEKLY&symbol='.$symbol.'&apikey=9SPY9Z07HF5B3VWY';
            
            return $url;
        }
        elseif($interval == 'monthly' || $interval == 'yearly'){
            $url = 'https://www.alphavantage.co/query?function=TIME_SERIES_MONTHLY&symbol='.$symbol.'&apikey=9SPY9Z07HF5B3VWY';
    
            return $url;
         }
    }
    private function returnJsonFormat($result){
        $array = array();
            while($row =mysqli_fetch_assoc($result)){ // popravi metodu -- u bazi mysqli_fetch..
                $array[] = $row;
            }
            $json = json_encode($array);
            
            return $json;
    }
    private function addStockData($tableName ,$ids, $open, $high, $low, $close, $volume, $date){
        $sql = "INSERT INTO $tableName (idStock, open, high, low, closed, volume, date)
                VALUES('$ids', '$open', '$high', '$low', '$close', '$volume', '$date')";
                
        $result = AppCore::getDB()->sendQuery($sql);
        if(!$result){
            throw new Exception("neuspijeli pokusajj");
            
        }
    }
    //promjeni ime dodaj api//++++++++
    private function getLatestStockData($interval){ //interval se odnosi na kljuc(datum ) za koji se vezu podaci dionica
        $symbol = $this->getStockSymbol();
        $json = file_get_contents($this->specifyUrl($symbol));
        $data = json_decode($json, true);
        $lastData ='';
        $first = true;
        foreach ($data as $key => $value) {
            if($key == $interval){
                foreach ($value as $key => $value) {
                    if($first){
                    $lastData .= $key;
                    $first = false;
                    }
                }
            }
        }
       echo $lastData;
        return $lastData;
    }

    //////////////////////////////////////////////////////daily//////////////////////////////////////////////////////

    private function displayLatestData($interval, $tableName){
        $urlInerval = $_GET['interval'];
        $id = $this->getStockID();
        
        $this->updateTable($interval, $tableName);
        if($urlInerval == 'yearly'){
            $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date LIKE '%-12-30'
                OR date LIKE '%-12-31' OR date LIKE '%-12-29' ORDER BY date DESC LIMIT 1";//sve ove prepravit dodat stockid
        }else{

            $sql = "SELECT * FROM $tableName WHERE idStock = $id ORDER BY date DESC LIMIT 1";
        }
        $result = AppCore::getDB()->sendQuery($sql);
        return $this->data = $this->returnJsonFormat($result);
        
        
    }

    //////////////////////////////////////////////ALL/////////////////////////////////////////////////////////
    private function getAllData($interval, $tableName){//mozda prominit ime 
        $latestData = $this->latestDataFromDatabase($tableName);
        $symbol = $this->getStockSymbol();
        $json = file_get_contents($this->specifyUrl($symbol));
        $data = json_decode($json, true);
        $array = array();
        foreach($data as $key => $value){
            if($key == $interval){
                if(is_array($value)){    
                    foreach ($value as $key1 => $value) {
                        if($key1 == $latestData){
                            break;
                        }
                        $array[] = $key1;
                        if(is_array($value)){
                            foreach ($value as $key => $value) {
                                $array[] = $value;   
                            }   
                        }
                    } 
                }
            }

        }
        
        return $array;
    }
    private function updateTable($interval, $tableName){//update() 
        $idStock = $this->getStockID();
        $ids =  intval($idStock);
        $array = $this->getAllData($interval,$tableName);// ovo
        $arrayLenght = count($array);
        $sql = "INSERT INTO $tableName (idStock, date, open, high, low, closed, volume) VALUES";
        $queryParts = array();
      
            
        for ($i=0; $i < $arrayLenght; $i+=6) { 
            
            $queryParts[] = "(".$ids.", '".$array[0+$i]."', '".$array[1+$i]."', '".$array[2+$i]."', '".$array[3+$i]."', '".$array[4+$i]. "', '" .$array[5+$i]. "')";
        
        }
         
        if($array == null){
            return;
        }else{
            $sql .= implode(',', $queryParts);
            $result = AppCore::getDB()->sendQuery($sql);
            if(!$result){
                throw new Exception("neuspijeli pokusajj");
                
            }
        }
        
    }
    private function latestDataFromDatabase($tableName){
        $id = $this->getStockID();
        $sql = "SELECT * FROM $tableName WHERE idStock = $id ORDER BY date DESC LIMIT 1";
        $result = AppCore::getDB()->sendQuery($sql);
        $array = AppCore::getDB()->fetchArray($result);
        return $array['date'] ?? '';
    }
   
    private function displayAllData($interval, $tableName){
        $this->updateTable($interval, $tableName);
        $id = $this->getStockID();
        $urlInerval = $_GET['interval'];
        if($urlInerval == 'yearly'){
            $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date LIKE '%-12-30'
                OR date LIKE '%-12-31' OR date LIKE '%-12-29'";
        }else{

            $sql = "SELECT * FROM $tableName WHERE idStock = $id";
        }
        $result = AppCore::getDB()->sendQuery($sql);
        $rowCnt = AppCore::getDB()->numRows($result);
        if($rowCnt == 0){
            $this->updateTable($interval, $tableName);
            $this->displayAllData($interval, $tableName);
        }else{
            return $this->data = $this->returnJsonFormat($result);
            
        }
    }
    

    ////////////////////////////////////Search///////////////////////////////////////////////////////////////


    private function search($interval, $tableName, $data){
        $this->updateTable($interval, $tableName);
        $id = $this->getStockID();
        $urlInerval = $_GET['interval'];
        if($urlInerval == 'daily'){
            $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date LIKE '$data%' ORDER BY date DESC LIMIT 1";
        }elseif($urlInerval == 'yearly'){
            $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date LIKE '$data-12-30'
                OR date LIKE '$data-12-31' OR date LIKE '$data-12-29'";
        }else{
            $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date = '$data'";
        }
        
        $result = AppCore::getDB()->sendQuery($sql);
        $rowCnt = AppCore::getDB()->numRows($result);
        
        if($rowCnt == 0){
            
            if($urlInerval == 'daily'){//odrizat dio data
                $data = substr($data,0,8);
                
                $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date LIKE '$data%' ORDER BY date DESC LIMIT 1";
            }elseif($urlInerval == 'yearly'){
                $data = substr($data,0,3);
                
                $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date LIKE '$data%-12-30'
                    OR idStock = $id AND date LIKE '$data%-12-31' OR idStock = $id AND date LIKE '$data%-12-29'";
            }else{
                $data = substr($data,0,8);
                
                $sql = "SELECT * FROM $tableName WHERE idStock = $id AND date LIKE '$data%'";
            }
            $result = AppCore::getDB()->sendQuery($sql);
            $rowCnt = AppCore::getDB()->numRows($result);
            if($rowCnt == 0){
                return $this->data = 'too old data';
            }else{

                return $this->data = $this->returnJsonFormat($result);
            }
        }else{
            return $this->data = $this->returnJsonFormat($result);
        }
    }
    
    
    private function isStockSuported(){
        $stock = $_GET['symbol'] ?? '';
        $sql = "SELECT * FROM stocks WHERE symbol = '$stock'";
        $result = AppCore::getDB()->sendQuery($sql);
        $rowCnt = AppCore::getDB()->numRows($result);
        if($rowCnt == 0){
            return false;
        }else{
            return true;
        }
    }
    private function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date ;
}
}
?>