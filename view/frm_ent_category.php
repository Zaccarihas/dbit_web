<?php


    // Init
    session_start();
    require "../controller/error_reporting.php";
    $db = require "../controller/dbconnect.php";
    require "../controller/alerts.php";

    // Category references
    $sql = <<< SQL
        SELECT node_id, CONCAT(label, " - ", title) AS prompt
        FROM nodes 
        WHERE type='Category'
        ORDER BY prompt ASC
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $selection = $qry->fetchAll(PDO::FETCH_ASSOC);

    $ent= $_SESSION["entry"];
    $ent_idx = isset($_GET["ent"])?$_GET["ent"]:null;
    $new_entry = $ent_idx === null;
    $fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

    //echo "<br>POST<pre>"; print_r($_POST); echo "</pre>";
    //echo "<br>GET<pre>"; print_r($_GET); echo "</pre>";
    //echo "<br>Entry:"; var_dump($ent_idx);
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Entry Category form</title>
        <link rel="stylesheet" href="view/ent_cat_form.css">
    <head>

    <body>
        <form id="frm_entry" method="post" action="../controller/ctrl_entry_category_pst.php<?php echo $ent_idx===null?"":"?ent=".$ent_idx ?>">
                       
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
                    <label for="node">Category</label>
                    <select id="node" name="node">
                        <option value="">-- Välj en kategori --</option><?php
                        foreach($selection as $itm){
                            ?><option value="<?php echo $itm["node_id"]; ?>"<?php 
                                echo $ent["node_id"]==$itm["node_id"]?" selected":null ?>><?php
                                echo $itm["prompt"] ?></option><?php 
                        } ?>
                    </select>
                </div>

                <div class="formfield">
                    <label for="total">Total</label>
                    <input type="text" id="total" name="total" value="<?php 
                        //echo $fmt->formatCurrency($ent["total"]??$_SESSION["transaction"]["difference_categories"]??0,"SEK");
                        echo $fmt->formatCurrency($new_entry?$_SESSION["transaction"]["difference_categories"]:$ent["total"],"SEK");
                    ?>" placeholder="Belopp" required/>
                </div>

                <div class="formfield">
                    <label for="quantity">Mängd</label>
                    <input type="text" id="quantity" name="quantity" value="<?php 
                        echo $ent["quantity"];
                    ?>" placeholder="Mängd"/>
                </div>

                <div class="formfield">
                    <label for="unit">Enhet</label>
                    <input type="text" id="unit" name="unit" value="<?php 
                        echo $ent["unit"];
                    ?>" placeholder="Enhet"/>
                </div>

                <div class="formfield">
                    <label for="product">Produkt</label>
                    <input type="text" id="product" name="product" value="<?php 
                        echo $ent["product"];
                    ?>" placeholder="Produkt"/>
                </div>

                <div class="formfield">
                    <label for="ent_note">Note</label>
                    <textarea id="ent_note" name="ent_note" rows=5 cols=20><?php 
                        echo $ent["ent_note"]; 
                    ?></textarea>
                </div>
            
            </fieldset>
            <fieldset id="fldset_tags">
                <div id="tag_ctrl">
                    <input type="text" id="tag" name="tag">
                    <button type="submit" id="btn_addtag" name="submit" value="add_tag">Add</button>
                </div>
                <div id="tags_list">
                    <?php foreach($_SESSION['entry']['tags'] as $tag){ ?>
                        <button type="submit" name="submit" value="<?php echo $tag ?>"><?php echo $tag; ?></button><?php } ?>
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