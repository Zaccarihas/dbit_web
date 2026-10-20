<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_tbl_node.php";

class Category extends Node {

    public function __construct(
        $db,
        $id = null,
        $label = null,
        $title = null,
        $parent = null,
        $subgroup = null,
        $description = null,
        $note = null,
    ){
        parent::__construct(
            $db, 
            id: $id, 
            label: $label,
            title: $title,
            parent: $parent,
            subgroup: $subgroup,
            description: $description,
            note: $note
        );

    }

    public function save($attributes = null){        
        $attributes = $this->get();
        $attributes['type'] = "Category";
        parent::save($attributes);
    }
}