<?php

    require "error_reporting.php";

    // Testing
    /*
    echo "<b><h4>Inkommande GET</h4><pre>"; print_r($_GET); echo "</pre>";
    require "error_reporting.php";
    */
    
    // Init
    require "dbconnect.php";
    session_start();

    // -----------------------
    // get_entries
    //
    // Fill the temporary transaction with specified entries from database  */
    function get_transaction_tags($trn_id){

        global $db;

        $sql = <<< SQL
            SELECT tag FROM trn_tags WHERE transaction = :id
        SQL;
        $qry = $db->prepare($sql);
        $qry->execute([':id' => $trn_id]);
        $_SESSION["transaction"]["tags"] = $qry->fetchAll(PDO::FETCH_COLUMN);

    }

    // -----------------------
    // get_entries
    //
    // Fill the temporary transaction with specified entries from database  */
    function get_entries($trn_id, $type){

        global $db;
       
        $ent_meta = [
            "accounts" => ['tab' => "ent_accounts", 'view' => "vw_ent_accounts"],
            "associates" => ['tab' => "ent_associates", 'view' => "vw_ent_associates"],
            "depots" => ['tab' => "ent_depots", 'view' => "vw_ent_depots"],
            "categories" => ['tab' => "ent_categories", 'view' => "vw_ent_categories"]
        ];




        $_SESSION["transaction"][$ent_meta[$type]['tab']] = [];

        $sql = "SELECT * FROM ".$ent_meta[$type]['view']." WHERE transaction_id = :id";
        $qry = $db->prepare($sql);
        $qry->execute([
            ':id'   => $trn_id
        ]);
 
        $_SESSION["transaction"][$ent_meta[$type]["tab"]] = $qry->fetchAll(PDO::FETCH_ASSOC);

        $total = "total_".$type;
        foreach($_SESSION["transaction"][$ent_meta[$type]["tab"]] as $idx => $ent) {

            // Add to total
            $_SESSION["transaction"][$total] += ($ent["ent_direction"]=="from"?-1:1) * $ent["total"];

            // Create the prompt for the entries
            if($type == "associates") {
                $_SESSION["transaction"]["ent_associates"][$idx]["prompt"] = $ent["title"].($ent["location"] !== null?", ".$ent["location"]:"");
                unset($_SESSION["transaction"]["ent_accounts"][$idx]["location"]);
                unset($_SESSION["transaction"]["ent_accounts"][$idx]["title"]);
            } else {
                $_SESSION["transaction"][$ent_meta[$type]["tab"]][$idx]["prompt"] = $ent["label"]." - ".$ent["title"];
                unset($_SESSION["transaction"]["ent_accounts"][$idx]["label"]);
                unset($_SESSION["transaction"]["ent_accounts"][$idx]["title"]);
            }
        }
        $_SESSION["difference_".$type] = $_SESSION["transaction"]["total_target"] - abs($_SESSION["transaction"][$total]);
        

        // If the type is categories then fetch related tags
        if ($type == 'categories') {
            $sql = <<< SQL
                SELECT * FROM entrytag
                WHERE entry = :id
            SQL;
            $qry = $db->prepare($sql);
            $qry->bindParam(':id', $ent_id);
            foreach($_SESSION["transaction"][$ent_meta[$type]['tab']] as $idx => $entry) {
                $ent_id = $entry["entry_id"];
                $qry->execute();
                $res = $qry->fetchAll(PDO::FETCH_ASSOC);
                $_SESSION["transaction"][$ent_meta[$type]['tab']][$idx]["tags"]=array_column($res,"tag");
            }
        }

    }

    // Initiate the session with empty values for transatcion
    $_SESSION["transaction"] = [
        "transaction_id" => null,
        "subject" => null,
        "transaction_date" => null,
        "total_target" => 0,
        "total_accounts" => 0,
        "total_associates" => 0,
        "total_depots" => 0,
        "total_categories" => 0,
        "difference_accounts" => 0,
        "difference_associates" => 0,
        "difference_depots" => 0,
        "difference_categories" => 0,
        "trn_note" => null,
        "tags" => []
    ];
    
    // If a specific transaction been requested, fill the session with relevant data

    if($_GET["id"]){
       
        // Get the current transaction information
        $qry = $db->prepare("SELECT * FROM transactions WHERE transaction_id=:id");
        $qry->bindValue(":id", $_GET["id"]);
        $qry->execute();
        $current_values = $qry->fetchAll(PDO::FETCH_ASSOC)[0];

        $_SESSION["transaction"]["transaction_id"] = $current_values["transaction_id"];
        $_SESSION["transaction"]["subject"] = $current_values["subject"];
        $_SESSION["transaction"]["transaction_date"] = $current_values["transaction_date"];
        $_SESSION["transaction"]["total_target"] = $current_values["target_total"];
        $_SESSION["transaction"]["trn_note"] = $current_values["trn_note"];

        // Get related tags
        get_transaction_tags($_GET["id"]);
        
        // Fetch account entries
        get_entries($_GET["id"], "accounts");
        get_entries($_GET["id"], "associates");
        get_entries($_GET["id"], "depots");
        get_entries($_GET["id"], "categories");
            


        /*
        if(!empty($entry_ids)){
            $placeholders = implode(',',array_fill(0, count($entry_ids), '?'));

            // Fetch related tags

        
            $qry = $db->prepare($sql);
            //$qry->bindValue(":id", implode(", ",array_keys($_SESSION["entries"])));
            $qry->execute($entry_ids);
            $_SESSION["tags"] = $qry->fetchAll(PDO::FETCH_ASSOC);
        }
        */

    };


    // Testing
    //echo "<br>Temporary Transaction:<pre>"; print_r($_SESSION["transaction"]); echo "</pre>";
            
    header("Location: /dbit2/view/frm_transaction.php");
    die();
