<?php
    // Init
    require_once "error_reporting.php";
    require_once "cls_db_manager.php";
    require_once "cls_datatransfer_manager.php";
    require_once "cls_account.php";

    session_start();

    // Init
    $dm = new Datatransfer_manager();
    $dbm = new DB_manager("config/db_ds.txt");
    $db = $dbm->connect();

    $return_location = "/dbit/lst_accounts.php";

    // Rout
    if($dm->get_set()){
    
        // Delete account
        $acc = new Account($db, id: $this->get["id"]);
        $acc->delete();

    }
    else {
        
        // Save account
        $acc = new Account($db);
        $acc->parse($dm->post);
        $acc->save();

    }

    // Return to list
    header("Location: $return_location");
    die();


