<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_abs_tbl.php";
class Node extends DBit_Table {

    public $label;
    public $title;
    public $parent;
    public $type;
    public $subgroup;
    public $description;
    public $note;

    protected $init_sql_get = <<< SQL
    SELECT * FROM nodes WHERE id = :id
    SQL;

    protected $init_sql_add = <<< SQL
    INSERT INTO nodes(id, label, title, parent, type, subgroup, description, note)
    VALUES (:id, :label, :title, :parent, :type, :subgroup, :description, :note)
    SQL;

    protected $init_sql_upd = <<< SQL
    UPDATE nodes
    SET label = :label, title = :title, parent = :parent, type = :type, subgroup = :subgroup, description = :description, note = :note
    WHERE id = :id
    SQL;

    protected $init_sql_del = <<< SQL
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
        parent::__constructor(
            $db,
            $this->init_sql_get,
            $this->init_sql_add,
            $this->init_sql_upd,
            $this->init_sql_del,
            $id
        );

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

    public function dump() {
        echo "<br>Node dump<hr>";
        parent::dump();
    }

    public function prompt(){
        return $this->label." - ".$this->title;
    }

}