<?php
    //print_r($_GET);

    require "../controller/dbconnect.php";

    $dta = [
        "node_id" => null,
        "label" => null,
        "title" => null,
    ];

    if($_GET["id"]) {

        $qry = $db->prepare("SELECT * FROM nodes WHERE node_id=:id");
        $qry->bindValue(":id", (int)$_GET["id"]);
        $qry->execute();
        $dta = $qry->fetchAll(PDO::FETCH_ASSOC)[0];

    }

    $new_itm = $dta["node_id"] == null;

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
        <form id="frm_associate" method="post" action="../controller/associate_reg.php">
            <div id="node_id">
                <?php echo $new_itm?"NEW":$dta["node_id"]; ?>
                <input type="hidden" name="node_id" value="<?php echo $new_itm?null:$dta["node_id"]?>">
            </div>
            <div class="formfield">
                <label class="label"for="Title">Title</label>
                <input class="field" type="text" id="title" name="title" value="<?php 
                    echo $new_itm?null:$dta["title"];
                ?>" placeholder="Motpart" required/>
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
                <button class="formbuttons" type="button" onclick="window.location.href='lst_associates.php'">Cancel</button>
            </fieldset>
        </form>

    <script>
        function delete_itm(){

            //alert("Hit kom du");

            const confirmed = window.confirm("Är du säker på att du vill ta bort motparten");
            
            if(confirmed) {
                window.location.href = "controller/associate_del.php?id=" + <?php echo $dta["node_id"] ?>;
            }           
            
        }
    </script>

    </body>

</html>