<?php
    // Init
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_depot.php";

    session_start();

    // Init
    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();

    $return_location = "/dbit/view/lst_depots.php";

    // Rout
    switch($dm->post["submit"]){

        case "delete":
            $dpt = new Depot($db, id: $dm->post["id"]);
            $dpt->delete();
            break;

        case "register":
            $dpt = new Depot($db);
            unset($dm->post["submit"]);
            $dpt->parse($dm->post);
            $dpt->save();
            break;
    }

    // Return to list
    header("Location: $return_location");
    die();


