<?php
    //print_r($_GET);

    require "dbconnect.php";

    $dta = [
        "node_id" => null,
        "label" => null,
        "title" => null,
        "parent" => null
    ];

    $sql = <<< SQL
        SELECT node_id, label, title
        FROM nodes
        WHERE type = "Category"
        ORDER BY label ASC
    SQL;

    $qry = $db->prepare($sql);
    $qry->execute();
    $parents = $qry->fetchAll(PDO::FETCH_ASSOC);

    if($_GET["id"]) {

        $qry = $db->prepare("SELECT * FROM nodes WHERE node_id=:id");
        $qry->bindValue(":id", (int)$_GET["id"]);
        $qry->execute();
        $dta = $qry->fetchAll(PDO::FETCH_ASSOC)[0];

    }

    $new_itm = $dta["node_id"] == null;

    //print_r($dta);
?>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>Recon form</title>
        <link rel="stylesheet" href="view/cat_form.css">
    <head>

    <body>
        <form id="frm_category" method="post" action="controller/category_reg.php">
            <div id="node_id">
                <?php echo $new_itm?"NEW":$dta["node_id"]; ?>
                <input type="hidden" name="node_id" value="<?php echo $new_itm?null:$dta["node_id"]?>">
            </div>
            <div class="formfield">
                <label class="label"for="label">Label</label>
                <input class="field" type="text" id="label" name="label" value="<?php 
                    echo $new_itm?null:$dta["label"];
                ?>" placeholder="Etikett" required/>
            </div>
            <div class="formfield">
                <label class="label" for="title">Note</label>
                <input class="field" type="text" id="title" name="title" value="<?php 
                    echo $new_acc?null:$dta["title"]
                ?>" placeholder="Kategori"/>
            </div>
            <div class="formfield">
                <label class="label" for="parent">Parent</label>
                <select class="field" id="parent" name="parent">
                    <option value=""<?php echo $new_itm? "selected":"" ?>>Välj överställt konto</option>
                    <?php 
                    foreach($parents as $cat){ ?>
                        <option 
                            value="<?php echo $cat["node_id"] ?>" <?php 
                                echo $dta["parent"] == $cat["node_id"]?"selected":"" 
                            ?>><?php echo $cat["label"]." - ".$cat["title"] ?></option>
                    <?php } ?>
                </select>
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
                <button class="formbuttons" type="button" onclick="window.location.href='lst_categories.php'">Cancel</button>
            </fieldset>

        </form>

    <script>
        function delete_itm(){

            //alert("Hit kom du");

            const confirmed = window.confirm("Är du säker på att du vill ta bort kategorin");
            
            if(confirmed) {
                window.location.href = "controller/category_del.php?id=" + <?php echo $dta["node_id"] ?>;
            }           
            
        }
    </script>

    </body>

</html>