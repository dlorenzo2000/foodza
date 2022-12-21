<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 22, 2022
 * Updated: Nov 22, 2022 
 * Purpose: Creates a small captch textfield that needs to be entered in order
 *          for a comment to be posted.
 ******************************************************************************/
     
        session_start();
        $str_random = md5(rand());
        $str = substr($str_random, 0, 6);
        $_SESSION['captcha'] = $str;

        $onScreenImage = imagecreate(100, 30);
        imagecolorallocate($onScreenImage, 220, 220, 255);
        $col = imagecolorallocate($onScreenImage, 0, 0, 0);
        imagestring($onScreenImage, 29, 10, 2, $str, $col);     
        header('content: image/jpeg');   
        imagejpeg($onScreenImage);      
?>