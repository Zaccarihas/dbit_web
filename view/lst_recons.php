<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_tbl_recon.php";

// Connect to database
$dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
$db = $dbm->connect();

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
        <link rel="stylesheet" href="/dbit/view/css/rec_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Avstämningar</h1>
            <nav>
                <button onclick="window.location.href = '/dbit/view/frm_recon.php'">Ny avstämning</button>
                <button onclick="window.location.href = '/dbit/index.php'">Tillbaka</button>
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
                <tr id="<?php echo "item_".$itm["id"] ?>">
                    <td><a href="/dbit/view/frm_recon.php?id=<?php echo $itm["id"] ?>"><?php echo $itm["date"]?></a></td>
                    <td><?php echo $itm["prompt"]?></td>
                    <td><?php echo $itm["note"]?></td>
                    <td><?php echo $fmt->formatCurrency($itm["total"]??0,"SEK")?></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


