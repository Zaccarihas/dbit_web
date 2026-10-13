<?php
require "../config/db_ds.php";

try {
    $db = new PDO($dsn, $usr, $pwd);
} catch (PDOException $e) {
    echo $e->getMessage();
}

return $db

?>
