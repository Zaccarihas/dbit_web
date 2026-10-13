<?php

    session_start();
    echo "<br><br>GET:<br><pre>"; print_r($_GET); echo "</pre>";
    echo "<br><br>POST:<br><pre>"; print_r($_POST); echo "</pre>";
    echo "<br><br>SESSION:<br><pre>"; print_r($_SESSION); echo "</pre>";

    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);

    $new = $_GET["id"] === null;
    $original = $new?null:$_SESSION["entries"][$_GET["id"]];
    //echo "<br><br>ORIGINAL:<br><pre>"; print_r($original); echo "</pre>";

    // Create a result entry by combining existing info with new.
    $res = [
        "entry_id" => $new?null:$original["entry_id"],
        "transaction" => $_SESSION["transaction"]["transaction_id"],
        "note" => $_POST["note"],
        "status" => $_POST["status"],
        "credit_balance" => $_POST["balance_out"],
        "credit_result" => $_POST["result_out"],
        "debet_balance" => $_POST["balance_in"],
        "debet_result" => $_POST["result_in"],
        "amount" => (float)str_replace(',','.',$_POST["total"]),
        "product" => $_POST["product"],
        "quantity" => (float)str_replace(',','.',$_POST["quantity"]),
        "unit" => $_POST["unit"],
        "entry_type" => $new?null:$original["entry_type"]
    ];

    //print("<br><br>New: ".$new);

    // Add the result if it is a new entry ...
    if($new) {
        $_SESSION["transaction"]["entries"][] = $res;
    } 
    // ... otherwise replace 
    else {
        
        /*
        print("<br><br>Uppdaterar Entry ".$_GET["id"]." med:<br>");
        echo "<pre>";
        print_r($res);
        echo "<pre>";
        */
        
        $_SESSION["transaction"]["entries"][$_GET["id"]] = $res;
    }
             
    header("Location: /dbit/frm_transaction.php");
    die();
