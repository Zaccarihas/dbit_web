<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_tbl_node.php";

class Associate extends Node {

    public $location;

    private $debug = false;

    protected $sql_get = <<< SQL
    SELECT * FROM vw_associates WHERE id = :id
    SQL;

    private $sql_add_assoc = <<< SQL
    INSERT INTO associates(associate_id, location)
    VALUES (:id, :location)
    SQL;

    private $sql_upd_assoc = <<< SQL
    UPDATE associates
    SET location = :location
    WHERE id = :id
    SQL;

    private $sql_del_assoc = <<< SQL
    DELETE FROM associates
    WHERE id = :id
    SQL;


    public function __construct(
        $db,
        $id = null,
        $title = null,
        $parent = null,
        $description = null,
        $note = null,
        $location = null
    ){
        parent::__construct(
            $db, 
            id: $id, 
            title: $title,
            parent: $parent,
            description: $description,
            note: $note
        );

        $this->location = $location;

    }

    public function get(){
        $attributes = parent::get();
        $attributes["location"] = $this->location;
        return $attributes;
    }

    public function save($attributes = null){

        // Save the node-information        
        $is_new = $this->is_new(); // Since is_new checks if id is null and the id will be updated when i save the node
        $attributes = $this->get();
        $attributes['type'] = "Associate";
        unset($attributes["location"]);
        parent::save($attributes);

        // Save the Associate information
        $sql = $is_new?$this->sql_add_assoc:$this->sql_upd_assoc;
        $qry = $this->db->prepare($sql);
        DB_manager::run($qry, [
            'id' => $this->id,
            'location' => $this->location
        ]);

    }

    public function del(){

        if(!$this->is_new()){
            // Delete the node
            parent::delete();

            // Delete the Associate
            $qry = $this->db->prepare($this->sql_del_assoc);
            $qry->bindValue(':id', $this->id);
            DB_manager::run($qry);
        }

    }
}