<?php
   
   print_r($_GET); 
   require "../dbconnect.php";
    
    $sql = <<< SQL
        DELETE FROM nodes
        WHERE node_id = :id
    SQL;   

    $qry = $db->prepare($sql);
    $qry->bindValue(':id', $_GET["id"]);
        
    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }
       
    header("Location: /dbit/lst_depots.php");
    die();
