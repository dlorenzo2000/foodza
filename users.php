<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 18, 2022
 * Updated: Nov 18, 2022 
 * Purpose: Administrators access the dashboard to manage users and pages.
 ******************************************************************************/

    require_once('header.php'); 
    // if the user visits this page and isn't logged in, then redirect
    if(!($usr_dat = CheckLogin($db)) && $usr_dat['admin'] != 1){
        LoginRedirect();
    }

    $qry = "SELECT * FROM user ORDER BY first_name";
    $stm = $db->prepare($qry);
    $stm->execute();
?>

<h1>Manage users</h1>
<br />
<table>
    <?php while($dat = $stm->fetch()): ?>   
        <ul>
            <li class="tbl-li">[<?= $dat['first_name'] ?></li>         
            <li class="tbl-li"><?= $dat['last_name'] ?>]</li>         
            <li class="tbl-li">[<?= $dat['email'] ?>]</li>   
            <li class="tbl-li">[Username: <?= strtoupper($dat['username']) ?>]</li>  
            <li class="tbl-li">[Active: <?= $dat['active'] ?>]</li>
            <li class="tbl-li">
                <a href="user_edit.php?userid=<?= $dat['userid']?>">EDIT</a></li>
        </ul>
    <?php endwhile ?>
</table>
<?php require_once('footer.php');