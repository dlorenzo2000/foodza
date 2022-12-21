<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 13, 2022
 * Updated: Nov 30, 2022 
 * Purpose: Reviews page that contains food blogs written by registered users.
 *          This can be viewed by all visitors to this page.
 *****************************************************************************/

    require_once('header.php');

    if($usr_dat = CheckLogin($db)){
        if($usr_dat['admin'] == 1){
            // if all posts will be visible when this WHERE
            // claus is exercised for admins
            $and_active = "";   
        }
        else{            
            $and_active = "AND post.active = 1";
        }           
    }
    else{
        $and_active = "AND post.active = 1";
    }

    $sortCriteria = "";

    if($_POST){
        if(isset($_POST['sort-reviews'])){
            if($_POST['sort-reviews'] == "newest-reviews")
                $sortCriteria = "ORDER BY post.modified_date DESC";
            elseif($_POST['sort-reviews'] == "restaurant-name")
                $sortCriteria = "ORDER BY restaurant.restaurant_name";
            else
                $sortCriteria = "ORDER BY foodcategory.category_name";  
        }

        if(isset($_POST['category']) && ($_POST['category'] > 0)){
            $categoryid = $_POST['category'];
            $sortCriteria = "AND post.categoryid = $categoryid";
        } 
         
        if(isset($_POST['restaurant']) && ($_POST['restaurant'] > 0)){    
            $restaurantid =  $_POST['restaurant'];         
            $sortCriteria = "AND restaurant.restaurantid = $restaurantid";
        }         
    } 

    $qryRestaurant = "SELECT post.postid, foodcategory.category_name, restaurant.restaurant_name
        , post.post_title, post.post_content, post.restaurant_rating, post.active, user.first_name
        , restaurant.restaurantid, post.created_date, post.modified_date, images.image_name 
                    FROM post 
                        INNER JOIN restaurant
                        JOIN user
                        JOIN foodcategory
                        LEFT JOIN images ON images.postid = post.postid
                    WHERE post.restaurantid = restaurant.restaurantid   
                        AND post.categoryid = foodcategory.categoryid
                        AND post.userid = user.userid
                        $and_active
                        $sortCriteria";

    $stmRestaurant = $db->prepare($qryRestaurant);

    $stmRestaurant->execute();

    $qryCategory = "SELECT * FROM foodcategory";
    $stmCategory = $db->prepare($qryCategory);
    $stmCategory->execute();

    $qryRestaurantOnly = "SELECT * FROM restaurant 
        ORDER BY restaurant_name ASC";
    $stmRestaurantOnly = $db->prepare($qryRestaurantOnly);
    $stmRestaurantOnly->execute();

    $qryImages = "";
?>
  
<h1>Reviews</h1>
<div class="row">
    <div class="col">
        <button onclick="location.href='post_review.php';" 
            class="btn btn-secondary">Write a review</button>
    </div>
</div>     
<div class="row col-md-10">       
    <form action="reviews.php" method="post">   
        <label for="sort-reviews">Sort reviews by:</label>
        <select name="sort-reviews">  
            <option hidden disabled selected value>
                    -- All reviews -- 
            </option>             
            <option value="restaurant-name">Restaurant Name</option>                 
            <option value="food-category">Food Category</option>
            <option value="newest-reviews">Newest reviews</option> 
        </select>  
        <select name="restaurant">     
            <option hidden disabled selected value>-- Restaurant only --</option> 
            <?php if($stmRestaurantOnly->rowCount() > 0): ?>
                <?php while($dat = $stmRestaurantOnly->fetch()): ?>
                    <option value="<?= $dat['restaurantid'] ?>">
                        <?= $dat['restaurant_name'] ?> 
                    </option>
                <?php endwhile ?>
            <?php endif ?>     
        </select> 
        <select name="category"class="select-option-li">    
            <option hidden disabled selected value>-- Category only --</option>             
            <?php if($stmCategory->rowCount() > 0): ?>
                <?php while($datCategory = $stmCategory->fetch()): ?>
                    <option value="<?= $datCategory['categoryid'] ?>">
                        <?= $datCategory['category_name'] ?> 
                    </option>
                <?php endwhile ?>
            <?php endif ?>> 
        </select> 
        <button type="submit" class="btn btn-secondary" id="submit">Sort</button>
    </form> 
    <br />
    <br />
    <br />
    <ul>       
        <?php if($stmRestaurant->rowCount() > 0): ?>         
            <?php while ($datRestaurant = $stmRestaurant->fetch()): ?>                                        
                <li>
                    <h5>
                        <?= $datRestaurant['restaurant_name'] ?> - 
                        <?= $datRestaurant['category_name'] ?>
                    </h5>  
                    <?php if(isset($usr_dat) && ($usr_dat['admin'] == 1)): ?> 
                        <?php if($datRestaurant['active'] == 0): ?>
                            Inactive post                      
                        <?php endif ?>  
                        <?php if($datRestaurant['active'] == 1): ?>
                            Active post                      
                        <?php endif ?>        
                        <a href="review_edit.php?postid=<?= $datRestaurant['postid']?>">[edit]</a>  
                    <?php endif ?>
                    <h6>Title - 
                        <a href="review_read.php?postid=<?= $datRestaurant['postid']?>">
                            <?= $datRestaurant['post_title'] ?>
                        </a>
                    </h6> 
                    <p><?= $datRestaurant['post_content'] ?></p>        
                    <h6>
                        <?= $datRestaurant['restaurant_rating'] ?>/10 
                        rating posted by <?= $datRestaurant['first_name'] ?> on                                  
                        <?php $display_date = (strtotime($datRestaurant['created_date']) 
                            == strtotime($datRestaurant['modified_date'])) ?
                            date('F d, Y h:i A', strtotime($datRestaurant['created_date'])) : 
                            date('F d, Y h:i A', strtotime($datRestaurant['modified_date'])); ?>    
                        <?php if(isset($display_date)) echo $display_date; ?>                         
                        <a href="review_read.php?postid=<?= $datRestaurant['postid']?> ">READ COMMENTS</a>                           
                    </h6>   
                    <?php if(isset($datRestaurant['image_name'])): ?>
                        Photos: <img src="uploads/<?=$datRestaurant['image_name']?>" 
                            class="thumb" alt="<?=$datRestaurant['image_name'] ?>" />                    
                    <?php endif ?>   
                </li> 
                <hr>
            <?php endwhile ?>
        <?php else: ?>
            No reviews exist yet for that restaurant. 
        <?php endif ?>
    </ul> 
</div>
<?php require_once('footer.php'); ?> 