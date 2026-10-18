<?php

    // Init
    session_start();
    require "error_reporting.php";
    require "dbconnect.php";

    function save_entform_to_tmp() {
        $formatter = new NumberFormatter('sv_SE', NumberFormatter::DECIMAL);

        $_SESSION["entry"]["entry_id"] = $_POST["entry_id"];
        $_SESSION["entry"]["node_id"] = $_POST["node"];
        $_SESSION["entry"]["total"] = $formatter->parse($_POST["total"]);
        $_SESSION["entry"]["quantity"] = $formatter->parse($_POST["quantity"]);
        $_SESSION["entry"]["unit"] = $_POST["unit"];
        $_SESSION["entry"]["product"] = $_POST["product"];
        $_SESSION["entry"]["ent_note"] = $_POST["ent_note"];

    }

    //echo "<br/>POST:<br><pre>"; print_r($_POST); echo "</pre>";
    //echo "<br/>GET:<br><pre>"; print_r($_GET); echo "</pre>";
    //die();

    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);


    // Update the temporary entry
    save_entform_to_tmp();

    $ent_idx = isset($_GET["ent"])?$_GET["ent"]:null;
    $tmp_ent = $_SESSION["entry"];

    $view_folder = "/dbit/view/";
    $return_location = "frm_transaction.php";
    $current_location = "frm_ent_category.php";

    // Route
    switch($_POST["submit"]) {

        case "add_tag":

            if ($_POST["tag"] !== null && !in_array($_POST["tag"], $_SESSION["entry"]["tags"])) {
                $_SESSION["entry"]["tags"][] = $_POST["tag"];
            }

            header("Location: ".$view_folder.$current_location.($ent_idx===null?"":"?ent=".$ent_idx));
            exit();
            break;
    
        case "register":


            // Add label and title to the temporary entry
            $sql = <<< SQL
                SELECT CONCAT(label, " - ", title) AS prompt, type_group AS type
                FROM nodes WHERE node_id = :id
            SQL;
            $qry = $db->prepare($sql);
            $qry->execute([":id" => $_SESSION["entry"]["node_id"]]);
            $res = $qry->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION["entry"]["prompt"] = $res[0]["prompt"];

            // Add direction depending on category type
            $_SESSION["entry"]["ent_direction"] = $res[0]["type"] == "exp"?"to":"from";

            //echo "<br/>POST:<br><pre>"; print_r($_POST); echo "</pre>";
            //echo "<br/>GET:<br><pre>"; print_r($_GET); echo "</pre>";
            //echo "<br>Entry: "; var_dump($ent_idx);
            //die();

            // Register the temporary entry into the temporary transaction
            if($ent_idx==null) {
                
                $_SESSION["transaction"]["ent_categories"][] = $_SESSION["entry"];
                $_SESSION["transaction"]["total_categories"] += ($_SESSION["entry"]["ent_direction"]=="to"?1:-1) * $_SESSION["entry"]["total"];

            } else {
               
                // Update the accounts total
                $pre_dir = $_SESSION["transaction"]["ent_categories"][$ent_idx]["ent_direction"];
                $pre_tot = $_SESSION["transaction"]["ent_categories"][$ent_idx]["total"];
                $cur_dir = $_SESSION["entry"]["ent_direction"];
                $cur_tot = $_SESSION["entry"]["total"];  

                $_SESSION["transaction"]["total_categories"] -= ($pre_dir == "to"?1:-1) * $pre_tot;
                $_SESSION["transaction"]["total_categories"] += ($cur_dir == "to"?1:-1) * $cur_tot;
            
                $_SESSION["transaction"]["ent_categories"][$ent_idx] = $_SESSION["entry"];
            
            }

            // Calculate remaining difference
            $_SESSION["transaction"]["difference_categories"] = $_SESSION["transaction"]["total_target"] - abs($_SESSION["transaction"]["total_categories"]);

            //echo "<br/>POST:<br><pre>"; print_r($_SESSION); echo "</pre>";
            //die();

            
            break;

        case "delete":
                        
            // Remove the entry from the temporary transaction
            unset($_SESSION["transaction"]["ent_categories"][$ent_idx]);
            $_SESSION["transaction"]["total_categories"] -= ($_SESSION["entry"]["ent_direction"]=="from"?-1:1) * $_SESSION["entry"]["total"];
            
            
            break;

        case "cancel":
            break;

        default:
            // Remove selected tag from temporary entry
            $idx = array_search($_POST["submit"], $_SESSION["entry"]["tags"], true);
            if($idx !== false) {
                    array_splice($_SESSION["entry"]["tags"], $idx, 1);
                }
            
            header("Location: ".$view_folder.$current_location.($ent_idx===null?"":"?ent=".$ent_idx));
            exit();
            break;
            
    }

    // Remove tempoarary entry from session
    unset($_SESSION["entry"]);

    // Return to transaction form
    header("Location: ".$view_folder.$return_location);
    exit();

             
