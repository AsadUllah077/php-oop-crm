<?php 
class Database {
    protected $host = 'localhost';
    protected $user = 'root';
    protected $password = '';
    protected $dbname = 'oop-project';

    public $con = null;

    public function __construct(){
        if($this->con == null){
            $this->con = mysqli_connect($this->host,$this->user,$this->password,$this->dbname);
        }
        if($this->con->connect_errno){
            echo 'Fail'.$this->con->connect_error;
        }
    }
}

?>