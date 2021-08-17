<?php 

class CopyWeek {
    private $data;
    private $first_week_day;
    public function __construct()
    {
        $this->data =  json_decode(file_get_contents('events.json',true));
    } 
    public function copyEvent(){

    }
    public function checkEvent(){

    }
    public function save(){
        
    }
}