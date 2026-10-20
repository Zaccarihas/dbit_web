<?php
    // Init
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_associate.php";

    $debug = false;

    session_start();

    // Init
    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();

    $return_location = "/dbit/view/lst_associates.php";

    if($debug){
        echo "<hr>Associate post-controller";
        echo "<br><pre>"; print_r($dm->post); echo "</pre>";
        die();
    }

    // Rout
    switch($dm->post["submit"]){

        case "delete":
            $asc = new Associate($db, id: $dm->post["id"]);
            $asc->delete();
            break;

        case "register":
            $asc = new Associate($db);
            unset($dm->post["submit"]);
            $asc->parse($dm->post);
            $asc->save();
            break;
    }

    // Return to list
    header("Location: $return_location");
    die();


