<?php

    // Init
    session_start();

    function save_entform_to_tmp() {
        $_SESSION["entry"]["entry_id"] = $_POST["entry_id"];
        $_SESSION["entry"]["note"] = $_POST["note"];
        $_SESSION["entry"]["status"] = $_POST["status"];
        $_SESSION["entry"]["credit_balance"] = (int)$_POST["balance_out"];
        $_SESSION["entry"]["credit_result"] = (int)$_POST["result_out"];
        $_SESSION["entry"]["debet_balance"] = (int)$_POST["balance_in"];
        $_SESSION["entry"]["debet_result"] = (int)$_POST["result_in"];
        $_SESSION["entry"]["amount"] = (float)$_POST["total"];
        $_SESSION["entry"]["quantity"] = (float)$_POST["quantity"];
        $_SESSION["entry"]["transfer_method"] = $_POST["transfer_method"];
        $_SESSION["entry"]["product"] = $_POST["product"];
        $_SESSION["entry"]["unit"] = $_POST["unit"];
        $_SESSION["entry"]["entry_type"] = $_POST["entry_type"];
    }

    // Testing
    /*
    echo "<br><br>GET:<br><pre>"; print_r($_GET); echo "</pre>";
    echo "<br><br>POST:<br><pre>"; print_r($_POST); echo "</pre>";
    echo "<br><br>SESSION:<br><pre>"; print_r($_SESSION); echo "</pre>";
    */

    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);


    $ent_idx = isset($_GET["ent"])?$_GET["ent"]:null;
    $tmp_ent = $_SESSION["entry"];

    // Route
    switch($_POST["submit"]) {
        case "cancel":

            // Remove the temporary entry from session
            unset($_SESSION["entry"]);
            
            header("Location: /dbit2/frm_transaction.php");
            exit();
            break;
        case "register":
            //echo "Du tryckte Register";
            // Update the temporary entry
            save_entform_to_tmp();

            // Register the temporary entry into the temporary transaction
            if($ent_idx===null) {
                $_SESSION["transaction"]["entries"][] = $_SESSION["entry"];    
            } else {
                $_SESSION["transaction"]["entries"][$ent_idx] = $_SESSION["entry"];
            }
            

            // Remove the temporary entry
            unset($_SESSION["entry"]);

            // Return to the transaction form
            header("Location: /dbit2/frm_transaction.php");
            exit();
            break;

        case "delete":
                        
            // Remove the entry from the temporary transaction
            unset($_SESSION["transaction"]["entries"][$ent_idx]);
            
            // Remove the temporary entry
            unset($_SESSION["entry"]);
            
            // Return to the transaction form
            header("Location: /dbit2/frm_transaction.php");
            exit();
            break;

        case "add_tag":
                        
            // Keep the form input in temporary entry
            save_entform_to_tmp();

            // Add the tag to the temporary entries tag list
            $_SESSION["entry"]["tags"][] = $_GET["tag"];
            
            // Return to entry form
            if($ent_idx===null){
                $ext = "";
            } else {
                $ext = "?ent=".$ent_idx;
            }
            header("Location: /dbit2/frm_entry.php".$ext);
            exit();
            
            break;
        default:

            // Update temporary entry with form data
            save_entform_to_tmp();

            // Get the tag to search for from the value of the submit-button
            $call = explode("_", $_POST["submit"]);

            unset($_SESSION["entry"]["tags"][array_search($call[1], $_SESSION["entry"]["tags"])]);
            if($ent_idx===null){
                $ext = "";
            } else {
                $ext = "?ent=".$ent_idx;
            }
            header("Location: /dbit2/frm_entry.php".$ext);
            exit();
            break;
    }
             
    echo "Unhandled call: ".$_POST["submit"];
    echo "<br><pre>"; print_r($_GET);
    exit();
