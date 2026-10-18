<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";

$debug = false;

// Connect to database
$dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
$db = $dbm->connect();


// Get all nodes
$qry = $db->prepare("SELECT * FROM vw_accounts ORDER BY label");
$qry->execute();
$res = $qry->fetchAll(PDO::FETCH_ASSOC);

if($debug) {
    echo "<hr><pre>"; print_r($res); echo"</pre>"; die;
}


// Create a formatter for the currency columns
$fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

?>

<html>
    <head>

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        
        <title>Account list</title>
        <link rel="stylesheet" href="/dbit/view/css/acc_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Kontolista</h1>
            <nav>
                <button onclick="window.location.href = '/dbit/view/frm_account.php'">Nytt konto</button>
                <button onclick="window.location.href = '/dbit/index.php'">Tillbaka</button>
            </nav>
        </header>
        
        <table id="account_list">
            <thead>
                <tr>
                    <th id="col_label">Label</th>
                    <th id="col_title">Title</th>
                    <th id="col_recon">Reconciled</th>
                    <th id="col_rectot">Recon Tot</th>
                    <th id="col_total">Total</th>
                </tr>
            <tbody>

                <?php foreach($res as $node) { ?>
                <tr id="<?php echo "item_".$node["id"] ?>">
                    <td><?php echo $node["label"]?></td>
                    <td><a href="/dbit/view/frm_account.php?id=<?php echo $node["id"] ?>"><?php echo $node["title"]?></a></td>
                    <td><?php echo $node["reconciled"]?></td>
                    <td><?php echo $fmt->formatCurrency($node["rec_total"]??0,"SEK")?></td>
                    <td><?php echo $fmt->formatCurrency($node["cur_total"]??0,"SEK")?></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


