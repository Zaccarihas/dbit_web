<?php


    // Init
    require "../controller/error_reporting.php";
    $db = require "../controller/dbconnect.php";
    session_start();

    // Testing
    //print_r($_GET);
    //echo "<b><h4>Session</h4><pre>"; print_r($_SESSION); echo "</pre>";
    //echo "<b><h4>POST</h4><pre>"; print_r($_POST); echo "</pre>";
    //die();

    // Show any registered alerts
    if(isset($_SESSION["alert"])){
        echo "<script>alert('".$_SESSION["alert"]."');</script>";
        unset($_SESSION["alert"]);
    }
    
    // Account references
    $sql = <<< SQL
        SELECT node_id, CONCAT(label, " - ", title) AS prompt 
        FROM nodes 
        WHERE type='Account'
        ORDER BY prompt ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $accs = $qry->fetchAll(PDO::FETCH_ASSOC);

    /*
    echo "<b><h4>Accounts</h4><pre>"; print_r($accs); echo "</pre>";
    */


    //if(isset($_SESSION["entry"]))
    $ent= $_SESSION["entry"];
    $ent_idx = isset($_GET["ent"])?$_GET["ent"]:null;
    $new_entry = $ent_idx === null;
    $fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Entry Account form</title>
        <link rel="stylesheet" href="view/ent_acc_form.css">
    <head>

    <body>
        <form id="frm_entry" method="post" action="../controller/ctrl_entry_account_pst.php<?php echo $ent_idx===null?"":"?ent=".$ent_idx ?>">
                       
            <div id="ent_id">
            <?php
                if($ent["entry_id"]===null){
                    echo "NEW";
                } 
                else {
                    $id_part1 = str_pad(intdiv($ent["entry_id"],1000),3,"0",STR_PAD_LEFT);
                    $id_part2 = str_pad($ent["entry_id"]%1000,3,"0",STR_PAD_LEFT);
                    printf("%s %s", $id_part1, $id_part2); 
                }
            ?>
            <input type="hidden" name="entry_id" value="<?php echo $ent["entry_id"] ?>">
            </div>

            <fieldset id="fldset_meta">
                
                <div class="formfield">
                    <label for="account">Account</label>
                    <select id="account" name="account">
                        <option value="">-- Välj ett konto --</option><?php
                        foreach($accs as $itm){
                            ?><option value="<?php echo $itm["node_id"]; ?>"<?php 
                                echo $ent["node_id"]==$itm["node_id"]?" selected":null ?>><?php
                                echo $itm["prompt"] ?></option><?php 
                        } ?>
                    </select>
                </div>

                <fieldset id="direction">
                    <label for="ent_direction_to">To</label>
                    <input type="radio" id="ent_direction_to" name="ent_direction" value="to"<?php
                        echo $ent["ent_direction"] == "to"?"checked":"";
                    ?>>
                    <label for="ent_direction_from">From</label>
                    <input type="radio" id="ent_direction_from" name="ent_direction" value="from"<?php
                        echo $ent["ent_direction"] == "from"?"checked":"";
                    ?>>>
                </fieldset>

                <div class="formfield">
                    <label for="total">Total</label>
                    <input type="text" id="total" name="total" value="<?php 
                        // echo $fmt->formatCurrency($ent["total"]??$_SESSION["transaction"]["difference_accounts"]??0,"SEK");
                        echo $fmt->formatCurrency($new_entry?$_SESSION["transaction"]["difference_accounts"]:$ent["total"],"SEK");

                    ?>" placeholder="Belopp" required/>
                </div>

                <div class="formfield">
                    <label for="status">Status</label>
                    <input type="text" id="status" name="status" value="<?php 
                        echo $ent["status"];
                    ?>" placeholder="Status"/>
                </div>

                <div class="formfield">
                    <label for="method">Method</label>
                    <input type="text" id="method" name="method" value="<?php 
                        echo $ent["method"];
                    ?>" placeholder="Överföringsmetod"/>
                </div>

           
            </fieldset>

            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="btn_register" name="submit" value="register">Register</button>
                <?php if (!$new_entry) { ?>
                    <button 
                        class="formbuttons" name="submit" id="btn_delete" value="delete" type="submit" onclick="deleteEntry(<?php echo $ent["entry_id"] ?>)">Delete</button>              
                <?php } ?>
                <button class="formbuttons" type="submit" name="submit" id="btn_cancel" value="cancel" formnovalidate>Cancel</button>
            </fieldset>

        </form>

        <script>
            function deleteEntry(entry){

                const frm = document.getElementById("frm_entry");
                const confirmed = window.confirm("Är du säker på att du vill ta bort posten");         
                if(confirmed) { frm.submit() }
                
            }
        </script>

    </body>

</html>