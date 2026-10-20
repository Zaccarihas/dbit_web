<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
class Node {

    public $id;
    public $label;
    public $title;
    public $parent;
    public $type;
    public $subgroup;
    public $description;
    public $note;

    protected $db;
    private $dbg = false;
    private $dbg_delete = true;

    protected $sql_get = <<< SQL
    SELECT * FROM nodes WHERE id = :id
    SQL;

    protected $sql_add = <<< SQL
    INSERT INTO nodes(id, label, title, parent, type, subgroup, description, note)
    VALUES (:id, :label, :title, :parent, :type, :subgroup, :description, :note)
    SQL;

    protected $sql_upd = <<< SQL
    UPDATE nodes
    SET label = :label, title = :title, parent = :parent, type = :type, subgroup = :subgroup, description = :description, note = :note
    WHERE id = :id
    SQL;

    protected $sql_del = <<< SQL
    DELETE FROM nodes
    WHERE id = :id
    SQL;

    public function __construct(
        $db,
        $id = null,
        $label = null,
        $title = null,
        $parent = null,
        $type = null,
        $subgroup = null,
        $description = null,
        $note = null
    )
    {
        $this->db = $db;
        $this->id = $id;
        $this->label = $label;
        $this->title = $title;
        $this->parent = $parent;
        $this->type = $type;
        $this->subgroup = $subgroup;
        $this->description = $description;
        $this->note = $note;
    }

    public function get(){
        return [
            'id' => $this->id,
            'label' => $this->label,
            'title' => $this->title,
            'parent' => $this->parent,
            'type' => $this->type,
            'subgroup' => $this->subgroup,
            'description' => $this->description,
            'note' => $this->note
        ];
    }

    public function is_new(){
        return $this->id === null;
    }

    public function parse($src){
        foreach($src as $key => $value){
            $this->$key = $value;
        }
    }

    public function dump() {
        echo "<br>Node dump<hr>";
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

        if($this->dbg) {
            echo "<br>Node - Save<hr>";
            echo "<br>sql: ".$sql;
            echo "<br>attributes: <pre>"; print_r($this->get()); echo "</pre>";
            
            die();
        }

        DB_manager::run($qry, $attributes??$this->get());

        // Get node id if new node has been added
        $this->id = $this->is_new()?$this->db->lastInsertId():$this->id;
    }

    public function delete(){

        if($this->dbg_delete) {
            echo "<br><hr>Node - Delete";
            echo "sql: $this->sql_del";
            echo "id: ".$this->id;
        }
        if(!$this->is_new()){
            $qry = $this->db->prepare($this->sql_del);
            $qry->bindValue(':id', $this->id);
            DB_manager::run($qry);
        }
    }


}