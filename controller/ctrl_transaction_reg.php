<?php

    require "../dbconnect.php";

    // Kom ihåg att komplettera alla entries från session som ska läggas till med 
    // rätt transaktionsid eftersom det sätts till null när entryt lägg till sessions-variabeln.

    // Ta också bort sessionsvariabeln när den har överförts till databasen.

    // Entries med entry_id = null är nya och ska läggas till övriga ska uppdateras.


    //print_r($_POST);

    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);

    $new = $_POST["recon_id"] === null;

    if($new) {
        $sql = <<< SQL
        INSERT INTO recons(date, account, note, total)
        VALUES (:date, :account, :note, :total)
        SQL;
    }
    else {
        $sql = <<< SQL
        UPDATE recons
        SET date = :date, account = :account, note = :note, total = :total
        WHERE recon_id = :id
        SQL;
    }

    //print($sql);
    
    $qry = $db->prepare($sql);
    $qry->bindValue(':date', $_POST["reconciled"]);
    $qry->bindValue(':account', $_POST["account"]);
    $qry->bindValue(':note', $_POST["note"]);
    $qry->bindValue(':total', $_POST["total"]!=null?(float)$_POST["total"]:0);
    if(!$new) {
        $qry->bindValue(':id', $_POST["recon_id"]);
    }
    
    try{
        $qry->execute();
    } catch(PDOException $e) {
        echo "DB Error: ".$e->getMessage();
        die();
    }
            
    header("Location: /dbit/lst_recons.php");
    die();
