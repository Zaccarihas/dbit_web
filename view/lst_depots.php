<?php
require "controller/error_reporting.php";

// Connect to database
$db = require "dbconnect.php";

// Get all nodes
$qry = $db->prepare("SELECT * FROM nodes WHERE type='Depot' ORDER BY label ASC");
$qry->execute();
$res = $qry->fetchAll(PDO::FETCH_ASSOC);


?>

<html>
    <head>

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        
        <title>Depot list</title>
        <link rel="stylesheet" href="view/dep_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Depåer</h1>
            <nav>
                <button onclick="window.location.href = 'frm_depot.php'">Ny Depå</button>
                <button onclick="window.location.href = 'index.php'">Tillbaka</button>
            </nav>
        </header>
        
        <table id="recon_list">
            <thead>
                <tr>
                    <th id="col_rec">Depå</th>
                </tr>
            <tbody>
                <?php foreach($res as $itm) { ?>
                <tr id="<?php echo "item_".$itm["node_id"] ?>">
                    <td><a href="frm_depot.php?id=<?php echo $itm["node_id"] ?>"><?php echo $itm["label"]." - ".$itm["title"]?></a></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


