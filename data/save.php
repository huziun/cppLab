<?php

//-----------------------------WITH PIWO-----------------------------//
//---PIWKO MAGIC---//
class SaveService {

    private $data;
    /**
     * @param int $id
     */
    private $id=0;

    private $event;
    /**
     * All event's have personal id
     * JSON structure [index] => "event object" -> event_id
     * 
     * Statments:
     *  - index == event id
     *  - when we create event without buffer we reservate empty slot (event_id + 1 )
     *  - new event_id = json->length + 2 
     */
    public function __construct()
    {
        $this->event = $_POST;
        $this->data = json_decode(file_get_contents('events.json',true)); // upload data from .json 
        
        ($this->event['EventId']=="NaN") ? $this->id = sizeof($this->data) : $this->id = $this->event['EventId'];
        var_dump($this->id);
        if($this->id < 0){
            $this->id = 0;
        }
    }

    public function CreateScenario()
    {
        $event = $this->createEvent();
        $event_buffer = $this->createBuffer($this->event['start_time'],$this->event['date'],$this->event['buffer_time'],$this->id); 
        
        
        return [
            'event'=>$event,
            'event_buffer'=>$event_buffer
        ];
    }
    
    public function createEvent()
    {
        $newData = [
            'id' => $this->id,
            'title'=>$this->event['title'],
            'start'=>$this->event['date']."T".$this->event['start_time'],
            'end' =>$this->event['date']."T".$this->event['finish_time'],
            "type"=>$this->chooseBox($this->event['choose_box']),
            "choose_box"=>$this->event['choose_box'],
            "temperature"=>$this->event['temperature'],
            'humidity'=>$this->event['humidity'],
            "buffer_time"=>$this->event['buffer_time'],
            "configurationRows" => $this->createConfigurationRows()
        ];
        return $newData;
    }

    public function createBuffer(string $time, string $date,string $buffer,$eventId)
    {
        if($this->event['buffer_time']=="No buffer"){
            $bufferEvent = [];
        }
        else {
            //echo $date.$time.$buffer;
            $dataStart = new DateTime($date." ".$time);
            list($hours,$minutes) = explode(':',$buffer);
           // echo $hours ." : ".$minutes;
            $dataStart->modify("- ". $hours . " hours");
            $dataStart->modify("- ".$minutes." min");
            //echo $dataStart->format("H:i");
            $bufferEvent = [
                'id' => $eventId+1,
                'title'=>"Buffer Time",
                'type'=>"buffer",
                "start" => $date."T".$dataStart->format("H:i"),
                "end"=> $date."T".$time,
            ];
        }
        return $bufferEvent;
    }
    public function chooseBox(string $str)
    {
        return ($str=="box1") ? "slot" : "textshit";
    }
    public function createConfigurationRows()
    {
        $configurationRows = [];
        $r = round((sizeof($this->event)-8)/3);
        for ($i=1; $i <= $r; $i++) { 
            $arr = [
                'time'=>$_POST['time'.$i],
                'altitude'=>(int)$_POST['altitude'.$i],
                'oxygen'=>(int)$_POST['oxygen'.$i],
            ];
            array_push($configurationRows,($arr));
        }
        return $configurationRows;
    }
    public function save($arr,$buffer){

        if($this->id <= sizeof($this->data)-1){
            $this->SaveEditJson($arr,$buffer);
        }
        else {
            $this->SaveJson($arr,$buffer);
        }
        //var_dump($this->data);
      
    }
    public function SaveJson($arr,$buffer)
    {
        $this->data[]=$arr;
        $this->data[]=$buffer;
        $save_json = json_encode($this->data);
        file_put_contents('events.json',$save_json);
    }
    public function SaveEditJson($arr,$buffer){
        $this->data[$this->id]=$arr;
        $this->data[$this->id+1]=$buffer;
        $save_json = json_encode($this->data);
        file_put_contents('events.json',$save_json);
    }
}
$service = new SaveService;
$events_data = $service->CreateScenario();
$service->save($events_data['event'],$events_data['event_buffer']);

