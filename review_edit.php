<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 15, 2022
 * Updated: Dec 04, 2022 
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
        }
        else{
            $postid = filter_input(INPUT_POST, 'postid'
            , FILTER_SANITIZE_NUMBER_INT);  
        }   
                
        $qry = "SELECT post.postid, foodcategory.category_name, restaurant.restaurant_name
            , post.post_title, post.post_content, restaurant.restaurantid, post.restaurant_rating
            , post.created_date, post.modified_date, images.image_name, post.active
            , images.image_name_thumb
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
        
        $qryEditRestaurant = "SELECT * 
        FROM restaurant JOIN post on post.restaurantid = restaurant.restaurantid
        WHERE post.postid = $postid LIMIT 1";

        $stmEditRestaurant = $db->prepare($qryEditRestaurant);
        $stmEditRestaurant->execute();
        $datEditRestaurant = $stmEditRestaurant->fetch();          
      
        if($_POST && empty(trim($_POST['post_title'])))
            $title_error = "* Title field cannot be blank.";

        if($_POST && empty(trim($_POST['post_content'])))
            $post_content_error = "* Post text area cannot be blank.";

        if($_POST && empty($_POST['restaurant_rating']))
            $rating_error = "* You must rate the restaurant from 1 to 10.";

        if($_POST && empty($_POST['restaurantid']))
            $restaurant_error = "* You must select the restaurant name.";

        if($_POST && empty($_POST['categoryid']))
            $category_error = "* You must select a cateogry.";

        if($_POST && !empty(trim($_POST['post_title'])) 
            && !empty(trim($_POST['post_content'])) 
            && !empty($_POST['restaurant_rating'])
            && !empty($_POST['restaurantid']) 
            && !empty($_POST['categoryid'])){

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
                header("Location: my_reviews.php");
                exit;
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

            if ($image_upload_detected) {         
                $image_filename = date("Y_m_d_H_i").'_'.$_FILES['image']['name'];                  
        
                $temporary_image_path = $_FILES['image']['tmp_name'];
        
                $new_image_path = file_upload_path($image_filename);

                $fileName = pathinfo($image_filename, PATHINFO_FILENAME);
        
                if (file_is_an_image($temporary_image_path, $new_image_path)){ 
                    move_uploaded_file($temporary_image_path, $new_image_path);

                    // get the extension of the file name            
                    $ext = pathinfo($image_filename, PATHINFO_EXTENSION);
                                  
                    $image_name = new \Gumlet\ImageResize($new_image_path);  

                    // create medium size copy of image 700px width
                    $image_name->resizeToWidth(700);
    
                    $newName = 'uploads\\'.$fileName.'_medium.'.$ext; 
                    $image_name->save($newName); 
                                
                    // create thumbnail size of image 75px width 
                    $image_thumbnail = new \Gumlet\ImageResize($new_image_path);
                    $image_thumbnail->resizeToWidth(75);
    
                    // create copied file with thumbnail name
                    $newThumbName = 'uploads\\'.$fileName.'_thumb.'.$ext; 
                    $image_thumbnail->save($newThumbName); 

                    $image_medium = $fileName.'_medium.'.$ext; 
                    $image_thumb = $fileName.'_thumb.'.$ext; 
                   
                    $qryImage = "INSERT INTO images (image_name, image_name_thumb, postid)
                                    VALUES(:image_name, :image_name_thumb, :postid)";

                    $stmImage = $db->prepare($qryImage);
                    $stmImage->bindValue(':image_name', $image_medium, PDO::PARAM_STR);
                    $stmImage->bindValue(':image_name_thumb', $image_thumb, PDO::PARAM_STR);
                    $stmImage->bindValue(':postid', $postid, PDO::PARAM_INT);
                    $stmImage->execute();

                    // remove the original sized photo
                    $originalPath = "uploads/".$image_filename;
                    unlink($originalPath);
                      
                    header("Location: my_reviews.php");
                    exit; 
                }
            }    
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
        <input type="text" name="post_title" value
            ="<?php if(isset($dat)) echo $dat['post_title'];
                elseif(isset($_POST['post_title'])) echo($_POST['post_title']); ?>">
        <span class="error-message"><?php if(isset($title_error)) 
            echo $title_error; ?>
        </span>   
        <br />
        <textarea name="post_content" rows="10" cols="94" value
            ="<?php if(isset($dat)) echo $dat['post_content'];
                elseif(isset($_POST['post_content'])) 
                echo($_POST['post_content']); ?>"><?php if(isset($dat)) 
                echo $dat['post_content'];
                elseif(isset($_POST['post_content'])) echo($_POST['post_content']); ?>
        </textarea>
        <span class="error-message"><?php if(isset($post_content_error)) 
            echo $post_content_error; ?>
        </span> 
        <br />
        <label for="restaurant_rating">Rating </label>
        <select name="restaurant_rating"> 
            <option selected value="<?= $dat['restaurant_rating']?>">
                <?= $dat['restaurant_rating']?>
            </option>
            <?php for($i=1; $i<=10; $i++): ?>
                <option value = "<?=$i ?>">
                    <?=$i ?>
                </option>
            <?php endfor ?>
            <span class="error-message"><?php if(isset($rating_error)) echo $rating_error; ?></span>   
        </select> 
        <label for="restaurantid">Restaurant</label> 
        <select name="restaurantid"> 
            <?php if($stmRestaurant->rowCount() > 0): ?>    
                <option selected value="<?= $datEditRestaurant['restaurantid'] ?>">
                    <?= $datEditRestaurant['restaurant_name'] ?>
                </option>  
                <?php while($datRestaurant = $stmRestaurant->fetch()): ?>
                    <option value="<?= $datRestaurant['restaurantid'] ?>">
                        <?=$datRestaurant['restaurant_name'] ?>
                    </option>                    
                <?php endwhile ?>
            <?php endif ?>
        </select> 
        <a href="restaurant.php">Add restaurant</a>
        <br />
        <label for="categoryid">Category </label>    
        <select name="categoryid"> 
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
                >Delete image
            </button> 
        <?php endif ?>    
        <br />
        <?php if(!(isset($dat['image_name']))): ?>
            <label for="image">Upload food image:</label> 
            (gif, jpg, jpeg, png files only)
            <input type="file" name="image" id="image" /> 
        <?php endif?>
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