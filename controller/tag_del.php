<?php
    session_start();

    unset($_SESSION["transaction"]["entries"][$_GET["ent_id"]]["tags"][$_GET["tag_id"]]);
           
    header("Location: /dbit/frm_entry.php?ent_id=".$_GET["ent_id"]);
    die();
