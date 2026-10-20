<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_abs_tbl.php";
class Recon extends DBit_Table{

    public $date;
    public $account;
    public $note;
    public $total;

    private $init_sql_get = <<< SQL
    SELECT * FROM recons WHERE id = :id
    SQL;

    private $init_sql_add = <<< SQL
    INSERT INTO recons(id, date, account, note, total)
    VALUES (:id, :date, :account, :note, :total)
    SQL;

    private $init_sql_upd = <<< SQL
    UPDATE recons
    SET date = :date, account = :account, note = :note, total = :total
    WHERE id = :id
    SQL;

    private $init_sql_del = <<< SQL
    DELETE FROM recons
    WHERE id = :id
    SQL;

    public function __construct(
        $db,
        $id = null,
        $date = null,
        $account = null,
        $note = null,
        $total = null,
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
            
        $this->date = $date;
        $this->account = $account;
        $this->note = $note;
        $this->total = $total;
    }

    public function get() {

        return [
            'id' => $this->id,
            'date' => $this->date,
            'account' => $this->account,
            'note' => $this->note,
            'total' => $this->total,
        ];
    }

    public function dump() {
        echo "<br>Recon dump<hr>";
        parent::dump();
    }

}