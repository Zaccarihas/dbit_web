<?php
    
    // Development    
    //require "controller/error_reporting.php";
        
    // Init
    session_start();
    require "../controller/dbconnect.php";

    // Testing
    //echo "<b><h4>Session</h4><pre>"; print_r($_SESSION); echo "</pre>";

    // Reference to nodes
    /*
    $nodes = [];
    $sql = <<< SQL
        SELECT * 
        FROM nodes 
        ORDER BY node_id ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $res = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach($res as $itm){
        $nodes[$itm["node_id"]] = $itm;
    }
    */

    //echo "<pre>"; print_r($nodes); echo "</pre>"; die();

    //echo "<b><h4>Associates</h4><pre>"; print_r($ascs); echo "</pre>";
    
    $trn = $_SESSION["transaction"];
    $new = $trn["transaction_id"] === null;
    $fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

    // Testing
    //echo "<br>Temporary Transaction:<pre>"; print_r($trn); echo "</pre>";
    //die();

?>
<html>
    <head>
        <title>Transaction form</title>
        <link rel="stylesheet" href="../view/css/trn_form.css">
    <head>

    <body>
        <form id="frm_transaction" method="post" action="../controller/ctrl_transaction_pst.php">
            
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
                    <label for="note">Note</label>
                    <textarea cols="30" rows="5" id="trn_note" name="trn_note" placeholder="Anteckning"><?php
                        echo $trn["trn_note"];
                    ?></textarea>
                </div>

            </fieldset>

            <fieldset id="fldset_summary">
                <div class="formfield">
                    <label for="target_total">Target total</label>
                    <input type="text" id="target_total" name="total_target" value="<?php 
                        echo $fmt->formatCurrency($trn["total_target"]??0,"SEK")
                    ?>" placeholder="Target total"/>
                </div>    
                <div id="tags">
                    <div id="tag_ctrl">
                        <input type="text" id="tag" name="tag">
                        <button type="submit" id="btn_addtag" name="submit" value="add_tag">Add</button>
                    </div>
                    <div id="tags_list">
                        <?php foreach($_SESSION['transaction']['tags'] as $tag){ ?>
                        <button type="submit" name="submit" value="<?php echo "tag_".$tag ?>"><?php echo $tag; ?></button><?php } ?>
                    </div>
                </div>
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

            <div id="layout_entries">
                
                <fieldset id="fldset_accounts">
                    <legend>Affected Accounts</legend>
                    <div class="<?php echo $_SESSION["transaction"]["difference_accounts"] == 0?"positive":"negative"; ?>">
                        <p id="tot_acc">Total: <?php echo $_SESSION["transaction"]["total_accounts"]?></p>
                        <p id="diff_acc">Difference: <?php echo $_SESSION["transaction"]["difference_accounts"]?></p>
                    </div>
                    <button class="formbuttons" type="submit" id="btn_add_account" name="submit" value="add_account" formnovalidate >Add Account</button>
                    <table id="accounts_table">
                        <thead>
                            <tr>
                                <th>Ctrl</th>
                                <th>Status</th>
                                <th>Account</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($_SESSION['transaction']['ent_accounts'] as $idx => $ent){?>
                                <tr id="<?php echo $idx;?>" class="<?php echo $ent["ent_direction"] == "from"?"negative":"positive"; ?>">
                                    <td><button type="submit" name="submit" value="open_account_<?php echo $idx ?>">Open</button></td>
                                    <td><?php echo $ent["status"] ?></td>
                                    <td><?php echo $ent["prompt"] ?></td>                                       
                                    <td><?php echo $fmt->formatCurrency($ent["total"]??0,"SEK");?></td>
                                </tr><?php
                            }?>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset class="fldset_associates">
                    <legend>Associates</legend>
                    <div class="<?php echo $_SESSION["transaction"]["difference_associates"] == 0?"positive":"negative"; ?>">
                        <p id="tot_asc">Total:  <?php echo $_SESSION["transaction"]["total_associates"]?></p>
                        <p id="tot_acc">Difference: <?php echo $_SESSION["transaction"]["difference_associates"]?></p>
                    </div>
                    <button class="formbuttons" type="submit" id="btn_add_assoc" name="submit" value="add_assoc" formnovalidate >Add Associate</button>
                    <table id="associate_table">
                        <thead>
                            <tr>
                                <th>Ctrl</th>
                                <th>Associate</th>
                                <th>Reference</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($_SESSION['transaction']['ent_associates'] as $idx => $ent){?>
                                <tr id="<?php echo $idx ?>" class="<?php echo $ent["ent_direction"] == "from"?"negative":"positive"; ?>">
                                    <td><button type="submit" name="submit" value="open_assoc_<?php echo $idx ?>">Open</button></td>
                                    <td><?php echo $ent["prompt"] ?></td>
                                    <td><?php echo $ent["reference"] ?></td>
                                    <td><?php echo $fmt->formatCurrency($ent["total"]??0,"SEK");?></td>
                                </tr><?php
                            }?>
                        </tbody>
                    </table>
                </fieldset>
                
                <fieldset class="fldset_depots">
                    <legend>Depots</legend>
                    <div class="<?php echo $_SESSION["transaction"]["difference_depots"] == 0?"positive":"negative"; ?>">
                        <p id="tot_acc">Total:  <?php echo $_SESSION["transaction"]["total_depots"]?></p>
                        <p id="tot_acc">Difference: <?php echo $_SESSION["transaction"]["difference_depots"]?></p>
                    </div>
                    <button class="formbuttons" type="submit" id="btn_add_depot" name="submit" value="add_depot" formnovalidate >Add Depot</button>
                    <table id="depots_table">
                        <thead>
                            <tr>
                                <th>Ctrl</th>
                                <th>Depot</th>
                                <th>Total</th>                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($_SESSION['transaction']['ent_depots'] as $idx => $ent){?>
                                <tr id="<?php echo $idx ?>" class="<?php echo $ent["ent_direction"] == "from"?"negative":"positive"; ?>">
                                    <td><button type="submit" name="submit" value="open_depot_<?php echo $idx ?>">Open</button></td>
                                    <td><?php echo $ent["prompt"] ?></td>
                                    <td><?php echo $fmt->formatCurrency($ent["total"]??0,"SEK");?></td>
                                </tr><?php
                            }?>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset class="fldset_categories">
                    <legend>Categorized Items</legend>
                    <div class="<?php echo $_SESSION["transaction"]["difference_categories"] == 0?"positive":"negative"; ?>">
                        <p id="tot_acc">Total: <?php echo $_SESSION["transaction"]["total_categories"]?></p>
                        <p id="tot_acc">Difference: <?php echo $_SESSION["transaction"]["difference_categories"]?></p>
                    </div>
                    <button class="formbuttons" type="submit" id="btn_add_category" name="submit" value="add_category" formnovalidate >Add Category</button>
                    <table id="categories_table">
                        <thead>
                            <tr>
                                <th>Ctrl</th>
                                <th>Category</th>
                                <th>Note</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($_SESSION['transaction']['ent_categories'] as $idx => $ent){ ?>
                                <tr id="<?php echo $idx;?>" class="<?php echo $ent["ent_direction"] == "from"?"positive":"negative"; ?>">
                                    <td><button type="submit" name="submit" value="open_category_<?php echo $idx ?>">Open</button></td>
                                    <td><?php echo $ent["prompt"] ?></td>
                                    <td><?php echo $ent["ent_note"] ?></td>                                       
                                    <td><?php echo $fmt->formatCurrency($ent["total"]??0,"SEK");?></td>
                                </tr><?php
                            }?>
                        </tbody>
                    </table>
                </fieldset>
            </div>

        </form>

        <script>
            
            function delete_acc(){

                const frm = document.getElementById("frm_transaction");
                const confirmed = window.confirm("Är du säker på att du vill ta bort transaktionen");
                
                if(confirmed) {
                    frm.submit();
                } else { return false; }
                
            }

            // Get the element for the summary
            const transaction = <?php echo json_encode($_SESSION["transaction"]); ?>;
            const target_total = document.getElementById("target_total");
            const actual_total = document.getElementById("actual_total");
            const total_difference = document.getElementById("total_difference");
            
            // Calculate all outgoing expense sums
            exp_entries = transaction["entries"].filter(ent => ent.entry_type === "exp");
            console.log(exp_entries);
            exptot = exp_entries.reduce((tot, ent)=> tot + parseFloat(ent.amount), 0);
            console.log(exptot);

            // Calculate all incoming income sums
            inc_entries = transaction["entries"].filter(ent => ent.entry_type === "inc");
            console.log(inc_entries);
            inctot = inc_entries.reduce((tot, ent) => tot + parseFloat(ent.amount), 0);
            console.log(inctot);

            acttot = inctot - exptot;
            console.log("Target total field: " + target_total.value.replace(",",".").replace("−","-"));

            trgtot = parseFloat(target_total.value.replace(",",".").replace("−","-"));
            console.log("Target tot: " + trgtot);
            diff = trgtot - acttot;
            //console.log(parseFloat(target_total.value.replace(",",".")));
            
            actual_total.innerHTML = acttot;
            total_difference.innerHTML = diff;
        </script>

    </body>

</html>