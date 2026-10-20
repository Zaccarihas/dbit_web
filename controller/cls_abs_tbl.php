<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
abstract class DBit_Table {

    protected $db;
    protected $dbg = false;

    public $id;

    protected $sql_get;
    protected $sql_add;
    protected $sql_upd;
    protected $sql_del;

    public function __constructor(
        $db,
        $sql_get,
        $sql_add,
        $sql_upd,
        $sql_del,
        $id = null
    ){
        $this->db = $db;
        $this->id = $id;
        $this->sql_get = $sql_get;
        $this->sql_add = $sql_add;
        $this->sql_upd = $sql_upd;
        $this->sql_del = $sql_del; 
    }

    public function is_new(){
        return $this->id === null;
    }

    public function parse($src){
        foreach($src as $key => $value){
            $this->$key = $value;
        }
    }

    abstract function get();

    public function dump() {
        foreach($this->get() as $attribute => $value){
            echo "<br>".$attribute.": "; var_dump($this->$attribute);
        }
        echo "<hr>";
    }

    public function load(){
        if(!$this->is_new()){
            $qry = $this->db->prepare($this->sql_get);
            $qry->bindValue(':id', $this->id);
            DB_manager::run($qry);

            $this->parse($qry->fetchAll(PDO::FETCH_ASSOC)[0]);
        }
    }

    public function save($attributes = null){
        $sql = $this->is_new()?$this->sql_add:$this->sql_upd;
        $qry = $this->db->prepare($sql);
        DB_manager::run($qry, $attributes??$this->get());

        // Get node id if new node has been added
        $this->id = $this->is_new()?$this->db->lastInsertId():$this->id;
    }

    public function delete(){
        if(!$this->is_new()){
            $qry = $this->db->prepare($this->sql_del);
            $qry->bindValue(':id', $this->id);
            DB_manager::run($qry);
        }
    }

}