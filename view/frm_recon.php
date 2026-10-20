<?php
    
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_tbl_recon.php";
    
    $dm = new Datatransfer_manager();
    $dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
    $db = $dbm->connect();
    $recon = new Recon($db);

    $sql = <<< SQL
        SELECT id, prompt
        FROM vw_accounts
        ORDER BY prompt ASC
    SQL;

    $qry = $db->prepare($sql);
    DB_manager::run($qry);

    $accs = $qry->fetchAll(PDO::FETCH_ASSOC);

    if($dm->get_set()) {
        $recon->id = $dm->get['id'];
        $recon->load();
    }

    $fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

?>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>Recon form</title>
        <link rel="stylesheet" href="/dbit/view/css/rec_form.css">
    <head>

    <body>
        <form id="frm_recon" method="post" action="/dbit/controller/ctrl_recon_pst.php">
            <div id="recon_id">
                <?php echo $recon->is_new()?"NEW":$recon->id; ?>
                <input type="hidden" name="id" value="<?php echo $recon->is_new()?null:$recon->id?>">
            </div>
            <div class="formfield">
                <label class="label"for="reconciled">Reconciled</label>
                <input class="field" type="text" id="date" name="date" value="<?php 
                    echo $recon->is_new()?date("Y-m-d H:i:s"):$recon->date;
                ?>" placeholder="Avstämningstidpunkt" required/>
            </div>
            <div class="formfield">
                <label class="label" for="account">Account</label>
                <select class="field" id="account" name="account" required>
                    <option value=""<?php echo $recon->is_new()? "selected":"" ?>>Välj avstämningskonto</option>
                    <?php 
                    foreach($accs as $acc){ ?>
                        <option 
                            value="<?php echo $acc["id"] ?>" <?php 
                                echo $recon->account == $acc["id"]?"selected":"" 
                            ?>><?php echo $acc["prompt"] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="formfield">
                <label class="label" for="note">Note</label>
                <input class="field" type="text" id="note" name="note" value="<?php 
                    echo $recon->is_new()?null:$recon->note
                ?>" placeholder="Notering"/>
            </div>
            <div class="formfield">
                <label class="label" for="total">Total</label>
                <input class="field" type="text" id="total" name="total" value="<?php 
                    echo $recon->is_new()?null:$fmt->formatCurrency($recon->total??0,"SEK");
                ?>" placeholder="Avstämnt belopp"/>
            </div>
            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" name="submit" id="register" value="register">Register</button>
                <?php
                    if(!$recon->is_new()) {
                        ?>
                        <button class="formbuttons" type="submit" name="submit" value="delete"onclick="delete_itm(); return false;">Delete</button>
                        <?php
                    } 
                ?>                
                <button class="formbuttons" type="submit" name="submit" value="cancel">Cancel</button>
            </fieldset>

        </form>

    <script>

        function delete_itm(){

            const frm = document.getElementById("frm_recon");
            const confirmed = window.confirm("Är du säker på att du vill ta bort avstämningen");
            
            if(confirmed) {
                frm.submit();
            } else { return false; }
                
        }
    </script>

    </body>

</html>