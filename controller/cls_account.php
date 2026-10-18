<?php
require_once "error_reporting.php";
require_once "cls_db_manager.php";

class Account {

    public $id;
    public $label;
    public $title;
    public $parent;
    public $description;
    public $upper_limit;
    public $lower_limit;
    public $trustee;
    public $alias;
    public $rate;
    public $note;

    private $db;
    private $debug = false;

    private $sql_get = <<< SQL
    SELECT * FROM vw_accounts
    WHERE id = :id
    SQL;
    
    private $sql_add_node = <<< SQL
    INSERT INTO nodes(node_id, label, title, parent, type, description, note)
    VALUES (:node_id, :label, :title, :parent, "Account", :description, :note)
    SQL;

    private $sql_add_account = <<< SQL
    INSERT INTO accounts(account_id, upper_limit, lower_limit, trustee, alias_id, rate)
    VALUES (:account_id, :upper_limit, :lower_limit, :trustee, :alias_id, :rate)
    SQL;

    private $sql_upd_node = <<< SQL
    UPDATE nodes
    SET label = :label, title = :title, parent = :parent, type = "Account", description = :description, note = :note
    WHERE node_id = :id
    SQL;

    private $sql_upd_account = <<< SQL
    UPDATE accounts
    SET upper_limit = :upper_limit, lower_limit = :lower_limit, trustee = :trustee, alias_id = :alias, rate = :rate
    WHERE account_id = :id
    SQL;

    private $sql_del_node = <<< SQL
    DELETE FROM nodes
    WHERE node_id = :id
    SQL;

    private $sql_del_account = <<< SQL
    DELETE FROM accounts
    WHERE account_id = :id
    SQL;

    public function __construct(
        $db,
        $id = null, 
        $label = null, 
        $title = null, 
        $description = null,
        $upper_limit = null,
        $lower_limit = null,
        $trustee = null,
        $alias = null, 
        $rate = null,
        $note = null
        ){

        $this->db = $db;
        $this->id = $id;
        $this->label = $label;
        $this->title = $title;
        $this->description = $description;
        $this->upper_limit = $upper_limit;
        $this->lower_limit = $lower_limit;
        $this->trustee = $trustee;
        $this->alias = $alias;
        $this->rate = $rate;
        $this->note = $note;

    }

    public function load(){
        if(!$this->is_new()){
            $qry = $this->db->prepare($this->sql_get);
            $qry->bindValue(':id', $this->id);
            DB_manager::run($qry);

            $this->parse($qry->fetchAll(PDO::FETCH_ASSOC)[0]);
        }
    }

    public function save(){

        if($this->debug) {
            echo "<hr>Account - save";
        }

        // Node general query
        $sql = $this->is_new()?$this->sql_add_node:$this->sql_upd_node;
        $qry = $this->db->prepare($sql);

        if($this->debug) {
            echo "<br>sql: ".$sql;
        }

        // Set attributes for the Nodes query
        $qry->bindValue(':node_id', $this->id??null);
        $qry->bindValue(':label', $this->label);
        $qry->bindValue(':title', $this->title);
        $qry->bindValue(':parent', $this->parent);
        $qry->bindValue(':description', $this->description);
        $qry->bindValue(':note', $this->note);
        DB_manager::run($qry);

        if($this->debug) {
            echo "<br>Nodes update!";
        }

        // Get correct node id to relate the specific account info to.
        $node_id = $this->is_new()?$this->db->lastInsertId():$this->id;

        // Account specific query
        $sql = $this->is_new()?$this->sql_add_account:$this->sql_upd_account;
        $qry = $this->db->prepare($sql);
            
        // Set attributes for the accounts query
        $qry->bindValue(':account_id', $node_id);
        $qry->bindValue(':upper_limit', $this->upper_limit);
        $qry->bindValue(':lower_limit', $this->lower_limit);
        $qry->bindValue(':trustee', $this->trustee);
        $qry->bindValue(':alias_id', $this->alias);
        $qry->bindValue(':rate', $this->rate);
        DB_manager::run($qry);

        if($this->debug){
            echo "<br>Accounts updated";
        }
    }

    public function delete(){
        if(!$this->is_new()){

            // Delete the account specific information from the Accounts-table
            $qry = $this->db->prepare($this->sql_del_account);
            $qry->bindValue(':id', $this->id);
            DB_manager::run($qry);

            // Delete the related general node information from the Nodes-table
            $qry = $this->db->prepare($this->sql_del_node);
            $qry->bindValue(':id', $this->id);
            DB_manager::run($qry);

        }
    }

    public function is_new(){
        return $this->id === null;
    }

    public function prompt() {
        return $this->label." - ".$this->title;
    }

    public function parse($src){
        foreach($src as $key => $value){
            $this->$key = $value;
        }
    }

    public function dump() {
        echo "<br><hr>";
        echo "id: "; var_dump($this->id);
        echo "<br>label: "; var_dump($this->label);
        echo "<br>title: "; var_dump($this->title);
        echo "<br>parent: "; var_dump($this->parent);
        echo "<br>description: "; var_dump($this->description);
        echo "<br>upper_limit: "; var_dump($this->upper_limit);
        echo "<br>lower_limit: "; var_dump($this->lower_limit);
        echo "<br>trustee: "; var_dump($this->trustee);
        echo "<br>alias: "; var_dump($this->alias);
        echo "<br>rate: "; var_dump($this->rate);
        echo "<br>note: "; var_dump($this->note);
        echo "<hr>";
    }
}