<?php

    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_associate.php";

    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();

    $assoc = new Associate($db);

    if($dm->get_set()) {
        $assoc->id = $dm->get["id"];
        $assoc->load();
    }

?>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>Associate form</title>
        <link rel="stylesheet" href="view/ass_form.css?v=4">
    <head>

    <body>
        <form id="frm_associate" method="post" action="/dbit/controller/ctrl_associate_pst.php">
            
            <div id="node_id">
                <?php echo $assoc->is_new()?"NEW":$assoc->id; ?>
                <input type="hidden" name="id" value="<?php echo $assoc->is_new()?null:$assoc->id ?>">
            </div>
            
            <div class="formfield">
                <label class="label"for="title">Title</label>
                <input class="field" type="text" id="title" name="title" value="<?php 
                    echo $assoc->is_new()?null:$assoc->title;
                ?>" placeholder="Motpart" required/>
            </div>

            <div class="formfield">
                <label class="label"for="location">Location</label>
                <input class="field" type="text" id="location" name="location" value="<?php 
                    echo $assoc->is_new()?null:$assoc->location;
                ?>" placeholder="Placering"/>
            </div>

            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" value="register" id="register" name="submit">Register</button>
                <?php
                    if(!$assoc->is_new()) {
                        ?>
                        <button class="formbuttons" type="submit" value="delete" name="submit" onclick="delete_asc(); return false;">Delete</button>
                        <?php
                    } 
                ?>                
                <button class="formbuttons" type="submit" name="submit" value="cancel">Cancel</button>
            </fieldset>
        </form>

    <script>

        function delete_asc(){

            const frm = document.getElementById("frm_associate");
            const confirmed = window.confirm("Är du säker på att du vill ta bort aktören");
            
            if(confirmed) {
                frm.submit();
            } else { return false; }
                
        }

    </script>

    </body>

</html>