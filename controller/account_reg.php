<?php
   
    
    require_once "cls_db_manager.php";
    $dbm = new DB_manager("../config/db_ds.txt");
    $db = $dbm->connect();


    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);

    $new_acc = $_POST["account_id"] === null;

    // Lägg in i nodes

    if($new_acc) {
        $sql = <<< SQL
        INSERT INTO nodes(label, title, parent, type)
        VALUES (:label, :title, :parent, "Account")
        SQL;
    }
    else {
        $sql = <<< SQL
        UPDATE nodes
        SET label = :label, title = :title, parent = :parent, type = "Account"
        WHERE node_id = :id
        SQL;
    }
    

    $qry = $db->prepare($sql);
    $qry->bindValue(':label', $_POST["label"]);
    $qry->bindValue(':title', $_POST["title"]);
    $qry->bindValue(':parent', $_POST["parent"] != null?(int) $_POST["parent"]:null);
    if(!$new_acc) {
        $qry->bindValue(':id', $_POST["account_id"]);
    }
    
    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }

    $node_id = $new_acc?$db->lastInsertId():$_POST["account_id"];


    // Lägg in i accounts

    if($new_acc) {
        $sql = <<< SQL
        INSERT INTO accounts(account_id, upper_limit, lower_limit, trustee, alias_id, rate, note)
        VALUES (:account_id, :upper_limit, :lower_limit, :trustee, :alias_id, :rate, :note)
        SQL;
    } else {
        $sql = <<< SQL
        UPDATE accounts
        SET 
            upper_limit = :upper_limit, 
            lower_limit = :lower_limit, 
            trustee = :trustee, 
            alias_id = :alias_id,
            rate = :rate,
            note = :note
        WHERE
            account_id = :account_id
        SQL;
    }
    
    
    $qry = $db->prepare($sql);
    $qry->bindValue(':account_id', $node_id);
    $qry->bindValue(':upper_limit', $_POST["upper_limit"] != null?(float)$_POST["upper_limit"]:null);
    $qry->bindValue(':lower_limit', $_POST["lower_limit"] != null?(float)$_POST["lower_limit"]:null);
    $qry->bindValue(':trustee', $_POST["trustee"]);
    $qry->bindValue(':alias_id', $_POST["alias_id"]);
    $qry->bindValue(':rate', $_POST["rate"] != null?(float)$_POST["rate"]:null);
    $qry->bindValue(':note', $_POST["note"]); 

    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }
        
    header("Location: /dbit/lst_accounts.php");
    die();
