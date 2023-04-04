<?php 
    if(!isset($_SESSION['logged_in'])){
        $_SESSION['logged_in'] = false;
    }

    $current_url = "$_SERVER[REQUEST_URI]";

    if($_SESSION['logged_in'] == false){
        $_SESSION['status'] = "You need to authenticate first";
        header('Location: login.php');
    }
?>