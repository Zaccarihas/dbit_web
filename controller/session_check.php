<?php    
    
    if(session_status() == PHP_SESSION_ACTIVE){
        echo "Session Active";
    } else {
        echo "No Active Session ... activating session";
        session_start();
    }

    if(!empty($_SESSION)){
        echo"<pre>";
        print_r($_SESSION);
        echo"</pre>";
    } else {
        echo "No session variables set";
    }
    