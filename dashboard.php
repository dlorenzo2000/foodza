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
    if(!($usr_dat = CheckLogin($db))){
        LoginRedirect();
    }
?>

<h1>Dashboard</h1>
<br />
Mangage
<nav>
    <ul>
        <li class="nav-li"><a href="users.php">USERS</a></li>
        <li class="nav-li"><a href="reviews.php">REVIEWS</a></li>
        <li class="nav-li"><a href="category.php">CATEGORIES</a></li>
        <li class="nav-li"><a href="restaurant.php">RESTAURANTS</a></li>
    </ul> 
<br />
<br />
<br /> 
<?php require_once('footer.php'); ?>