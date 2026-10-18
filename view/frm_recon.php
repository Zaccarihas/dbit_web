<?php
    //print_r($_GET);

    $db = require "dbconnect.php";

    $dta = [
        "recon_id" => null,
        "rec_date" => null,
        "account" => null,
        "note" => null,
        "total" => null,
    ];

    $sql = <<< SQL
        SELECT account_id, label, title
        FROM vw_accounts
        ORDER BY label ASC
    SQL;

    $qry = $db->prepare($sql);
    $qry->execute();
    $accs = $qry->fetchAll(PDO::FETCH_ASSOC);

    if($_GET["id"]) {

        $qry = $db->prepare("SELECT * FROM vw_recons WHERE recon_id=:id");
        $qry->bindValue(":id", (int)$_GET["id"]);
        $qry->execute();
        $dta = $qry->fetchAll(PDO::FETCH_ASSOC)[0];

    }

    $fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

    $new_itm = $dta["recon_id"] == null;


?>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>Recon form</title>
        <link rel="stylesheet" href="view/rec_form.css">
    <head>

    <body>
        <form id="frm_recon" method="post" action="controller/recon_reg.php">
            <div id="recon_id">
                <?php echo $new_itm?"NEW":$dta["recon_id"]; ?>
                <input type="hidden" name="recon_id" value="<?php echo $new_itm?null:$dta["recon_id"]?>">
            </div>
            <div class="formfield">
                <label class="label"for="reconciled">Reconciled</label>
                <input class="field" type="text" id="reconciled" name="reconciled" value="<?php 
                    echo $new_itm?date("Y-m-d H:i:s"):$dta["rec_date"];
                ?>" placeholder="Avstämningstidpunkt" required/>
            </div>
            <div class="formfield">
                <label class="label" for="account">Account</label>
                <select class="field" id="account" name="account" required>
                    <option value=""<?php echo $new_itm? "selected":"" ?>>Välj avstämningskonto</option>
                    <?php 
                    foreach($accs as $acc){ ?>
                        <option 
                            value="<?php echo $acc["account_id"] ?>" <?php 
                                echo $dta["account"] == $acc["account_id"]?"selected":"" 
                            ?>><?php echo $acc["label"]." - ".$acc["title"] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="formfield">
                <label class="label" for="note">Note</label>
                <input class="field" type="text" id="note" name="note" value="<?php 
                    echo $new_itm?null:$dta["note"]
                ?>" placeholder="Notering"/>
            </div>
            <div class="formfield">
                <label class="label" for="total">Total</label>
                <input class="field" type="text" id="total" name="total" value="<?php 
                    echo $new_itm?null:$fmt->formatCurrency($dta["total"]??0,"SEK");
                ?>" placeholder="Avstämnt belopp"/>
            </div>
            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="register" name="register">Register</button>
                <?php
                    if(!$new_itm) {
                        ?>
                        <button class="formbuttons" type="button" onclick="delete_itm(); return false;">Delete</button>
                        <?php
                    } 
                ?>                
                <button class="formbuttons" type="button" onclick="window.location.href='lst_recons.php'">Cancel</button>
            </fieldset>

        </form>

    <script>
        function delete_itm(){

            //alert("Hit kom du");

            const confirmed = window.confirm("Är du säker på att du vill ta bort avstämningen");
            
            if(confirmed) {
                alert("Skickar vidare till "+ <?php echo $dta["recon_id"] ?>);
                window.location.href = "controller/recon_del.php?id=" + <?php echo $dta["recon_id"] ?>;
            }           
            
        }
    </script>

    </body>

</html>