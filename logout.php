<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 12, 2022
 * Updated: Nov 12, 2022 
 * Purpose: Manages the logout procedure.
 *****************************************************************************/

    session_start();

    if(isset($_SESSION['username'])){
       session_destroy();
    }

    header("Location: index.php");
    die;
?>