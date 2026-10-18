<?php

    //require_once $_SERVER["DOCUMENT_ROOT"]."dbit/controller/error_reporting.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_account.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";

    
    $debug = false;

    // Init
    $dm = New Datatransfer_manager();
    $dbm = New DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt"); 
    $db = $dbm->connect();

    if($debug){
        echo "<hr>Account Form";
        echo "<br><pre>"; print_r($dm->get); echo "<pre>";
        echo "<br><pre>"; print_r($dm->post); echo "<pre>";
        die();
    }

    $acc = New Account($db, id: $dm->get_set()?$dm->get["id"]:null);
    $acc->load();

    // Get account references for the parent selection field
    $sql = <<< SQL
    SELECT id, prompt
    FROM vw_accounts
    ORDER BY id
    SQL;

    $qry = $db->prepare($sql);
    DB_manager::run($qry);

    $accounts = array_column($qry->fetchAll(PDO::FETCH_ASSOC), 'prompt', 'id')

?>
<html>
    <head>
        <title>Account form</title>
        <link rel="stylesheet" href="/view/css/acc_form.css">
    <head>

    <body>
        <form id="frm_account" method="post" action="/dbit/controller/ctrl_account_pst.php">
            <div id="account_id">
                <?php
                    if($acc->is_new()){
                        echo "NEW";
                    } 
                    else {
                        $id_part1 = str_pad(intdiv($acc->id,1000),3,"0",STR_PAD_LEFT);
                        $id_part2 = str_pad($acc->id%1000,3,"0",STR_PAD_LEFT);
                        printf("%s %s", $id_part1, $id_part2); 
                    }
                ?>
                <input type="hidden" name="id" value="<?php echo $acc->is_new()?null:$acc->id?>">
            </div>
            <fieldset id="fldset_desc"> 

                <div class="formfield">
                    <label for="label">Label</label>
                    <input type="text" id="label" name="label" value="<?php echo $acc->is_new()?null:$acc->label?>" placeholder="Kontots etikett eller kod" pattern="[A-Za-z0-9]+" required/>
                </div>
                
                <div class="formfield">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo $acc->is_new()?null:$acc->title?>" placeholder="Kontots namn" required/>
                </div>

                <div class="formfield">
                    <label for="parent">Parent</label>
                    <select name="parent">
                        <option value=""<?php echo $acc->parent===null?"selected":"" ?>>Välj överställt konto</option><?php 
                        foreach($accounts as $idx => $prompt){ 
                            ?><option 
                                value="<?php echo $idx ?>" <?php 
                                    echo $acc->parent == $idx?"selected":"" 
                                ?>><?php echo $prompt ?></option><?php }
                    ?></select>
                </div>

                <div class="formfield">
                    <label for="trustee">Trustee</label>
                    <input type="text" id="trustee" name="trustee" value="<?php echo $acc->is_new()?null:$acc->trustee?>" placeholder="Förvaltare av kontot"/>
                </div>
                
                <div class="formfield">
                    <label for="alias">Alias</label>
                    <input type="text" id="alias" name="alias" value="<?php echo $acc->is_new()?null:$acc->alias?>" placeholder="Kontots alias hos förvaltaren">
                </div>
                
                <div class="formfield">
                    <label for="note">Note</label>
                    <textarea id="note" name="note" rows="5"><?php echo $acc->is_new()?null:$acc->note ?></textarea>
                </div>

            </fieldset>
            <fieldset id="fldset_meta">
                
                <div class="formfield">
                    <label for="upper_limit">Upper limit</label>
                    <input type="text" id="upper_limit" name="upper_limit" value="<?php echo $acc->is_new()?null:$acc->upper_limit?>" placeholder="Övre beloppsgräns"/>
                </div>
                
                <div class="formfield">
                    <label for="lower_limit">Lower limit</label>
                    <input type="text" id="lower_limit" name="lower_limit" value="<?php echo $acc->is_new()?0:$acc->lower_limit?>" placeholder="Nedre beloppsgräns (negativt för kreditutrymme)"/>
                </div>

                <div class="formfield">
                    <label for="rate">Rate</label>
                    <input type="text" id="rate" name="rate" value="<?php echo $acc->is_new()?1:$acc->rate?>" placeholder="Omvandlingsfaktor"/>
                </div>
            
            </fieldset>
            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="btn_reg" name="submit" value="register" accesskey="'">Register</button>
                <?php
                    if(!$acc->is_new()) {
                        ?><button type="submit" class="formbuttons" id="btn_del" name="submit" value="delete" accesskey = "-" formnovalidate onclick="delete_acc()">Delete</button>
                        <?php
                    } 
                ?><button class="formbuttons" type="submit" id="btn_bck" name="submit" value="cancel" accesskey="¨" formnovalidate>Cancel</button>
            </fieldset>

        </form>

    <script>
        function delete_acc_old(account){

            const confirmed = window.confirm("Är du säker på att du vill ta bort kontot");
            
            if(confirmed) {
                window.location.href = "../controller/account_del.php?id=" + account;
            }           
            
        }

        function delete_acc(){

            const frm = document.getElementById("frm_account");
            const confirmed = window.confirm("Är du säker på att du vill ta bort kontot");
            
            if(confirmed) {
                frm.submit();
            } else { return false; }
                
        }
    </script>

    </body>

</html>