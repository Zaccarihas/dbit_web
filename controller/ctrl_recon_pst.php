<?php
    // Init
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_tbl_recon.php";

    $dbg = false;

    session_start();

    // Init
    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();

    $return_location = "/dbit/view/lst_recons.php";

    if($dbg){
        echo "<hr>Recon post-controller";
        echo "<br><pre>"; print_r($dm->post); echo "</pre>";
        die();
    }

    // Rout
    switch($dm->post["submit"]){

        case "delete":
            $rec = new Recon($db, id: $dm->post["id"]);
            $rec->delete();
            break;

        case "register":
            $rec = new Recon($db);
            unset($dm->post["submit"]);
            $rec->parse($dm->post);
            $rec->save();
            break;
    }

    // Return to list
    header("Location: $return_location");
    die();


