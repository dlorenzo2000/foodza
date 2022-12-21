<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 15, 2022
 * Updated: Nov 30, 2022 
 * Purpose: Handles the update review process.
 *****************************************************************************/

    require_once('header.php');
    
    // checks to see if the user is logged in and redirects to login if not
    if(!($usr_dat = CheckLogin($db))){
        LoginRedirect();
    }
    else{
        // populate the drop down boxes with the following code
        $username = $_SESSION['username'];

        if($usr_dat['admin'] == 1){
            // query the user table for all posts
            $qryUser = "SELECT * FROM User";
            $active_status = "0 OR post.active = 1";
        }
        else{
            // query the user table for only one user
            $qryUser = "SELECT * FROM User WHERE username = $username LIMIT 1";    
            $active_status = 1;
        } 
        
        // query the restaurant table
        $qryRestaurant = "SELECT * FROM Restaurant ORDER BY restaurant_name ASC";

        // query the foodcategory table
        $qryCategory = "SELECT * FROM foodcategory ORDER BY category_name ASC";

        $stmUser = $db->prepare($qryUser);
        $stmRestaurant = $db->prepare($qryRestaurant);
        $stmCategory = $db->prepare($qryCategory);

        $stmUser->execute();
        $stmRestaurant->execute();
        $stmCategory->execute();
 
        // get the postid from the selected review to output to the page on load
        if(isset($_GET['postid'])){
            $postid = filter_input(INPUT_GET, 'postid'
                , FILTER_SANITIZE_NUMBER_INT);    
                
            $qry = "SELECT post.postid, foodcategory.category_name, restaurant.restaurant_name
                , post.post_title, post.post_content, restaurant.restaurantid, post.restaurant_rating
                , post.created_date, post.modified_date, images.image_name, post.active
                    FROM post                      
                    INNER JOIN foodcategory ON foodcategory.categoryid = post.categoryid              
                    INNER JOIN restaurant ON post.restaurantid = restaurant.restaurantid 
                    LEFT JOIN images ON images.postid = $postid
                    WHERE post.active = $active_status AND post.postid = $postid LIMIT 1";

            $stm = $db->prepare($qry);            
            $stm->execute();

            $dat = $stm->fetch();

            $qryImage ="SELECT * FROM images WHERE images.postid = $postid LIMIT 5";
            $stmImage = $db->prepare($qryImage);
            $stmImage->execute();           

            $qryEditCategory = "SELECT * FROM foodcategory JOIN post 
                WHERE post.categoryid = foodcategory.categoryid 
                AND post.postid = $postid LIMIT 1";

            $stmEditCategory = $db->prepare($qryEditCategory);
            $stmEditCategory->execute();
            $datEditCategory = $stmEditCategory->fetch();            
        }         

        if($_POST && $_POST['delete_image']){
            $postid = filter_input(INPUT_POST, 'postid'
            , FILTER_SANITIZE_NUMBER_INT);   
             
            $qry = "SELECT * FROM images WHERE images.postid = $postid LIMIT 5";
            $stm = $db->prepare($qry);
            $stm->execute();    

            $stm = $db->prepare($qry);            
            $stm->execute();

            $dat = $stm->fetch();
           
            $path = "uploads/".$dat['image_name'];
            unlink($path);     
 
            $qryDelete="DELETE FROM images WHERE postid = $postid LIMIT 1";
            $stmDelete=$db->prepare($qryDelete);
            $stmDelete->execute(); 
        }   

        if($_POST && ($_POST['delete'])){ 
            $postid = filter_input(INPUT_POST, 'postid'
                , FILTER_SANITIZE_NUMBER_INT);
            $qry="UPDATE post 
                  SET active = 0 
                  WHERE postid = $postid"; 

            $stm=$db->prepare($qry);        
            $stm->execute(); 

            header("Location: my_reviews.php");
            exit;
        }

        if($_POST && ($_POST['activate'])){ 
            $postid = filter_input(INPUT_POST, 'postid'
                , FILTER_SANITIZE_NUMBER_INT);

            $qry="UPDATE post 
                  SET active = 1 
                  WHERE postid = $postid";
                    
            $stm=$db->prepare($qry);        
            $stm->execute(); 

            header("Location: my_reviews.php");
            exit;
        }        

        if($_POST){
            $postid = filter_input(INPUT_POST, 'postid'
                , FILTER_SANITIZE_NUMBER_INT);
            $post_title = trim(filter_input(INPUT_POST, 'post_title'
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $post_content = trim(filter_input(INPUT_POST, 'post_content'
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $restaurant_rating 
                = (int)(filter_input(INPUT_POST, 'restaurant_rating'
                    , FILTER_SANITIZE_NUMBER_INT));
            $restaurantid 
                = (int)(filter_input(INPUT_POST, 'restaurantid'
                    , FILTER_SANITIZE_NUMBER_INT));
            $categoryid 
                = (int)(filter_input(INPUT_POST, 'categoryid'
                    , FILTER_SANITIZE_NUMBER_INT));
     
            $qryPost = "UPDATE post 
                        SET post_title=:post_title, post_content=:post_content
                            , restaurant_rating=:restaurant_rating
                            , restaurantid=:restaurantid, categoryid=:categoryid
                        WHERE postid=:postid";

            $stmPost = $db->prepare($qryPost);

            $stmPost->bindvalue(':postid', $postid, PDO::PARAM_INT); 
            $stmPost->bindValue(':post_title', $post_title, PDO::PARAM_STR);
            $stmPost->bindValue(':post_content', $post_content, PDO::PARAM_STR);
            $stmPost->bindvalue(':restaurant_rating', $restaurant_rating, PDO::PARAM_INT);
            $stmPost->bindvalue(':restaurantid', $restaurantid, PDO::PARAM_INT);
            $stmPost->bindvalue(':categoryid', $categoryid, PDO::PARAM_INT);
            
            $stmPost->execute();

            //////////////////////////////////////////////////////////////////////////////////////////////////////// 
            function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
                $current_folder = dirname(__FILE__);
                
                $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
                
                return join(DIRECTORY_SEPARATOR, $path_segments);
            }
        
            function file_is_an_image($temporary_path, $new_path) {                
                $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png', 'application/pdf'];
                $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png', 'pdf'];
                
                $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
                $actual_mime_type        = mime_content_type($temporary_path);
                
                $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
                $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
                
                return $file_extension_is_valid && $mime_type_is_valid; 
            }
            
            $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
            $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

            // if ($image_upload_detected) {         
                
                $image_filename = date("Y_m_d_H_i")."".$_FILES['image']['name'];  
        
                $temporary_image_path = $_FILES['image']['tmp_name'];
        
                $new_image_path = file_upload_path($image_filename);
        
                if (file_is_an_image($temporary_image_path, $new_image_path)) 
                {
                    $qryImage = "INSERT INTO images (image_name, postid)
                                    VALUES(:image_name, :postid)";

                    $stmImage = $db->prepare($qryImage);
                    $stmImage->bindValue(':image_name', $image_filename, PDO::PARAM_STR);
                    $stmImage->bindValue(':postid', $postid, PDO::PARAM_INT);
                    $stmImage->execute();

                    move_uploaded_file($temporary_image_path, $new_image_path);           
                }
            // }    
            /////////////////////////////////////////////////////////////////////////////////////////////////////

            header("Location: my_reviews.php");
            exit;
        }        
    }
?> 
 
<div class="row justify-content-center">
    <h1>EDIT review</h1>
    <form method="post" action="review_edit.php" enctype="multipart/form-data"> 
        <input type="hidden" name="postid" value="<?=$dat['postid']?>">
        <label for="post_title">Title</label>        
        <input type="text" name="post_title" value="<?=$dat['post_title']?>">
        <br />
        <textarea name="post_content" rows="10" cols="94"><?=$dat['post_content']?>
        </textarea>
        <br />
        <label for="restaurant_rating">Rating </label>
        <select name="restaurant_rating">
            <option hidden disabled selected value> 
                -- select an option -- 
            </option>
            <option selected value="<?= $dat['restaurant_rating']?>">
                <?= $dat['restaurant_rating']?>
            </option>
            <?php for($i=1; $i<=10; $i++): ?>
                <option value = "<?=$i ?>">
                    <?=$i ?>
                </option>
            <?php endfor ?>
        </select> 
        <label for="restaurantid">Restaurant</label>
        <select name="restaurantid">
            <option hidden disabled selected value> 
                -- select an option -- 
            </option>             
            <?php if($stmRestaurant->rowCount() > 0): ?>                
                <option selected value="<?= $dat['restaurantid'] ?>">
                    <?= $dat['restaurant_name'] ?>
                </option>      
                <option value="<?= $dat['restaurantid'] ?>">
                    <?= $dat['restaurant_name'] ?> 
                </option> 
            <?php endif ?>
        </select> 
        <a href="restaurant.php">Add restaurant</a>
        <br />
        <label for="categoryid">Category </label>    
        <select name="categoryid">
            <option hidden disabled selected value>
                    -- select an option -- 
            </option>
            <?php if($stmCategory->rowCount() > 0): ?>
                <option selected value="<?= $datEditCategory['categoryid'] ?>">
                    <?= $datEditCategory['category_name'] ?> 
                </option>
                <?php while($datCategory = $stmCategory->fetch()): ?>
                    <option value="<?= $datCategory['categoryid'] ?>">
                        <?=$datCategory['category_name'] ?>
                    </option>                    
                <?php endwhile ?>
            <?php endif ?>            
        </select> 
        <a href="category.php">Add category</a> 
        <br />
        <?php if((isset($dat['image_name']))): ?>
            Photos: 
            <?php while ($datImage = $stmImage->fetch()): ?>
                <img src="uploads/<?=$datImage['image_name']?>" 
                    class="img-view" alt="<?=$datImage['image_name'] ?>" />                      
            <?php endwhile ?>              
            <button type="submit" class="btn btn-secondary" 
                name="delete_image" value="delete_image"
                onclick="return confirm('Confirm delete this image?')"
                >Delete image</button> 
        <?php endif ?>    
        <br />
        <label for="image">Upload food image:</label> 
        <input type="file" name="image" id="image" /> 
        <br />
        <button type="submit" class="btn btn-secondary" id="submit">Save</button>  
        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
        <?php if(isset($dat['active']) && $dat['active']): ?>
            <button type="submit" class="btn btn-secondary" name="delete" value="delete"        
                onclick="return confirm('Confirm de-activation?')">De-activate</button>
        <?php else: ?>
            <button type="submit" class="btn btn-secondary" name="activate" value="activate"        
                onclick="return confirm('Confirm re-activation?')">Re-activate</button>
        <?php endif ?>
        <br />
        <br />  
    </form>  
</div> 