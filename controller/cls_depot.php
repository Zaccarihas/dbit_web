<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_tbl_node.php";

class Depot extends Node {

    protected $sql_get = <<< SQL
    SELECT * FROM vw_depots WHERE id = :id
    SQL;

    public function __construct(
        $db,
        $id = null,
        $label = null,
        $title = null,
        $parent = null,
        $description = null,
        $note = null,
    ){
        parent::__construct(
            $db, 
            id: $id, 
            label: $label,
            title: $title,
            parent: $parent,
            description: $description,
            note: $note
        );
    }

    public function save($attributes = null){

        $attributes = $this->get();
        $attributes['type'] = "Depot";
        parent::save($attributes);
    }
}