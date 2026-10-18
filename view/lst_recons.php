<?php
require "controller/error_reporting.php";

// Connect to database
$db = require "dbconnect.php";

// Get all nodes
$qry = $db->prepare("SELECT * FROM vw_recons");
$qry->execute();
$res = $qry->fetchAll(PDO::FETCH_ASSOC);

// Create a formatter for the currency columns
$fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

?>

<html>
    <head>

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        
        <title>Recon list</title>
        <link rel="stylesheet" href="view/rec_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Avstämningar</h1>
            <nav>
                <button onclick="window.location.href = 'frm_recon.php'">Ny avstämning</button>
                <button onclick="window.location.href = 'index.php'">Tillbaka</button>
            </nav>
        </header>
        
        <table id="recon_list">
            <thead>
                <tr>
                    <th id="col_rec">Reconciled</th>
                    <th id="col_acc">Account</th>
                    <th id="col_note">Note</th>
                    <th id="col_tot">Total</th>
                </tr>
            <tbody>
                <?php foreach($res as $itm) { ?>
                <tr id="<?php echo "item_".$itm["recon_id"] ?>">
                    <td><a href="frm_recon.php?id=<?php echo $itm["recon_id"] ?>"><?php echo $itm["rec_date"]?></a></td>
                    <td><?php echo $itm["label"]." - ".$itm["title"]?></td>
                    <td><?php echo $itm["note"]?></td>
                    <td><?php echo $fmt->formatCurrency($itm["total"]??0,"SEK")?></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