//--------------------------WITHOUT PIWO------------------------------//
//$data = file_get_contents('php://input');
//$data = json_encode(file_get_contents('php://input'), true);
//print_r($data);
//print_r($data);
//$json = json_encode($_POST);
/*
  {
                id: "4",
                title: "Test slot",
                start: "2021-08-10T10:30:00",
                end: "2021-08-11T12:30:00",
                type: "slot",
                choose_box: "box1",
                temperature: 26,
                humidity: 55,
                no_buffer:"checked",
                buffer_time: "0:30",
                configurationRows: [
                  {   time: "12:30",
                    altitude: 33,
                    oxygen: 25
                  }, {
                    time: "11:30",
                    altitude: 25,
                    oxygen: 14
                  }
                ]
              },
 */
/*function createBuffer(string $time, string $date,string $buffer,$eventId){
    echo $date.$time.$buffer;
    $dataStart = new DateTime($date." ".$time);
    $dataStart->modify("- ". 2 . " hours");
    echo $dataStart->format("H:i");
    $bufferEvent = [
        'id' => $eventId+1,
        'title'=>"Buffer Time",
        'type'=>"buffer",
        "start" => $date."T".$dataStart->format("H:i"),
        "end"=> $date."T".$time,
    ];
    
    return $bufferEvent;
}*/
/*function chooseBox(string $str){

    return ($str=="box1") ? "slot" : "textshit";
}
$data = json_decode(file_get_contents('events.json',true));


$configurationRows = [];
$r = round((sizeof($_POST)-8)/3);
echo $r;
echo "<br>";
var_dump( $data );
echo "<br>";
$foor = False;

$json_id;
for ($i=1; $i <= $r; $i++) { 
    $arr = [
        'time'=>$_POST['time'.$i],
        'altitude'=>(int)$_POST['altitude'.$i],
        'oxygen'=>(int)$_POST['oxygen'.$i],
    ];
    array_push($configurationRows,($arr));
}
for ($i=0; $i < sizeof($data); $i++) {
    var_dump("TEST ".$id); 
    if ($data[$i]->id==$_POST['EventId']){
        $foor = True;
        $json_id = $i;
    }
}

(!$foor) ? $id=sizeof($data)+2 : $id = $_POST['EventId'];
echo "ID: ".$id;
$buffer == null;
/*if($_POST['buffer_time']!="No buffer"){
    $buffer = createBuffer($_POST['start_time'],$_POST['date'],$_POST['buffer_time'],$id);
}*/
/*if($_POST['buffer_time']=="No buffer" && $foor){
    if($data[$json_id+1]->title=="Buffer Time"){
        echo "<br>Delete buffer ".$data[$_POST['EventId']]->id."<br>";
        unset($data[$json_id+1]);
        $RemovedBuffer = TRUE;
    }
}
$newData = [
    'id' => $id,
    'title'=>$_POST['title'],
    'start'=>$_POST['date']."T".$_POST['start_time'],
    'end' =>$_POST['date']."T".$_POST['finish_time'],
    "type"=>chooseBox($_POST['choose_box']),
    "choose_box"=>$_POST['choose_box'],
    "temperature"=>$_POST['temperature'],
    'humidity'=>$_POST['humidity'],
    "buffer_time"=>$_POST['buffer_time'],
    "configurationRows" => $configurationRows
];
var_dump($foor);
var_dump($buffer);

if($buffer!=null){
    ($foor) ? $data[$i-2] = $newData : $data[]=$newData;
    ($foor ) ? $data[$i-1] = $buffer : $data[] = $buffer ;
}
else {
    if($RemovedBuffer){
        $data[$i-2] = $newData;
    }
    else {
        ($foor) ? $data[$i-1] = $newData : $data[]=$newData;
    }
    
}

echo "Foo: ".$foor;
$newjson = json_encode($data);
echo 'i: '.$i;
var_dump($newjson);
file_put_contents('events.json',$newjson);
   //Zaebok
//}
//else {
//    echo "Somethin wrong";
//}
/*
 {
    "id": "2",
    "title": "Buffer Time",
    "start": "2021-04-21T10:30:00",
    "end": "2021-04-21T11:30:00",
    "type": "buffer"
  }
 */
//var_dump(json_encode($data));

//var_dump($_POST);
//
//$current = file_get_contents( 'events.json' );
//
//$current = str_replace(']',",", $current);
//$current .=$data . "\n]";
//print_r($current);
//
//file_put_contents( 'events.json', $current );
//
//
//file_put_contents('events.json', print_r($current, true));
//
//
