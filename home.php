<?php
/******************************************************************************
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Project
 * Created: Nov 12, 2022
 * Updated: Nov 17, 2022 
 * Purpose: Home page for registered users that are logged in.
 *****************************************************************************/

    require_once('header.php');
    
    $usr_dat = CheckLogin($db);
?>

<div class="index-body">
    <p>            
        Welcome back <?= $usr_dat['first_name'] ?>.
    </p>    
</div>
<?php require_once('footer.php'); ?> 