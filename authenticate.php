<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 22, 2022
 * Updated: Nov 22, 2022 
 * Purpose: Logged in users can update food categories.
 ******************************************************************************/

    define('ADMIN_LOGIN', 'thor');
    define('ADMIN_PASSWORD', 'godofthunder');

    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
        || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN)
        || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)){
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Our Blog"');
        exit("Access Denied: Username and password required.");
    }
?>