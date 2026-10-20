<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/error_reporting.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_db_manager.php";
//require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_datatransfer_manager.php";
//require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/controller/cls_associate.php";

// Connect to database
$dbm = new DB_manager($_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.txt");
$db = $dbm->connect();

// Get all nodes
$qry = $db->prepare("SELECT * FROM nodes WHERE type='Associate' ORDER BY title ASC");
$qry->execute();
$res = $qry->fetchAll(PDO::FETCH_ASSOC);


?>

<html>
    <head>

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        
        <title>Associate list</title>
        <link rel="stylesheet" href="/dbit/view/css/ass_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Associates</h1>
            <nav>
                <button onclick="window.location.href = '/dbit/view/frm_associate.php'">Ny aktör</button>
                <button onclick="window.location.href = '/dbit/index.php'">Tillbaka</button>
            </nav>
        </header>
        
        <table id="associates_list">
            <thead>
                <tr>
                    <th id="col_rec">Associate</th>
                </tr>
            <thead>
            <tbody>
                <?php foreach($res as $itm) { ?>
                <tr id="<?php echo 'item_'.$itm['id'] ?>">
                    <td><a href="/dbit/view/frm_associate.php?id=<?php echo $itm['id'] ?>"><?php echo $itm['title']?></a></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


