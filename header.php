<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 12, 2022
 * Updated: Dec 08, 2022 
 * Purpose: Header common to all pages.
 *****************************************************************************/

    session_start();
    require('connect.php');      
    require('library.php');  

    if(isset($_SESSION['username'])){
        $logout_link = "Logout";
        $my_reviews_link = "My Reviews";
        $dashboard = "Dashboard";        
        $usr_dat = CheckLogin($db);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <link rel="stylesheet" 
        href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" 
        integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" 
        crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css" type="text/css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Foodzagram version 3.0</title>
</head>
<body>   
<header>
    <div class="row top-nav-links align-item-center header-body">
        <div class="col-sm-4">
            <a href="index.php">
                <img src="images/foodzagram-logo-sm.png" class="top-logo"/>
            </a>
        </div>    
        <div class="col-sm-8">  
            <nav>      
                <ul>
                    <li class="top-nav-li">
                        <?php if(isset($_SESSION['username'])) 
                            echo "Hi " . strtoupper($_SESSION['username']) . "!";?>
                    </li>                             
                    <li class="top-nav-li"><a href="index.php">Main</a></li>     
                    <li class="top-nav-li">                        
                    <li class="top-nav-li"><a href="my_reviews.php?userid=<?=$usr_dat['userid']?>">
                        <?php if(isset($_SESSION['username']) && ($usr_dat = CheckLogin($db))) 
                           echo "My Reviews"; ?> </a>
                    </li> 
                    <li class="top-nav-li"><a href="dashboard.php">
                        <?php if(isset($_SESSION['username']) && ($usr_dat = CheckLogin($db)) 
                            && ($usr_dat['admin'] == 1)) echo "Dashboard"; ?> </a>
                    </li> 
                    <li class="top-nav-li">
                        <form action="search_results.php?search_words=" class="top-nav-search">
                            <input type="text" name="search_words">                        
                        <a href="search_results.php?search_words=">
                            <button class="btn btn-secondary">Search</button>
                        </a>
                        </form>
                    </li>   
                    <li class="top-nav-li">         
                        <?php if(isset($_SESSION['username'])): ?>
                            <button onclick="location.href='logout.php';" 
                                class="btn btn-secondary" >Logout </button>        
                        <?php else: ?>
                            <button onclick="location.href='login.php';" 
                                class="btn btn-secondary" >Login/Sign Up </button>  
                        <?php endif ?>  
                    </li>
                </ul>
            </nav>   
        </div>
    </div>            
</header> 
<hr>   
<div class="container">     
