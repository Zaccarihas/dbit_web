<?php
require "controller/error_reporting.php";

// Connect to database
require "dbconnect.php";

// Get all nodes
$qry = $db->prepare("SELECT * FROM account_summary");
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
        
        <title>Account list</title>
        <link rel="stylesheet" href="view/acc_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Kontolista</h1>
            <nav>
                <button onclick="window.location.href = 'frm_account.php'">Nytt konto</button>
                <button onclick="window.location.href = 'index.php'">Tillbaka</button>
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
                <tr id="<?php echo "item_".$node["account"] ?>">
                    <td><?php echo $node["label"]?></td>
                    <td><a href="frm_account.php?id=<?php echo $node["account"] ?>"><?php echo $node["title"]?></a></td>
                    <td><?php echo $node["reconciled"]?></td>
                    <td><?php echo $fmt->formatCurrency($node["recon_total"]??0,"SEK")?></td>
                    <td><?php echo $fmt->formatCurrency($node["current_total"]??0,"SEK")?></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


