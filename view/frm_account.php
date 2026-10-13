<?php
    // Init
    require "dbconnect.php";
    //require "controller/error_reporting.php";

    $dta = [
        "account_id" => null,
        "parent" => null,
        "label" => null,
        "title" => null,
        "upper_limit" => null,
        "lower_limit" => null,
        "trustee" => null,
        "alias_id" => null,
        "rate" => null,
        "note" => null
    ];

    if(isset($_GET["id"])) {
        $qry = $db->prepare("SELECT * FROM vw_accounts WHERE account_id=:id");
        $qry->bindValue(":id", (int)$_GET["id"]);
        $qry->execute();
        $dta = $qry->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    // Testing
    /*
    echo "<br><h4>Account</h4><pre>"; print_r($dta); echo "</pre>";
    echo "<br><h4>Call</h4><pre>"; print_r($_GET); echo "</pre>";
    */

    $new_acc = $dta["account_id"] == null;

    // nodes references
    $sql = <<< SQL
        SELECT * FROM nodes ORDER BY label
    SQL;
    $qry = $db->prepare($sql);
    $qry->execute();
    $res = $qry->fetchAll(PDO::FETCH_ASSOC);
    $nodes = [];
    foreach($res as $ent){
        $idx = $ent["node_id"];
        $nodes[$idx] = [
            "prompt" => $ent["type"]=="Associate"?$ent["title"]:$ent["label"]." - ".$ent["title"],
            "type" => $ent["type"]
        ];
    }


?>
<html>
    <head>
        <title>Account form</title>
        <link rel="stylesheet" href="view/acc_form.css">
    <head>

    <body>
        <form id="frm_account" method="post" action="controller/account_reg.php">
            <div id="account_id">
                <?php
                    if($new_acc){
                        echo "NEW";
                    } 
                    else {
                        $id_part1 = str_pad(intdiv($dta["account_id"],1000),3,"0",STR_PAD_LEFT);
                        $id_part2 = str_pad($dta["account_id"]%1000,3,"0",STR_PAD_LEFT);
                        printf("%s %s", $id_part1, $id_part2); 
                    }
                ?>
                <input type="hidden" name="account_id" value="<?php echo $new_acc?null:$dta["account_id"]?>">
            </div>
            <fieldset id="fldset_desc"> 

                <div class="formfield">
                    <label for="label">Label</label>
                    <input type="text" id="label" name="label" value="<?php echo $new_acc?null:$dta["label"]?>" placeholder="Kontots etikett eller kod" pattern="[A-Za-z0-9]+" required/>
                </div>
                
                <div class="formfield">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo $new_acc?null:$dta["title"]?>" placeholder="Kontots namn" required/>
                </div>

                <div class="formfield">
                    <label for="parent">Parent</label>
                    <select name="parent">
                        <option value=""<?php echo $dta["parent"]===null?"selected":"" ?>>Välj överställt konto</option>
                        <?php 
                        foreach($nodes as $idx => $acc){ 
                            if($acc["type"] == "Account") {?>
                            <option 
                                value="<?php echo $idx ?>" <?php 
                                    echo $dta["parent"] == $idx?"selected":"" 
                                ?>><?php echo $acc["prompt"] ?></option>
                        <?php }} ?>
                    </select>
                </div>

                <div class="formfield">
                    <label for="trustee">Trustee</label>
                    <input type="text" id="trustee" name="trustee" value="<?php echo $new_acc?null:$dta["trustee"]?>" placeholder="Förvaltare av kontot"/>
                </div>
                
                <div class="formfield">
                    <label for="alias_id">Alias</label>
                    <input type="text" id="alias_id" name="alias_id" value="<?php echo $new_acc?null:$dta["alias_id"]?>" placeholder="Kontots alias hos förvaltaren">
                </div>
                
                <div class="formfield">
                    <label for="note">Note</label>
                    <textarea id="note" name="note" rows="5"><?php echo $new_acc?null:$dta["note"]?></textarea>
                </div>

            </fieldset>
            <fieldset id="fldset_meta">
                
                <div class="formfield">
                    <label for="upper_limit">Upper limit</label>
                    <input type="text" id="upper_limit" name="upper_limit" value="<?php echo $new_acc?null:$dta["upper_limit"]?>" placeholder="Övre beloppsgräns"/>
                </div>
                
                <div class="formfield">
                    <label for="lower_limit">Lower limit</label>
                    <input type="text" id="lower_limit" name="lower_limit" value="<?php echo $new_acc?0:$dta["lower_limit"]?>" placeholder="Nedre beloppsgräns (negativt för kreditutrymme)"/>
                </div>

                <div class="formfield">
                    <label for="rate">Rate</label>
                    <input type="text" id="rate" name="rate" value="<?php echo $new_acc?1:$dta["rate"]?>" placeholder="Omvandlingsfaktor"/>
                </div>
            
            </fieldset>
            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="register" name="register" accesskey="'">Register</button>
                <?php
                    if(!$new_acc) {
                        ?>
                        <button type="button" class="formbuttons" accesskey = "-" onclick="delete_acc(<?php echo $dta['account_id'] ?>)">Delete</button>
                        <?php
                    } 
                ?>                
                <button class="formbuttons" type="button" accesskey="¨" onclick="window.location.href='lst_accounts.php'">Cancel</button>
            </fieldset>

        </form>

    <script>
        function delete_acc(account){

            const confirmed = window.confirm("Är du säker på att du vill ta bort kontot");
            
            if(confirmed) {
                window.location.href = "controller/account_del.php?id=" + account;
            }           
            
        }
    </script>

    </body>

</html>