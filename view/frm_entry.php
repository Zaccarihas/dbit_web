<?php


    // Init
    $db = require "dbconnect.php";
    session_start();

    // Testing
    //print_r($_GET);
    //require "controller/error_reporting.php";
    //echo "<b><h4>Session</h4><pre>"; print_r($_SESSION); echo "</pre>";

    // Show any registered alerts
    if(isset($_SESSION["alert"])){
        echo "<script>alert('".$_SESSION["alert"]."');</script>";
        unset($_SESSION["alert"]);
    }

    // nodes references
    $sql = <<< SQL
        SELECT * FROM nodes ORDER BY label ASC, title ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $res = $qry->fetchAll(PDO::FETCH_ASSOC);
    $nodes = [];
    foreach($res as $itm){
        $idx = $itm["node_id"];
        $nodes[$idx] = [
            "prompt" => $itm["type"]=="Associate"?$itm["title"]:$itm["label"]." - ".$itm["title"],
            "type" => $itm["type"]
        ];
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

    // Depots references
    $sql = <<< SQL
        SELECT node_id, CONCAT(label, " - ", title) AS prompt 
        FROM nodes 
        WHERE type='Depot'
        ORDER BY prompt ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $deps = $qry->fetchAll(PDO::FETCH_ASSOC);

    
    // Income Categories references
    $sql = <<< SQL
        SELECT node_id, CONCAT(label, " - ", title) AS prompt 
        FROM nodes 
        WHERE type='Category' AND type_group='inc'
        ORDER BY prompt ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $catincs = $qry->fetchAll(PDO::FETCH_ASSOC);

    // Expense Categories references
    $sql = <<< SQL
        SELECT node_id, CONCAT(label, " - ", title) AS prompt 
        FROM nodes 
        WHERE type='Category' AND type_group='exp'
        ORDER BY prompt ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $catexps = $qry->fetchAll(PDO::FETCH_ASSOC);

    $ent = $_SESSION["entry"];
    $ent_idx = isset($_GET["ent"])?$_GET["ent"]:null;
    $fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Entry form</title>
        <link rel="stylesheet" href="view/ent_form.css">
    <head>

    <body>
        <form id="frm_entry" method="post" action="controller/entry_pst.php<?php echo $ent_idx===null?"":"?ent=".$ent_idx ?>">
        
        <fieldset id="fldset_description"> 
                
                <div id="entry_id">
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

                <div class="formfield">
                    <label for="note">Note</label>
                    <textarea rows = 30 cols=15 id="note" name="note"><?php 
                        echo $ent["note"];
                    ?></textarea>
                </div>
            </fieldset>

            <fieldset id="fldset_accounting">
                
                <div class="formfield">
                        <label for="entry_type">Entry type</label>
                        <select id="entry_type" name="entry_type">
                            <option value="" <?php echo $ent["entry_type"]===null?"selected":"" ?>>Välj typ av post</option>
                            <option value="exp" <?php echo $ent["entry_type"] == "exp"?"selected":""?>>Expense</option>
                            <option value="inc" <?php echo $ent["entry_type"] == "inc"?"selected":""?>>Income</option>
                            <option value="trn" <?php echo $ent["entry_type"] == "trn"?"selected":""?>>Transfer</option>
                        </select>
                </div>

                <fieldset id="balance">
                    <legend>Balance</legend>
                                    
                    <div class="formfield">
                        <label for="balance_out">Out</label>
                        <select id="balance_out" name="balance_out"></select>
                    </div>

                    <div class="formfield">
                        <label for="balance_in">In</label>
                        <select id="balance_in" name="balance_in"></select>
                    </div>

                </fieldset>
                <fieldset id="result">
                    <legend>Result</legend>
                                    
                    <div class="formfield">
                        <label for="result_out">Out</label>
                        <select id="result_out" name="result_out"></select>
                    </div>

                    <div class="formfield">
                        <label for="result_in">In</label>
                        <select id="result_in" name="result_in"></select>
                    </div>
                </fieldset>
            </fieldset>

            <fieldset it="fldset_meta">

                <div class="formfield">
                    <label for="total">Total</label>
                    <input type="text" id="total" name="total" value="<?php 
                        echo $fmt->formatCurrency($ent["amount"]??0,"SEK");
                    ?>" placeholder="Belopp" required/>
                </div>

                <div class="formfield">
                    <label for="status">Status</label>
                    <input type="text" id="status" name="status" value="<?php 
                        echo $ent["status"];
                    ?>" placeholder="Status"/>
                </div>

                <div class="formfield">
                    <label for="transfer_method">Method</label>
                    <input type="text" id="transfer_method" name="transfer_method" value="<?php 
                        echo $ent["transfer_method"];
                    ?>" placeholder="Överföringsmetod"/>
                </div>

                <div class="formfield">
                    <label for="product">Product</label>
                    <input type="text" id="product" name="product" value="<?php 
                        echo $ent["product"];
                    ?>" placeholder="Produkt"/>
                </div>

                <div class="formfield">
                    <label for="quantity">Quantity</label>
                    <input type="text" id="quantity" name="quantity" value="<?php 
                        echo $ent["quantity"];
                    ?>" placeholder="Kvantitet"/>
                </div>

                <div class="formfield">
                    <label for="unit">Unit</label>
                    <input type="text" id="unit" name="unit" value="<?php 
                        echo $ent["unit"];
                    ?>" placeholder="Enhet"/>
                </div>

            </fieldset>

            <fieldset id="fldset_tags">
                <button type="submit" id="btn_add_tag" name="submit" value="add_tag" onclick="return addtag(<?php echo $ent["entry_id"] ?>)">Add tag</button>
                <?php foreach($ent["tags"] as $tag){ 
                    ?><button type="submit" name="submit" value="tag_<?php echo $tag ?>"><?php echo $tag; ?></button><?php } ?>
            </fieldset>

            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="btn_register" name="submit" value="register">Register</button>
                <button class="formbuttons" name="submit" id="btn_delete" value="delete" type="submit" onclick="deleteEntry(<?php echo $ent["entry_id"] ?>)">Delete</button>              
                <button class="formbuttons" type="submit" name="submit" id="btn_cancel" value="cancel" formnovalidate>Cancel</button>
            </fieldset>

        </form>

    <script>
        function deleteEntry(entry){

            //alert("Hit kom du");

            const frm = document.getElementById("frm_entry");
            const confirmed = window.confirm("Är du säker på att du vill ta bort posten");         
            if(confirmed) { frm.submit() }
            
        }

        function addtag(entry){

            const frm = document.getElementById("frm_entry");
            const tag = prompt("Ange ny tag: ", "");
            if (tag !== null){
                $ext = frm.action.includes("?")?"&":"?";
                frm.action += $ext + "tag=" + encodeURIComponent(tag);
                frm.submit()
            } else return false;
        }

        function set_options_for_select(select, opts, compvalue, prompt){
            select.innerHTML = '<option value="">-- ' + prompt + '--</option>';
            opts.forEach(function(item){
                const newOpt = document.createElement('option');
                newOpt.value = item.node_id;
                newOpt.textContent = item.prompt;
                newOpt.selected = item.node_id === compvalue;
                select.appendChild(newOpt);
            });
        }

        // Get the entry information
        const ent = <?php echo json_encode($ent); ?>;
        const ent_idx = <?php echo $ent_idx===null?"null":$ent_idx ?>

        // Create reference lists
        const accs = <?php echo json_encode($accs); ?>;
        const ascs = <?php echo json_encode($ascs); ?>;
        const deps = <?php echo json_encode($deps); ?>;
        const catincs = <?php echo json_encode($catincs); ?>;
        const catexps = <?php echo json_encode($catexps); ?>;

        // Get elements from page
        const entry_type = document.getElementById("entry_type");
        const balance_out = document.getElementById("balance_out");
        const balance_in = document.getElementById("balance_in");
        const result_out = document.getElementById("result_out");
        const result_in = document.getElementById("result_in");

        entry_type.addEventListener('change', function() {
            
            // Set the corresponding options
            switch(entry_type.value){
                case "exp":
                    balance_out.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_balance;
                        balance_out.appendChild(newOpt);
                    });

                    balance_in.innerHTML = '<option value="">-- Välj aktör --</option>';
                    ascs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_balance;
                        balance_in.appendChild(newOpt);
                    });

                    result_out.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_result;
                        result_out.appendChild(newOpt);
                    });

                    result_in.innerHTML = '<option value="">-- Välj kategori --</option>';
                    catexps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_result;
                        result_in.appendChild(newOpt);
                    });                    

                    break;
                case "inc":
                    balance_out.innerHTML = '<option value="">-- Välj aktör --</option>';
                    ascs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_balance;
                        balance_out.appendChild(newOpt);
                    });

                    balance_in.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_balance;
                        balance_in.appendChild(newOpt);
                    });

                    result_out.innerHTML = '<option value="">-- Välj kategori --</option>';
                    catincs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_result;
                        result_out.appendChild(newOpt);
                    });

                    result_in.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_result;
                        result_in.appendChild(newOpt);
                    });  
                    break;
                case "trn":
                    balance_out.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_balance;
                        balance_out.appendChild(newOpt);
                    });

                    balance_in.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_balance;
                        balance_in.appendChild(newOpt);
                    });

                    result_out.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_result;
                        result_out.appendChild(newOpt);
                    });

                    result_in.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_result;
                        result_in.appendChild(newOpt);
                    });  
                    break;
            }


        
            if(entry_type.value !== "") {
                balance_out.disabled = false;
                balance_in.disabled = false;
                result_out.disabled = false;
                result_in.disabled = false;
            } else {
                balance_out.disabled = true;
                balance_in.disabled = true;
                result_out.disabled = true;
                result_in.disabled = true;
            }
        });

        // Initiate accounting




        //console.log(ent_id);

        if(ent_idx === null){
            
            balance_out.innerHTML = '<option value="">-- Välj typ av post --</option>';
            balance_in.innerHTML = '<option value="">-- Välj typ av post --</option>';
            result_out.innerHTML = '<option value="">-- Välj typ av post --</option>';
            result_in.innerHTML = '<option value="">-- Välj typ av post --</option>';

            if(entry_type.value !== "") {
                balance_out.disabled = false;
                balance_in.disabled = false;
                result_out.disabled = false;
                result_in.disabled = false;
            } else {
                balance_out.disabled = true;
                balance_in.disabled = true;
                result_out.disabled = true;
                result_in.disabled = true;
            }
        } else {
            switch(entry_type.value){
                case "exp":
                    balance_out.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_balance;
                        balance_out.appendChild(newOpt);
                    });

                    balance_in.innerHTML = '<option value="">-- Välj aktör --</option>';
                    ascs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_balance;
                        balance_in.appendChild(newOpt);
                    });

                    result_out.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_result;
                        result_out.appendChild(newOpt);
                    });

                    result_in.innerHTML = '<option value="">-- Välj kategori --</option>';
                    catexps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_result;
                        result_in.appendChild(newOpt);
                    });                    

                    break;
                case "inc":
                    balance_out.innerHTML = '<option value="">-- Välj aktör --</option>';
                    ascs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_balance;
                        balance_out.appendChild(newOpt);
                    });

                    balance_in.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_balance;
                        balance_in.appendChild(newOpt);
                    });

                    result_out.innerHTML = '<option value="">-- Välj kategori --</option>';
                    catincs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_result;
                        result_out.appendChild(newOpt);
                    });

                    result_in.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_result;
                        result_in.appendChild(newOpt);
                    });  
                    break;
                case "trn":
                    balance_out.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_balance;
                        balance_out.appendChild(newOpt);
                    });

                    balance_in.innerHTML = '<option value="">-- Välj konto --</option>';
                    accs.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_balance;
                        balance_in.appendChild(newOpt);
                    });

                    result_out.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.credit_result;
                        result_out.appendChild(newOpt);
                    });

                    result_in.innerHTML = '<option value="">-- Välj depå --</option>';
                    deps.forEach(function(item) {
                        const newOpt = document.createElement('option');
                        newOpt.value = item.node_id;
                        newOpt.textContent = item.prompt;
                        newOpt.selected = item.node_id === ent.debet_result;
                        result_in.appendChild(newOpt);
                    });  
                    break;
            }


        
            if(entry_type.value !== "") {
                balance_out.disabled = false;
                balance_in.disabled = false;
                result_out.disabled = false;
                result_in.disabled = false;
            } else {
                balance_out.disabled = true;
                balance_in.disabled = true;
                result_out.disabled = true;
                result_in.disabled = true;
            }
        }

 

    </script>

    </body>

</html>