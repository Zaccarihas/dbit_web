<?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_depot.php";

    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();
    $dpt = new Depot($db);

    // Get lookup depots 
    $sql = <<< SQL
        SELECT id, prompt
        FROM vw_depots
        ORDER BY prompt ASC
    SQL;

    $qry = $db->prepare($sql);
    DB_manager::run($qry);
    $parents = $qry->fetchAll(PDO::FETCH_ASSOC);

    if($dm->get_set()) {
        $dpt->id = $dm->get["id"];
        $dpt->load();
    }

?>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>Depot form</title>
        <link rel="stylesheet" href="/dbit/view/css/dep_form.css">
    <head>

    <body>
        <form id="frm_depot" method="post" action="/dbit/controller/ctrl_depot_pst.php">
            <div id="node_id">
                <?php echo $dpt->is_new()?"NEW":$dpt->id; ?>
                <input type="hidden" name="id" value="<?php echo $dpt->is_new()?null:$dpt->id?>">
            </div>
            <div class="formfield">
                <label class="label"for="label">Label</label>
                <input class="field" type="text" id="label" name="label" value="<?php 
                    echo $dpt->is_new()?null:$dpt->label;
                ?>" placeholder="Etikett" required/>
            </div>
            <div class="formfield">
                <label class="label" for="title">Note</label>
                <input class="field" type="text" id="title" name="title" value="<?php 
                    echo $dpt->is_new()?null:$dpt->title
                ?>" placeholder="Depå"/>
            </div>
            <div class="formfield">
                <label class="label" for="parent">Parent</label>
                <select class="field" id="parent" name="parent">
                    <option value=""<?php echo $dpt->is_new()? "selected":"" ?>>Välj överställd depå</option>
                    <?php 
                    foreach($parents as $par){ ?>
                        <option 
                            value="<?php echo $par["id"] ?>" <?php 
                                echo $dpt->parent == $par["id"]?"selected":"" 
                            ?>><?php echo $par["prompt"] ?></option>
                    <?php } ?>
                </select>
            </div>
            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="register" name="submit" value="register">Register</button>
                <?php
                    if(!$dpt->is_new()) {
                        ?>
                        <button class="formbuttons" type="submit" name= "submit" value="delete" onclick="delete_dpt(); return false;">Delete</button>
                        <?php
                    } 
                ?>                
                <button class="formbuttons" type="submit" name="submit" value="cancel">Cancel</button>
            </fieldset>

        </form>

    <script>
        function delete_dpt(){

            const frm = document.getElementById("frm_depot");
            const confirmed = window.confirm("Är du säker på att du vill ta bort depån");
            
            if(confirmed) {
                frm.submit();
            } else { return false; }
                
        }
    </script>

    </body>

</html>