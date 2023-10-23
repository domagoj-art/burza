<?php
class IndexPage extends AbstractPage{
    public $templateName = 'Index';
    public function execute(){
        
        $sql="SELECT * FROM resources";
        $result=AppCore::getDB()->sendQuery($sql);
        $resources=[];

        while($row=AppCore::getDB()->fetchArray($result)){
            $resources[$row['id']]=$row;
        }
        //assing variable to template
        $this->data=[
            'resources'=>$resources
        ];

        
       
    }
}
?>