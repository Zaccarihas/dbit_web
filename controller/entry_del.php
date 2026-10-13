<?php
   
   print_r($_GET); 
   require "../dbconnect.php";
    
    $sql = <<< SQL
        DELETE FROM recons
        WHERE recon_id = :id
    SQL;   

    $qry = $db->prepare($sql);
    $qry->bindValue(':id', $_GET["id"]);
        
    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }
       
    header("Location: /dbit/lst_recons.php");
    die();
