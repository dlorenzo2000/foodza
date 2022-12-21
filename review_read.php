<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 20, 2022
 * Updated: Dec 04, 2022 
 * Purpose: Page for viewing whole review and comments posted by others
 ******************************************************************************/

    require_once('header.php');   
    
    if(isset($usr_dat))    
        $username = $usr_dat['first_name'];    
 
    if(isset($_POST['submit'])){
        if($_POST && empty(trim($_POST['comment']))){
            $comment_error = "* Comment form cannot be blank."; 
        }
        if($_POST && empty(trim($_POST['captcha']))){
            $captcha_error = "* Field cannot be blank."; 
        }

        
        if($_POST && !(empty(trim($_POST['comment']))) && !empty(trim($_POST['captcha']))){             
            $sessionCaptcha = $_SESSION['captcha'];
            if($usr_dat = CheckLogin($db)){
                    $userid = $usr_dat['userid'];                
                }        
                else{
                    $userid = 0;
                }
 
                $formCaptcha = $_POST['captcha'];
               
                if($sessionCaptcha == $formCaptcha){                    
                    $comment = trim(filter_input(INPUT_POST, 'comment'
                        , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                    $postid = filter_input(INPUT_POST, 'postid'
                        , FILTER_SANITIZE_FULL_SPECIAL_CHARS);                    
                
                    $qryComment = "INSERT INTO comment (comment, userid, postid)
                        VALUES (:comment, :userid, :postid)";

                    $stmComment = $db->prepare($qryComment);
                    $stmComment->bindValue(':comment', $comment);
                    $stmComment->bindValue(':userid', $userid);
                    $stmComment->bindValue(':postid', $postid);
                    $stmComment->execute();                  
                }
                else
                    $captcha_error = "* Could not verify you're human. Try try again."; 
            }
    }           
    
    // get the postid from the selected review to output to the page on load
    if(isset($_GET['postid'])){
        $postid = filter_input(INPUT_GET, 'postid'
            , FILTER_SANITIZE_NUMBER_INT);    
                     
        if(isset($usr_dat)){
            if($usr_dat['admin'] == 1){
                // query the user table for all posts
                $qryUser = "SELECT * FROM User";
                $active_status = "0 OR post.active = 1";
            }     

            $active_status = 1;      
        }
        else{
            // query the user table for only one user
            // $qryUser = "SELECT * FROM User WHERE username = $username LIMIT 1";    
            $active_status = 1;
        } 

        $qry = "SELECT post.postid, foodcategory.category_name, restaurant.restaurant_name
        , post.post_title, post.post_content, restaurant.restaurantid, post.restaurant_rating
        , post.created_date, post.modified_date, images.image_name, post.active, user.first_name
            FROM post                      
            INNER JOIN foodcategory ON foodcategory.categoryid = post.categoryid              
            INNER JOIN restaurant ON post.restaurantid = restaurant.restaurantid 
            LEFT JOIN images ON images.postid = $postid
            INNER JOIN user on user.userid = post.userid
            WHERE post.active = $active_status AND post.postid = $postid LIMIT 1";       

        $stm = $db->prepare($qry);           
        $stm->bindValue(':postid', $postid, PDO::PARAM_INT);
        $stm->execute();

        $dat = $stm->fetch();

        if(isset($usr_dat) && $usr_dat['admin'] == 1)
            $activeStatus = "";
        else
            $activeStatus = "AND comment.active = 1"; 

        $qryEditCategory = "SELECT * 
                            FROM foodcategory   
                                JOIN post 
                            WHERE post.categoryid = foodcategory.categoryid 
                                AND post.postid = $postid LIMIT 1";
     
        $stmEditCategory = $db->prepare($qryEditCategory);
        $stmEditCategory->execute();
        $datEditCategory = $stmEditCategory->fetch();             
        
        $qryComment = " SELECT * 
                        FROM comment 
                            LEFT JOIN user ON comment.userid = user.userid  
                        WHERE comment.postid = $postid 
                        $activeStatus
                        ORDER BY comment_date DESC";
         
        $stmComment = $db->prepare($qryComment);
        $stmComment->execute();             
     }     
?>  
 
<div class="row">
    <h1>Reading review</h1>
    <br />
    <br />           
    <br />           
    <h5 class="heading_inline"><?= $dat['restaurant_name'] 
        ?>- [<?= $datEditCategory['category_name'] ?> food]</h5>
    <h5>Title - <?=$dat['post_title']?></h5>
    <p>
        <?=$dat['post_content']?> 
        <br /> 
        <br /> 
        <?= $dat['restaurant_rating']?>/10 rating posted by <?=$dat['first_name']?> on    
        <?php $display_date = (($dat['created_date']) === ($dat['modified_date'])) ?
            date('F d, Y h:i A', strtotime($dat['created_date'])) : 
            date('F d, Y h:i A', strtotime($dat['modified_date'])); ?>  
        <?php if(isset($display_date)) echo $display_date; ?>     
        <br />
        <br />
        <br /> 
        <?php if(isset($dat['image_name'])): ?>
            Photos: <img src="uploads/<?=$dat['image_name']?>" 
                alt="<?=$dat['image_name'] ?>" />  
        <?php endif ?> 
    </p>
    <?php if($usr_dat = CheckLogin($db)): ?>  
        <form action="review_read.php?postid=<?= $dat['postid']?>" method="post">
            <input type="hidden" name="postid" value="<?=$dat['postid']?>"> 
            <label for="comment">
                Comment
            </label>
            <input type="text" size="125" name="comment" 
                value="<?php if(isset($_POST['comment'])): ?><?php echo $_POST['comment'];?><?php endif ?>">
            <span class="error-message">
            <?php if(isset($comment_error)) echo $comment_error; ?>
            </span> 
            <br />
            <br />
            Prove you are human: <input type="text" name="captcha"><img src="captcha.php">  
            <span class="error-message">
                <?php if(isset($captcha_error)) echo $captcha_error; ?>
            </span> 
            <br />
            <button type="submit" class="btn btn-secondary" id="submit" name="submit">Submit</button>         
            <br />
            <br />
            <br />  
        </form>  
    <?php endif ?>
</div>
<div class="row justify-content-center">
<br />
<br />
<?php if($stmComment->rowCount() > 0): ?>
    <ul>
        <?php while($datComment = $stmComment->fetch()): ?>
            <hr>
            <li>
                <?php if(strtotime($datComment['modified_date']) 
                    > strtotime($datComment['comment_date']))
                    $modified = "Updated on " . date('F d, Y h:i A'
                        , strtotime($datComment['modified_date']));
                ?>
                <p><?= $datComment['comment'] ?></p>
                <?php if($datComment['active'] == 1): ?>
                    [Active] 
                <?php else: ?> 
                    [In-active]
                <?php endif?>
                Comment posted by 
                <?php if($datComment['userid'] == 0 ): ?>
                    Anonymous user
                <?php else: ?>
                    <?=$datComment['first_name']; ?>
                <?php endif ?> 
                <br />
                on <?= date('F d, Y h:i A', strtotime($datComment['comment_date'])) ?>                    
                <br />               
                <span><?php if(isset($modified)) echo $modified; ?></span>   
                <?php if(isset($usr_dat['admin']) && ($usr_dat['admin'] == 1)) : ?>
                    <a href="comments_edit.php?commentid=<?=$datComment['commentid']?>
                        &postid=<?=$postid?>">EDIT COMMENT </a> 
                <?php endif ?>                    
                </li>
            <hr>          
        <?php endwhile ?>               
        </ul>
<?php else: ?>
    <p>
        There are no comments yet. 
    </p>
<?php endif ?>    
</div>    
<?php require_once('footer.php') ?>