<?php
    session_start();
    echo "<pre>"; print_r($_GET); echo "</pre>";
    echo "<pre>"; print_r($_SESSION); echo "</pre>";

    // Kontrollera att taggen inte redan finns i listan
    
    //if ($_SESSION["transaction"]["entries"]["$_GET["ent_id"])
    $tags = array_column($_SESSION["transaction"]["entries"][$_GET["ent_id"]]["tags"], "tag");
    if(in_array($_GET["tag"], $tags)){
            $_SESSION["alert"] = "Etiketten är redan satt för den här posten";
            header("Location: /dbit/frm_entry.php?ent_id=".$_GET["ent_id"]);
            die();
    }
    
    /*
    $tags = $_SESSION["tags"];
    foreach($tags as $idx => $tag){
        if($tag["entry"] == $_GET["id"] && $tag["tag"]==$_GET["tag"]){

        }
    }
    */

    // Register the new tag
    $_SESSION["transaction"]["entries"][$_GET["ent_id"]]["tags"][] = [
        "entry" => null,
        "tag" => $_GET["tag"]
    ];

    //echo "<pre>"; print_r($_SESSION); echo "</pre>";
       
    header("Location: /dbit/frm_entry.php?ent_id=".$_GET["ent_id"]);
    die();
