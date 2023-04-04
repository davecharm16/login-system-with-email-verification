<?php 
    session_start();

    if(isset($_POST['logout-btn'])){
        if(isset($_SESSION['logged_in'])){
            $_SESSION['logged_in'] = false;
            header('Location: login.php'); 
        }
        else{
            header('Location: index.php'); 
        }
    }
    else{
       header('Location: index.php'); 
    }

?>