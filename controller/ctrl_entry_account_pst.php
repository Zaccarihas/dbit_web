<?php

    // Init
    require "error_reporting.php";
    require "dbconnect.php";
    session_start();

    function save_entform_to_tmp() {
        
        $formatter = new NumberFormatter('sv_SE', NumberFormatter::DECIMAL);

        $_SESSION["entry"]["entry_id"] = $_POST["entry_id"];
        $_SESSION["entry"]["node_id"] = $_POST["account"];
        $_SESSION["entry"]["ent_direction"] = $_POST["ent_direction"];
        $_SESSION["entry"]["total"] = $formatter->parse($_POST["total"]);
        $_SESSION["entry"]["status"] = $_POST["status"];
        $_SESSION["entry"]["method"] = $_POST["method"];
        $_SESSION["entry"]["ent_note"] = $_POST["ent_note"];
    }

    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);

    $ent_idx = isset($_GET["ent"])?$_GET["ent"]:null;
    $tmp_ent = $_SESSION["entry"];

    $view_folder = "/dbit2/view/";
    $return_location = "frm_transaction.php";

    // Route
    switch($_POST["submit"]) {

        case "register":

            // Update the temporary entry
            save_entform_to_tmp();

            // Add label and title to the temporary entry
            $sql = <<< SQL
                SELECT CONCAT(label, " - ", title) AS prompt FROM nodes WHERE node_id = :id
            SQL;
            $qry = $db->prepare($sql);
            $qry->execute([":id" => $_SESSION["entry"]["node_id"]]);
            $res = $qry->fetchAll(PDO::FETCH_COLUMN);
            $_SESSION["entry"]["prompt"] = $res[0];
            
            // Register the temporary entry into the temporary transaction
            if($ent_idx===null) {
                $_SESSION["transaction"]["ent_accounts"][] = $_SESSION["entry"];  
                $_SESSION["transaction"]["total_accounts"] += ($_SESSION["entry"]["ent_direction"]=="from"?-1:1) * $_SESSION["entry"]["total"];  
                
            } else {

                // Update the accounts total
                $pre_dir = $_SESSION["transaction"]["ent_accounts"][$ent_idx]["ent_direction"];
                $pre_tot = $_SESSION["transaction"]["ent_accounts"][$ent_idx]["total"];
                $cur_dir = $_SESSION["entry"]["ent_direction"];
                $cur_tot = $_SESSION["entry"]["total"];  

                $_SESSION["transaction"]["total_accounts"] -= ($pre_dir == "from"?-1:1) * $pre_tot;
                $_SESSION["transaction"]["total_accounts"] += ($cur_dir == "from"?-1:1) * $cur_tot;
                
                // Update entry in transaction
                $_SESSION["transaction"]["ent_accounts"][$ent_idx] = $_SESSION["entry"];
                
            }

            // Calculate remaining difference
            $_SESSION["transaction"]["difference_accounts"] = $_SESSION["transaction"]["total_target"] - abs($_SESSION["transaction"]["total_accounts"]);
            
            // Update the accounts total
            
            break;

        case "delete":
                        
            // Remove the entry from the temporary transaction
            unset($_SESSION["transaction"]["ent_accounts"][$ent_idx]);
            $_SESSION["transaction"]["total_accounts"] -= ($_SESSION["entry"]["ent_direction"]=="from"?-1:1) * $_SESSION["entry"]["total"];  
            
            break;
            
    }

    // Remove tempoarary entry from session
    unset($_SESSION["entry"]);

    // Return to transaction form
    header("Location: ".$view_folder.$return_location);
    exit();

             
