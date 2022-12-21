<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 15, 2022
 * Updated: Nov 30, 2022 
 * Purpose: Handles the add/remove images process.
 *****************************************************************************/

    require_once('header.php');

     // checks to see if the user is logged in and redirects to login if not
    if(!($usr_dat = CheckLogin($db))){
        LoginRedirect();
    }
    else{
        if(isset($_GET['postid'])){
            $postid = filter_input(INPUT_GET, 'postid'
                , FILTER_SANITIZE_NUMBER_INT);    
                
            $qry = "SELECT post.postid, images.image_name 
                    FROM post                      
                    INNER JOIN images ON images.postid = $postid
                    WHERE post.active = 1 AND post.postid = $postid";

            $stm = $db->prepare($qry);            
            $stm->execute();  

            // This function checks to see if a checkbox on the form is 
            // selected. It takes two parameters, the name of the check_box
            // and the value passed
            function IsChecked($checkname, $value){
                if(!empty($_POST[$checkname])){
                    foreach($_POST[$checkname] as $checked){
                        if($checked == $value){
                            return true;
                        }
                    }
                }
                return false;
            }

            if($_POST){
                $adslkjasdf->execute();
                $postid = filter_input(INPUT_POST, 'postid'
                , FILTER_SANITIZE_NUMBER_INT);    
                
            $qry = "SELECT post.postid, images.image_name 
                    FROM post                      
                    INNER JOIN images ON images.postid = $postid
                    WHERE post.active = 1 AND post.postid = $postid";

            $stm = $db->prepare($qry);            
            $stm->execute();  
                while($datImage = $stm->fetch()){
                    if(IsChecked($_POST($datImage['image_name']), $_datImage['image_name'] )){
                        
                    }
                }           
            }
        }
    }
?>

<form method="post" action="images_edit.php?postid=<?=$dat['postid']?>"  enctype="multipart/form-data">   
    <input type="hidden" name="postid" value="<?=$dat['postid']?>">     
    <br /> 
    <?php while($dat = $stm->fetch()): ?>
        <img src="uploads/<?=$dat['image_name']?>" class="thumb" />                   
        <input type="checkbox" name="<?=$dat['image_name']?>" value="<?=$dat['image_name']?>">
        <br />
    <?php endwhile ?>  
    <br />
    <button type="submit" class="btn btn-secondary" name="delete_image">Delete image</button> 
    <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button> 
</form>
<?php require_once('footer.php'); ?>