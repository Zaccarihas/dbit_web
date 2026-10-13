<?php

require "dbconnect.php";

$qry = $db->prepare("SELECT * FROM vw_accounts WHERE account_id=3");
$qry->execute();
$res = $qry->fetchAll(PDO::FETCH_ASSOC);

?>
<html>
    <head>
        <title>Account form</title>
        <link rel="stylesheet" href="view/style.css">
    <head>

    <body>
        <form id="frm_account" method="post" action="controller/account_new.php">
            <div id="account_id">
                <?php 
                    $id_part1 = str_pad(intdiv($res[0]["account_id"],1000),3,"0",STR_PAD_LEFT);
                    $id_part2 = str_pad($res[0]["account_id"]%1000,3,"0",STR_PAD_LEFT);
                    printf("%s %s", $id_part1, $id_part2); 
                ?>
                <input type="hidden" name="account_id" value="<?php echo $res[0]["account_id"] ?>">
                <input type="hidden" name="parent" value="<?php echo $res[0]["parent"] ?>">
            </div>
            <fieldset id="fldset_desc"> 

                <div class="formfield">
                    <label for="label">Label</label>
                    <input type="text" id="label" name="label" value="<?php echo $res[0]["label"] ?>"/>
                </div>
                
                <div class="formfield">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo $res[0]["title"] ?>"/>
                </div>

                <div class="formfield">
                    <label for="trustee">Trustee</label>
                    <input type="text" id="trustee" name="trustee" value="<?php echo $res[0]["trustee"] ?>"/>
                </div>
                
                <div class="formfield">
                    <label for="alias_id">Alias</label>
                    <input type="text" id="alias_id" name="alias_id" value="<?php echo $res[0]["alias_id"] ?>"/>
                </div>
                
                <div class="formfield">
                    <label for="note">Note</label>
                    <textarea id="note" name="note" rows="5"></textarea>
                </div>

            </fieldset>
            <fieldset id="fldset_meta">
                
                <div class="formfield">
                    <label for="upper_limit">Upper limit</label>
                    <input type="text" id="upper_limit" name="upper_limit" value="<?php echo $res[0]["upper_limit"] ?>"/>
                </div>
                
                <div class="formfield">
                    <label for="lower_limit">Lower limit</label>
                    <input type="text" id="lower_limit" name="lower_limit" value="<?php echo $res[0]["lower_limit"] ?>"/>
                </div>

                <div class="formfield">
                    <label for="rate">Rate</label>
                    <input type="text" id="rate" name="rate" value="<?php echo $res[0]["rate"] ?>"/>
                </div>
            
            </fieldset>
            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="register" name="register">Register</button>
                <button class="formbuttons" type="cancel" id="cancel" name="cancel">Cancel</button>
            </fieldset>

        </form>
    </body>

</html>