<?php
    
    // Development    
    //require "controller/error_reporting.php";
        
    // Init
    session_start();
    require "dbconnect.php";

    // Testing
    //echo "<b><h4>Session</h4><pre>"; print_r($_SESSION); echo "</pre>";

    // nodes references
    $sql = <<< SQL
        SELECT * FROM nodes ORDER BY label
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $res = $qry->fetchAll(PDO::FETCH_ASSOC);
    $nodes = [];
    foreach($res as $ent){
        $idx = $ent["node_id"];
        $nodes[$idx] = [
            "prompt" => $ent["type"]=="Associate"?$ent["title"]:$ent["label"]." - ".$ent["title"],
            "type" => $ent["type"]
        ];
    }

    // Associate references
    $sql = <<< SQL
        SELECT node_id, title AS prompt 
        FROM nodes 
        WHERE type='Associate'
        ORDER BY prompt ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $ascs = $qry->fetchAll(PDO::FETCH_ASSOC);

    $trn = $_SESSION["transaction"];
    $new = $trn["transaction_id"] === null;
    $fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

?>
<html>
    <head>
        <title>Transaction form</title>
        <link rel="stylesheet" href="view/trn_form.css">
    <head>

    <body>
        <form id="frm_transaction" method="post" action="controller/transaction_pst.php">
            
            <fieldset id="fldset_desc"> 
                
            <div id="transaction_id">
                <?php
                    if($new){
                        echo "NEW";
                    } 
                    else {
                        $id_part1 = str_pad(intdiv($trn["transaction_id"],1000),3,"0",STR_PAD_LEFT);
                        $id_part2 = str_pad($trn["transaction_id"]%1000,3,"0",STR_PAD_LEFT);
                        printf("%s %s", $id_part1, $id_part2); 
                    }
                ?>
                <input type="hidden" name="transaction_id" value="<?php echo $new?null:$trn["transaction_id"]?>">
                </div>

                <div class="formfield">
                    <label for="when">Date and time</label>
                    <input type="text" id="when" name="when" value="<?php 
                        echo $trn["transaction_date"]
                    ?>" placeholder="Tidpunkt för transaktionen" required/>
                </div>
                
                <div class="formfield">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="<?php 
                        echo $trn["subject"]
                    ?>" placeholder="Rubrik" required/>
                </div>

                <div class="formfield">
                    <label for="total">Total</label>
                    <input type="text" id="total" name="total" value="<?php 
                        echo $fmt->formatCurrency($trn["total"]??0,"SEK")
                    ?>" placeholder="Total"/>
                </div>

                <div class="formfield">
                    <label for="default_associate">Default Associate</label>
                    <select id="default_associate" name="default_associate">
                        <option value=""<?php echo $trn["default_associate"]===null?"selected":"" ?>>Välj förvald aktör</option>
                        <?php 
                        foreach($nodes as $idx => $ent){ 
                            if($ent["type"] == "Associate") {?>
                            <option 
                                value="<?php echo $idx ?>" <?php 
                                    echo $trn["default_associate"] == $idx?"selected":"" 
                                ?>><?php echo $ent["prompt"] ?></option>
                        <?php }} ?>
                    </select>
                </div>
               
                <div class="formfield">
                    <label for="note">Note</label>
                    <textarea cols="30" rows="5" id="note" name="note" placeholder="Anteckning"><?php
                        echo $trn["note"];
                    ?></textarea>
                </div>

            </fieldset>

            <fieldset id="fldset_entries">
                <div id="entry_status">
                    <button type="submit" id="btn_newent" name="submit" value="add_ent">Add Entry</button>
                </div>
                <table id="entry_list">
                    <thead>
                        <tr><th>Note</th><th>Status</th><th>Balance out</th><th>Balance in</th><th>Result out</th><th>Result in</th><th>Total</th><th>Ctrl</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['transaction']['entries'] as $idx => $ent){ ?>
                        <tr id="<?php echo $idx ?>">
                            <td><?php echo $ent["note"] ?></a></td>
                            <td><?php echo $ent["status"] ?></td>
                            <td><?php echo $nodes[$ent["credit_balance"]]["prompt"] ?></td>
                            <td><?php echo $nodes[$ent["debet_balance"]]["prompt"] ?></td>
                            <td><?php echo $nodes[$ent["credit_result"]]["prompt"] ?></td>
                            <td><?php echo $nodes[$ent["debet_result"]]["prompt"] ?></td>
                            <td><?php echo $fmt->formatCurrency($ent["amount"]??0,"SEK");?></td>
                            <td><button type="submit" name="submit" value="openent_<?php echo $idx ?>">Open</button></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            
            </fieldset>

            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="btn_register" name="submit" value="register">Register</button>
                <?php
                    if(!$new){
                        ?>
                        <button class="formbuttons" type="submit" id="btn_delete" name="submit" value="delete" onclick="return delete_acc()">Delete</button>
                        <?php
                    } 
                ?>                
                <button class="formbuttons" type="submit" id="btn_cancel" name="submit" value="cancel" formnovalidate>Cancel</button>
            </fieldset>

        </form>

    <script>
        function delete_acc(){

            const frm = document.getElementById("frm_transaction");
            const confirmed = window.confirm("Är du säker på att du vill ta bort transaktionen");
            
            if(confirmed) {
                frm.submit();
            } else { return false; }
            
        }
    </script>

    </body>

</html>