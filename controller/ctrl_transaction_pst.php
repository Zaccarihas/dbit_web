<?php
    // Init
    require "error_reporting.php";
    require "dbconnect.php";
    session_start();

    // -----------------------
    // save_trnform_to_tmp
    //
    // Update the data of the temporary storage with the data provided through the form */
    // -----------------------
    function save_trnform_to_tmp() {

        $formatter = new NumberFormatter('sv_SE', NumberFormatter::DECIMAL);

        $_SESSION["transaction"]["transaction_id"] = $_POST["transaction_id"];
        $_SESSION["transaction"]["subject"] = $_POST["subject"];
        $_SESSION["transaction"]["transaction_date"] = $_POST["when"];
        $_SESSION["transaction"]["total_target"] = $formatter->parse($_POST["total_target"]);
        $_SESSION["transaction"]["trn_note"] = $_POST["trn_note"];

    }

    // -----------------------
    // register_transaction_tags
    //
    // Synchronize the tags for a transaction between the database and the temporary storage in SESSION for a given transaction.
    // -----------------------
    function register_transaction_tags($trn, $trn_id, $new_trn){

        global $db;

        // echo "<h2>Register transaction tags</h2>";
        // echo "<br>Trn ID: ".$trn_id;
        // echo "<br>New trn: ".$new_trn;
        // echo "<br>Transaction: <pre>"; print_r($trn); echo "</pre>";

        //$trn_id = $trn["transaction_id"];
        $existing_tags = [];
        if (!$new_trn) {
            
            // Get all existing tags for the transaction
            $sql = <<< SQL
                SELECT tag FROM trn_tags WHERE transaction = :id
            SQL;
            $qry = $db->prepare($sql);
            $qry->bindValue(':id', $trn_id);
            
            try{
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }

            $existing_tags = $qry->fetchAll(PDO::FETCH_COLUMN);

            //echo "<br>Existing tags: <pre>"; print_r($existing_tags); echo "</pre>";
        }
        
        // Add new tags
        if(isset($trn["tags"]) && !empty($trn["tags"])) {
            $add_tags = array_diff($trn["tags"], $existing_tags);

            //echo "<br>New tags:<pre>"; print_r($add_tags); echo "</pre>";
        
            $sql = <<< SQL
                INSERT INTO trn_tags (transaction, tag)
                VALUES (:trn, :tag)
            SQL;

            foreach ($add_tags as $tag){
                $qry = $db->prepare($sql);
                $qry->bindValue(':trn', $trn_id);
                $qry->bindValue(':tag', $tag);

                try {
                    $qry->execute();
                } catch(PDOException $e) {
                    echo "DB Error: ".$e->getMessage();
                    exit();
                }
            }
        }

        //echo "<br>New tags added";
        

        // Remove deleted tags
        if(isset($trn["tags"]) && !empty($trn["tags"])) {
            $rem_tags = array_diff($existing_tags, $trn["tags"]);
        } else {
            $rem_tags = $existing_tags;
        }
        
        $sql = <<< SQL
            DELETE FROM trn_tags
            WHERE transaction = :trn AND tag = :tag
        SQL;
            
        foreach ($rem_tags as $tag){
            $qry = $db->prepare($sql);
            $qry->bindValue(':trn', $trn_id);
            $qry->bindValue(':tag', $tag);

            try {
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }
        }
    }

    // -----------------------
    // register_entry_tags
    //
    // Synchronize the tags for an entry between the database and the temporary storage in SESSION for a given entry.
    // -----------------------
    function register_entry_tags($ent, $new_ent){

        global $db;

        //echo "<br><h2>Register Entry Tags</h2><hr><br>Registering entry tags<pre>"; print_r($ent); echo "</pre>";

        $ent_id = $ent["entry_id"];
        $existing_tags = [];
        if (!$new_ent) {
            
            // Get all existing tags for the entry
            $sql = <<< SQL
                SELECT tag FROM entrytag WHERE entry = :id
            SQL;
            $qry = $db->prepare($sql);
            $qry->bindValue(':id', $ent_id);
            
            try{
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }

            $existing_tags = $qry->fetchAll(PDO::FETCH_COLUMN);
        }

        //echo "<br>Existing tags<pre>"; print_r($existing_tags); echo "</pre>";
        
        // Add new tags
        if(isset($ent["tags"]) && !empty($ent["tags"])) {

            $add_tags = array_diff($ent["tags"], $existing_tags);
            //echo "<br>New tags<pre>"; print_r($add_tags); echo "</pre>";

        
        
            $sql = <<< SQL
                INSERT INTO entrytag(entry, tag)
                VALUES (:entry, :tag)
            SQL;

            foreach ($add_tags as $tag){
                $qry = $db->prepare($sql);
                $qry->bindValue(':entry', $ent_id);
                $qry->bindValue(':tag', $tag);

                try {
                    $qry->execute();
                } catch(PDOException $e) {
                    echo "DB Error: ".$e->getMessage();
                    exit();
                }
            }
        }

        // Remove deleted tags
        if(isset($trn["tags"]) && !empty($trn["tags"])) {
            $rem_tags = array_diff($existing_tags, $ent["tags"]);
        } else {
            $rem_tags = $existing_tags;
        }
        
        $sql = <<< SQL
            DELETE FROM entrytag
            WHERE entry = :entry AND tag = :tag
        SQL;
            
        foreach ($rem_tags as $tag){
            $qry = $db->prepare($sql);
            $qry->bindValue(':entry', $ent_id);
            $qry->bindValue(':tag', $tag);

            try {
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }
        }
    }

    // -----------------------
    // register_transaction_entry
    //
    // Register data for a given entry in the database
    // -----------------------
    function register_transaction_entry($trn_id, $ent, $ent_type){

        $dbg = false;
        $dbg_acc = false;
        $dbg_asc = false;
        $dbg_dep = false;
        $dbg_cat = false;

    
        global $db;

        // Convert any empty strings in entry to null


        if($dbg) {
            echo "<h2>Register Transaction Entry</h2>";
            echo "<br>Transation: ".$trn_id;
            echo "<br>Entry type: ".$ent_type;
            echo "<br>Entry for registration:<pre>"; print_r($ent); echo"</pre>";
        }
        
        $ent_id = $ent["entry_id"];
        $new_ent = $ent_id === null;
        
        // Create query
        if($new_ent) {
            $sql = <<< SQL
            INSERT INTO entries(
                transaction_id,
                node,
                total,
                ent_domain,
                ent_direction)
            VALUES (
                :transaction_id, 
                :node, 
                :total, 
                :ent_domain,
                :ent_direction)
            SQL;
        }
        else {
            $sql = <<< SQL
            UPDATE entries
            SET 
                transaction_id = :transaction_id, 
                node = :node, 
                total = :total,
                ent_domain = :ent_domain,
                ent_direction = :ent_direction
            WHERE entry_id = :id
            SQL;
        }

        if($dbg) {
            echo "<br>SQL: ".$sql;
        }

        $qry = $db->prepare($sql);
        $qry->bindValue(':transaction_id', $trn_id);
        $qry->bindValue(':node', $ent["node_id"]);
        $qry->bindValue(':total', $ent["total"]);
        $qry->bindValue(':ent_domain', $ent["ent_domain"]);
        $qry->bindValue(':ent_direction', $ent["ent_direction"]);
        if(!$new_ent) {
            $qry->bindValue(':id', (int)$ent["entry_id"]);
        }

        // Update database

        try{
            $qry->execute();
        } catch(PDOException $e) {
            echo "DB Error: ".$e->getMessage();
            exit();
        }

        if($dbg) {
            echo "<br>Entry updated or created<br>";
        }
        

        // Retrive id for entry if new entry is created.
        if($new_ent) {
            $ent_id = $db->lastInsertId();
            $_SESSION["entry"]["entry_id"] = $ent_id;
            $ent["entry_id"] = $ent_id;
        }

        if($dbg) {
            echo "<br>Entry: ".$ent_id;
        }
        
        // Add entry type specific fields
        switch ($ent_type) {
            case "account":

                
                if($dbg) {
                   echo "<br>Account Entry Specifics<br>";
                }
                
                // Check if specific fields are needed
                if ($ent["method"] !== null || $ent["status"] !== null) {
                    if($new_ent) {
                        $sql = <<< SQL
                            INSERT INTO ents_account(
                                entry_id,
                                transfer_method,
                                status)
                            VALUES (
                                :entry_id,
                                :transfer_method, 
                                :status)
                        SQL;
                    }
                    else {
                        $sql = <<< SQL
                            UPDATE ents_account
                            SET 
                                transfer_method = :transfer_method, 
                                status = :status
                            WHERE entry_id = :entry_id
                        SQL;
                    }

                    if($dbg_acc) {
                        echo "<br>sql:<br>".$sql;
                        echo "<br>method: ".$ent["method"];
                        echo "<br>status: ".$ent["status"];
                        echo "<br>entry: ".$ent_id;
                    }
                   

                    $qry = $db->prepare($sql);
                    $qry->bindValue(':transfer_method', $ent["method"]);
                    $qry->bindValue(':status', $ent["status"]);
                    $qry->bindValue(':entry_id', $ent_id);

                    try{
                        $qry->execute();
                    } catch(PDOException $e) {
                        echo "DB Error: ".$e->getMessage();
                        exit();
                    }

                    if($dbg) {
                        echo "<br>Account successfully registered";
                    }
                    
                }

                
                break;
            case "assoc":

                if ($dbg) {
                    echo "<br>Associate Entry Specifics<br>";
                }
                if($dbg) {
                    echo "<br>Associate successfully registered";
                }
                               
                break;
            case "category":

                if ($dbg) {
                    echo "<br>Category Entry Specifics<br>";
                }

                // Convert any empty strings to null
                $ent["quantity"] = $ent["quantity"]==""?null:$ent["quantity"];
                $ent["unit"] = $ent["unit"]==""?null:$ent["unit"];
                $ent["product"] = $ent["product"]==""?null:$ent["product"];
                $ent["ent_note"] = $ent["ent_note"]==""?null:$ent["ent_note"];
                
                 // Check if specific fields are needed
                if ($ent["quantity"] !== null || $ent["unit"] !== null || $ent["product"] !== null || $ent["ent_note"] !== null) {
                    
                    if($dbg_cat) {
                        echo "<br>Specific values identified";
                        echo "<br>Quantiy: ".var_dump($ent["quantity"]);
                        echo "<br>Unit: ".var_dump($ent["unit"]);
                        echo "<br>Product: ".var_dump($ent["product"]);
                        echo "<br>Note: ".var_dump($ent["ent_note"]);
                    }
                    if($new_ent) {
                        $sql = <<< SQL
                            INSERT INTO ents_category(
                                entry_id,
                                quantity,
                                unit,
                                product,
                                entry_note)
                            VALUES (
                                :entry_id,
                                :quantity, 
                                :unit,
                                :product,
                                :entry_note)
                        SQL;
                    }
                    else {
                        $sql = <<< SQL
                            UPDATE ents_category
                            SET 
                                quantity = :quantity, 
                                unit = :unit,
                                product = :product,
                                entry_note = :entry_note
                            WHERE entry_id = :entry_id
                        SQL;
                    }

                    $formatter = new NumberFormatter('sv_SE', NumberFormatter::DECIMAL);

                    if($dbg_cat){
                        echo "<br>SQL: ".$sql;
                    }
                    

                    $qry = $db->prepare($sql);
                    $qry->bindValue(':quantity', $ent["quantity"]===null?null:$formatter->parse($ent["quantity"]));
                    $qry->bindValue(':unit', $ent["unit"]);
                    $qry->bindValue(':product', $ent["product"]);
                    $qry->bindValue(':entry_note', $ent["ent_note"]);
                    $qry->bindValue(':entry_id', $ent_id);

                    try{
                        $qry->execute();
                    } catch(PDOException $e) {
                        echo "DB Error: ".$e->getMessage();
                        exit();
                    }
                }

                if($dbg) {
                    echo "<br>Entry specifics has been registered!";
                }                

                // Register any tags
                register_entry_tags($ent, $new_ent);

                break;
        }

        return $ent_id;
    }
    
    // -----------------------
    // register_transaction
    //
    // Register the transaction temporarly stored in SESSION, to the database */
    // -----------------------
    function register_transaction(){

        $dbg = false;
        global $db;

        // TRANSACTION
        // Check if it is a new transaction or an existing one
        $trn_id = $_SESSION["transaction"]["transaction_id"];
        $new_trn = $trn_id === null;



        if ($dbg) { echo "<br>Transaction id: ".$trn_id."<br><hr>"; }
        
        // Create query
        if($new_trn) {
            $sql = <<< SQL
            INSERT INTO transactions(subject, transaction_date, target_total, trn_note)
            VALUES (:subject, :transaction_date, :target_total, :trn_note)
            SQL;
        }
        else {
            $sql = <<< SQL
            UPDATE transactions
            SET 
                subject = :subject, 
                transaction_date = :transaction_date, 
                target_total = :target_total, 
                trn_note = :trn_note
            WHERE transaction_id = :id
            SQL;
        }

        if($dbg){
            echo "<br>SQL: ".$sql;
            //die();
        }

        $qry = $db->prepare($sql);
        $qry->bindValue(':subject', $_SESSION["transaction"]["subject"]);
        $qry->bindValue(':transaction_date', $_SESSION["transaction"]["transaction_date"]);
        $qry->bindValue(':target_total', $_SESSION["transaction"]["total_target"]);
        $qry->bindValue(':trn_note', $_SESSION["transaction"]["trn_note"]);
        if(!$new_trn) {
            $qry->bindValue(':id', $_SESSION["transaction"]["transaction_id"]);
        }

        // Update database

        try{
            $qry->execute();
        } catch(PDOException $e) {
            echo "DB Error: ".$e->getMessage();
            exit();
        }

        if ($dbg) {
            echo "<br>Transaction updated or created<br>";
        }

        // Retrive id for transaction if new transaction
        if($new_trn) {
            $trn_id = $db->lastInsertId();
        }

        if ($dbg) {
            echo "<br>Transaction id: ".$trn_id;
        }

        // Register Transaction Tags
        register_transaction_tags($_SESSION["transaction"], $trn_id, $new_trn);

        if ($dbg) {
            echo "<br>Transaction Tags registered";
        }

        // Accounts
        foreach($_SESSION["transaction"]["ent_accounts"] as $ent) {           
            register_transaction_entry($trn_id, $ent, "account");
        } 

        if ($dbg) {
            echo "<br>All accounts registered<br>";
        }
        
        
        // Associates
        foreach($_SESSION["transaction"]["ent_associates"] as $ent) {           
            register_transaction_entry($trn_id, $ent, "associate");
        } 

        if ($dbg) {
            echo "<br>All associates registered<br>";
        }

        // Depots
        foreach($_SESSION["transaction"]["ent_depots"] as $ent) {           
            register_transaction_entry($trn_id, $ent, "depot");
        } 

        if ($dbg) {
            echo "<br>All depots registered";
        }

        // Categories
        foreach($_SESSION["transaction"]["ent_categories"] as $ent) {           
            register_transaction_entry($trn_id, $ent, "category");
        } 

        if ($dbg) {
            echo "<br>All categories registered";
            die();
        }
    }

    // -----------------------
    // delete_transaction
    //
    // Remove the transaction represented in SESSION permanently from the database */
    // -----------------------
    function delete_transaction(){

        global $db;

        $trn_id = $_SESSION["transaction"]["transaction_id"];

        // Retrive affected entries
        $sql = <<< SQL
            SELECT entry_id FROM entries WHERE transaction_id = :transaction_id
        SQL;
        $qry = $db->prepare($sql);
        $qry->bindValue(":transaction_id", $trn_id);
        
        try {
            $qry->execute();
        } catch(PDOException $e) {
            echo "DB Error: ".$e->getMessage();
            exit();
        }

        $entries = array_column($qry->fetchAll(PDO::FETCH_ASSOC),"entry_id");

        //echo "<br>Entries<pre>"; print_r($entries); echo"</pre>";

        // Delete related entry tags
        $sql = <<< SQL
            DELETE FROM entrytag WHERE entry = :entry
        SQL;
        foreach($entries as $ent_id) {
            $qry = $db->prepare($sql);
            $qry->bindValue(":entry", $ent_id);

            try {
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }
        }

        //echo "<br>Tags deleted";

        // Delete entry specifics
        $sql_acc = <<< SQL
            DELETE FROM ents_account WHERE entry_id = :entry_id
        SQL;
        $sql_cat = <<< SQL
            DELETE FROM ents_category WHERE entry_id = :entry_id
        SQL;
        $sql_asc = <<< SQL
            DELETE FROM ents_associate WHERE entry_id = :entry_id
        SQL;

        foreach($entries as $ent_id) {
            $qry = $db->prepare($sql_acc);
            $qry->bindValue(":entry_id", $ent_id);

            try {
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }

           //echo "<br>acc spec deleted";

            $qry = $db->prepare($sql_asc);
            $qry->bindValue(":entry_id", $ent_id);

            try {
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }

            //echo "<br>associates specifics deleted";

            $qry = $db->prepare($sql_cat);
            $qry->bindValue(":entry_id", $ent_id);

            try {
                $qry->execute();
            } catch(PDOException $e) {
                echo "DB Error: ".$e->getMessage();
                exit();
            }
            //echo "<br>categories specifics deleted";

        }

        // Delete related entries
        $sql = <<< SQL
            DELETE FROM entries WHERE transaction_id = :trn_id
        SQL;
        $qry = $db->prepare($sql);
        $qry->bindValue(":trn_id", $trn_id);

        try {
            $qry->execute();
        } catch(PDOException $e) {
            echo "DB Error: ".$e->getMessage();
            exit();
        }

        //echo "<br>Entries deleted";

        // Delete related transaction tags
        $sql = <<< SQL
            DELETE FROM trn_tags WHERE transaction = :trn_id
        SQL;
        $qry = $db->prepare($sql);
        $qry->bindValue(":trn_id", $trn_id);

        try {
            $qry->execute();
        } catch(PDOException $e) {
            echo "DB Error: ".$e->getMessage();
            exit();
        }

        // Delete transaction
        $sql = <<< SQL
            DELETE FROM transactions WHERE transaction_id = :trn_id
        SQL;
        $qry = $db->prepare($sql);
        $qry->bindValue(":trn_id", $trn_id);

        try {
            $qry->execute();
        } catch(PDOException $e) {
            echo "DB Error: ".$e->getMessage();
            exit();
        }
        //echo "<br>Transaction deleted";
    }

    
    // -----------------------
    // prepare_temporary_entry
    //
    // Prepare a temporary entry in SESSION  */
    // -----------------------
    function prepare_temporary_entry($ent_type){

        // Set common attributes
        $_SESSION["entry"] = [
            "transaction_id" => null,
            "entry_id" => null,
            "ent_domain" => in_array($ent_type, ["account", "assoc"])?"balance":"result",
            "ent_direction" => null,
            "node_id" => null,
            "total" => 0,
            "ent_note" => null
        ];

        // Set entry type specific attributes
        switch ($ent_type) {
            case 'account':
                $_SESSION["entry"]["status"] = null;
                $_SESSION["entry"]["method"] = null;
                break;
            case 'category':
                $_SESSION["entry"]["quantity"] = null;
                $_SESSION["entry"]["unit"] = null;
                $_SESSION["entry"]["product"] = null;
                $_SESSION["entry"]["tags"] = [];
                break;
        }

    }

    //echo "<br/>POST:<br/><pre>"; print_r($_POST); echo "</pre>";


    // Replace empty strings with null
    $_POST = array_map(function ($value) {
        return $value === "" ? null : $value;
    }, $_POST);

    // Save the current form data into the temporary transaction in SESSION
    save_trnform_to_tmp();

    $view_folder = "/dbit2/view/";
    $return_location = "lst_transactions.php";

    //$new_trn = $_POST["recon_id"] === null;

    // Route
    switch ($_POST["submit"]){

        case 'cancel':

            // Remove temporary transaction
            unset($_SESSION["transaction"]);

            // Return to transaction list
            header("Location: ".$view_folder.$return_location);
            exit();    
            
            break;
        
        case 'register':
            
            register_transaction();
            
            // Remove the temporary transaction
            unset($_SESSION["transaction"]);

            // Return to transaction list
            header("Location: ".$view_folder.$return_location);
            exit();
            
            break;
            
        case 'delete':

            delete_transaction();

            // Remove the temporary transaction
            unset($_SESSION["transaction"]);

            // Return to transaction list
            header("Location: ".$view_folder.$return_location);
            exit();
            break;
                   
        
        case 'add_account':
        case 'add_assoc':
        case 'add_depot':
        case 'add_category':

            $entry_type = ltrim(strstr($_POST["submit"],"_"),"_");
            prepare_temporary_entry($entry_type);

            // Open Account form
            header("Location: ".$view_folder."frm_ent_".$entry_type.".php");
            exit();
            
            break;

        case "add_tag":
                       
            // Add the tag if the tag is not already in the list and the tag is not null
            if($_POST["tag"] !== null && !in_array($_POST["tag"], $_SESSION["transaction"]["tags"])) {
                $_SESSION["transaction"]["tags"][] = $_POST["tag"];
            }

            header("Location: ".$view_folder."frm_transaction.php");
            exit();
            
            break;

        default:

            // Keep forminput in temporary transaction
            save_trnform_to_tmp();

            // Check which button that's been used
            $call = explode("_", $_POST["submit"]);

            //echo "<br>POST:<pre>"; print_r($_POST); echo "</pre>";
            //echo "<br>Tag or entry button:<pre>"; print_r($call); echo "</pre>";
            //die();

            // Check if a tag has been pressed
            if ($call[0] == "tag"){

                // Get tag-value
                array_splice($call, 0, 1);
                $tag = implode("_", $call);

                // Remove the tag
                $idx = array_search($tag, $_SESSION["transaction"]["tags"], true);
                if($idx !== false) {
                    array_splice($_SESSION["transaction"]["tags"], $idx, 1);
                }

                header("Location: ".$view_folder."frm_transaction.php");
                exit();
            } else {

                // Copy the requested entry to a temporary entry
                $entrytabs = [
                    "account" => "ent_accounts",
                    "assoc" => "ent_associates",
                    "depot" => "ent_depots",
                    "category" => "ent_categories"
                ];

                $_SESSION["entry"] = $_SESSION["transaction"][$entrytabs[$call[1]]][$call[2]];

                //echo "<br>Call:<pre>"; print_r($call); echo "</pre>";

                header("Location: ".$view_folder."frm_ent_".$call[1].".php?ent=".$call[2]);
                exit();
            }


            break;
    }

    // Give fail message if controller is unabled to handle the request.        
    //echo "<h2>Unhandled call</h2>";
    //echo "<br><pre>"; print_r($_GET); echo "</pre>";
    //echo "<br><pre>"; print_r($_POST); echo "</pre>";
    
    exit();
