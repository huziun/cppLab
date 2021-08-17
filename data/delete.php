<?php echo "PHP : ".$_POST['event_id'];

class DeleteService {

    private $data;
    private $id;

    public function __construct()
    {
        $this->data = json_decode(file_get_contents('events.json',true)); 
        $this->id = $_POST['event_id'];
    }
    // remember about our statement "index == event_id"
    public function delete(){
        $this->data[$this->id] = [];
        $this->data[$this->id+1] = [];
    }
    public function save(){
        var_dump($this->data);
        $save_json = json_encode($this->data);
        file_put_contents('events.json',$save_json);
    }
}
$service = new DeleteService;
$service->delete();
$service->save();