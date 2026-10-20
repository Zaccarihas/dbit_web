<?php
    // Init
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_category.php";

    session_start();

    // Init
    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();

    $return_location = "/dbit/view/lst_categories.php";

    // Rout
    switch($dm->post["submit"]){

        case "delete":
            $cat = new Category($db, id: $dm->post["id"]);
            $cat->delete();
            break;

        case "register":
            $cat = new Category($db);
            unset($dm->post["submit"]);
            $cat->parse($dm->post);
            $cat->save();
            break;
    }

    // Return to list
    header("Location: $return_location");
    die();


