<?php
require "../controller/error_reporting.php";

// Connect to database
require "../controller/dbconnect.php";

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
        <link rel="stylesheet" href="view/ass_list.css">
    <head>
    <body>
        <header>
            <h1 id="main_head">Associates</h1>
            <nav>
                <button onclick="window.location.href = 'frm_associate.php'">Ny aktör</button>
                <button onclick="window.location.href = '../index.php'">Tillbaka</button>
            </nav>
        </header>
        
        <table id="associates_list">
            <thead>
                <tr>
                    <th id="col_rec">Associate</th>
                </tr>
            <tbody>
                <?php foreach($res as $itm) { ?>
                <tr id="<?php echo "item_".$itm["node_id"] ?>">
                    <td><a href="frm_associate.php?id=<?php echo $itm["node_id"] ?>"><?php echo $itm["title"]?></a></td>
                </tr>
                <?php } ?>
            </tbody>
        <table>
    </body>
</html>


