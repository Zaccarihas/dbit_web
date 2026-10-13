<?php

    require "../dbconnect.php";

    //print_r($_POST);

    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);

    $new = $_POST["node_id"] === null;

    if($new) {
        $sql = <<< SQL
        INSERT INTO nodes(label, title, parent, type)
        VALUES (:label, :title, :parent, "Category")
        SQL;
    }
    else {
        $sql = <<< SQL
        UPDATE nodes
        SET label = :label, title = :title, parent = :parent
        WHERE node_id = :id
        SQL;
    }

    //print($sql);
    
    $qry = $db->prepare($sql);
    $qry->bindValue(':label', $_POST["label"]);
    $qry->bindValue(':title', $_POST["title"]);
    $qry->bindValue(':parent', $_POST["parent"]);
    if(!$new) {
        $qry->bindValue(':id', $_POST["node_id"]);
    }
    
    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }
            
    header("Location: /dbit2/lst_categories.php");
    die();
