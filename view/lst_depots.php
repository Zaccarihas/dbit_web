<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";

$dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
$db = $dbm->connect();


// Get all depots
$qry = $db->prepare("SELECT id, prompt FROM vw_depots ORDER by prompt ASC");
DB_manager::run($qry);
$res = $qry->fetchAll(PDO::FETCH_ASSOC);

?>

<html>
    <head>

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        
        <title>Depot list</title>
        <link rel="stylesheet" href="/dbit/view/css/dep_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Depåer</h1>
            <nav>
                <button onclick="window.location.href = '/dbit/view/frm_depot.php'">Ny Depå</button>
                <button onclick="window.location.href = '/dbit/index.php'">Tillbaka</button>
            </nav>
        </header>
        
        <table id="recon_list">
            <thead>
                <tr>
                    <th id="col_rec">Depå</th>
                </tr>
            <tbody>
                <?php foreach($res as $itm) { ?>
                <tr id="<?php echo "item_".$itm["id"] ?>">
                    <td><a href="/dbit/view/frm_depot.php?id=<?php echo $itm["id"] ?>"><?php echo $itm["prompt"]?></a></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


