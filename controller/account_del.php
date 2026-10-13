<?php
   
    require "../dbconnect.php";
    //require "error_reporting.php";

    // Testing
    //echo "<br><h4>GET</h4><pre>"; print_r($_GET); echo "</pre>";

    // Ta bort från accounts
    $sql = <<< SQL
        DELETE FROM accounts
        WHERE account_id = :id
    SQL;   

    $qry = $db->prepare($sql);
    $qry->bindValue(':id', (int)$_GET["id"]);
        
    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }

    // Ta bort från nodes
    $sql = <<< SQL
        DELETE FROM nodes
        WHERE node_id = :id
    SQL;   

    $qry = $db->prepare($sql);
    $qry->bindValue(':id', (int)$_GET["id"]);
        
    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }
       
    header("Location: /dbit/lst_accounts.php");
    die();
