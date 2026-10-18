<?php
require "../controller/error_reporting.php";

// Connect to database
$db = require "../controller/dbconnect.php";

// Get all nodes
$qry = $db->prepare("SELECT * FROM transactions ORDER BY transaction_date DESC");
$qry->execute();
$res = $qry->fetchAll(PDO::FETCH_ASSOC);

// Create a formatter for the currency columns
$fmt = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);

$transaction_controller = "../controller/ctrl_transaction_pre.php";

?>

<html>
    <head>

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        
        <title>Transaction list</title>
        <link rel="stylesheet" href="css/trn_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Transaktionslista</h1>
            <nav>
                <button onclick="window.location.href = '<?php echo $transaction_controller ?>'">Ny transaktion</button>
                <button onclick="window.location.href = '../index.php'">Tillbaka</button>
            </nav>
        </header>
        
        <table id="transaction_list">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Subject</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($res as $itm){ ?>
                <tr id="<?php echo "item_".$itm["transaction_id"] ?>">
                    <td><?php echo $itm["transaction_date"]?></td>
                    <td><a href="<?php echo $transaction_controller ?>?id=<?php echo $itm["transaction_id"] ?>"><?php echo $itm["subject"]?></a></td>
                    <td><?php echo $fmt->formatCurrency($itm["target_total"]??0,"SEK")?></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


