<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/dbit/config/db_ds.php";

try {
    $db = new PDO($dsn, $usr, $pwd);
} catch (PDOException $e) {
    echo $e->getMessage();
}

return $db

?>
