<?php

    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_category.php";

    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();
    $cat = new Category($db);
    
    // Lookups for parent
    $sql = <<< SQL
        SELECT id, prompt
        FROM vw_categories
        ORDER BY prompt
    SQL;

    $qry = $db->prepare($sql);
    DB_manager::run($qry);
    $parents = $qry->fetchAll(PDO::FETCH_ASSOC);

    // Load selected category
    if($dm->get_set()) {
        $cat->id = $dm->get["id"];
        $cat->load();
    }

?>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>Recon form</title>
        <link rel="stylesheet" href="/dbit/view/css/cat_form.css">
    <head>

    <body>
        <form id="frm_category" method="post" action="/dbit/controller/ctrl_category_pst.php">
            <div id="node_id">
                <?php echo $cat->is_new()?"NEW":$cat->id; ?>
                <input type="hidden" name="id" value="<?php echo $cat->is_new()?null:$cat->id ?>">
            </div>
            <div class="formfield">
                <label class="label" for="label">Label</label>
                <input class="field" type="text" id="label" name="label" value="<?php 
                    echo $cat->is_new()?null:$cat->label;
                ?>" placeholder="Etikett" required/>
            </div>
            <div class="formfield">
                <label class="label" for="title">Title</label>
                <input class="field" type="text" id="title" name="title" value="<?php 
                    echo $cat->is_new()?null:$cat->title
                ?>" placeholder="Titel"/>
            </div>
            <div class="formfield">
                <label class="label" for="parent">Parent</label>
                <select class="field" id="parent" name="parent">
                    <option value=""<?php echo $cat->is_new()? "selected":"" ?>>Välj överställt konto</option>
                    <?php 
                    foreach($parents as $par){ ?>
                        <option 
                            value="<?php echo $par["id"] ?>" <?php 
                                echo $cat->parent == $par["id"]?"selected":"" 
                            ?>><?php echo $par["prompt"] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="formfield">
                <label class="label" for="subgroup">Category</label>
                <input class="field" type="text" id="subgroup" name="subgroup" value="<?php 
                    echo $cat->is_new()?null:$cat->subgroup
                ?>" placeholder="Kategori"/>
            </div>
            <div class="formfield">
                <label class="label" for="description">Description</label>
                <input class="field" type="text" id="description" name="description" value="<?php 
                    echo $cat->is_new()?null:$cat->title
                ?>" placeholder="Beskrivning"/>
            </div>
            <div class="formfield">
                <label class="label" for="note">Note</label>
                <input class="field" type="text" id="note" name="note" value="<?php 
                    echo $cat->is_new()?null:$cat->note
                ?>" placeholder="Notering"/>
            </div>

            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="register" name="submit" value="register">Register</button>
                <?php
                    if(!$cat->is_new()) {
                        ?>
                        <button class="formbuttons" type="submit" onclick="delete_itm(); return false;">Delete</button>
                        <?php
                    } 
                ?>                
                <button class="formbuttons" type="submit">Cancel</button>
            </fieldset>

        </form>

    <script>
        function delete_itm(){

            const frm = document.getElementById("frm_category");
            const confirmed = window.confirm("Är du säker på att du vill ta bort kategorin");
            
            if(confirmed) {
                frm.submit();
            } else { return false; }
                
        }
    </script>

    </body>

</html>