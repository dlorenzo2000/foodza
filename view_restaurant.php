<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 18, 2022
 * Updated: Nov 28, 2022 
 * Purpose: Page to view individual restaurant reviews
 ******************************************************************************/
    
    require_once('header.php');

    if(isset($_GET['restaurantid'])){
        $restaurantid = filter_input(INPUT_GET, 'restaurantid'
                , FILTER_SANITIZE_NUMBER_INT);

        $qryActive = "SELECT * FROM restaurant
            WHERE restaurantid = $restaurantid AND active = 1 LIMIT 1";
        $stmActive = $db->prepare($qryActive);
        $stmActive->execute();
        $datActive = $stmActive->fetch(); 

        $qryReviews = " SELECT post.postid, foodcategory.category_name, restaurant.restaurant_name
        , post.post_title, post.post_content, post.restaurant_rating, user.first_name
        , restaurant.restaurantid, post.created_date, post.modified_date, images.image_name
        , post.active
                        FROM post                      
                        INNER JOIN foodcategory ON foodcategory.categoryid = post.categoryid              
                        INNER JOIN restaurant ON post.restaurantid = restaurant.restaurantid 
                        LEFT JOIN images ON images.postid = post.postid 
                        LEFT JOIN user on post.userid = user.userid
                        where post.restaurantid = $restaurantid ORDER BY post.modified_date";
        
        $stmReviews = $db->prepare($qryReviews);        
        $stmReviews->execute();

        $qryRestaurant = "  SELECT * 
                            FROM restaurant  
                            INNER JOIN foodcategory ON foodcategory.categoryid = restaurant.categoryid
                            INNER JOIN city ON city.cityid = restaurant.cityid
                            INNER JOIN province ON province.provinceid = restaurant.provinceid
                            WHERE restaurantid = $restaurantid"; 

        $stmRestaurant = $db->prepare($qryRestaurant);
        $stmRestaurant->execute();
        $datRestaurant = $stmRestaurant->fetch();        
    }
?> 

<h1>Restaurant Details</h1>
<br />   
<ul> 
    <h2 class="heading_inline"><?= $datRestaurant['restaurant_name'] ?></h2>
    [<?= $datRestaurant['category_name'] ?> food]
    <br />  
    <?= $datRestaurant['restaurant_address'] ?>
    <br />
    <?= $datRestaurant['city_name'] ?>, <?= $datRestaurant['province_name'] ?> 
    <br />
    <br />
    <div class="col">
        <button onclick="location.href='post_review.php';" 
            class="btn btn-secondary">Write a review</button>
    </div>  
    <br />
    <?php if($stmReviews->rowCount() > 0): ?> 
        <h5>Restaurant reviews</h5> 
        <hr>
        <?php while ($datReviews = $stmReviews->fetch()): ?>                        
            <li>                                           
                <?php if(isset($usr_dat) && ($usr_dat['admin'] == 1)): ?> 
                    <?php if($datReviews['active'] == 0): ?>  
                        Inactive post                    
                    <?php endif ?>  
                    <?php if($datReviews['active'] == 1): ?>
                        Active post                      
                    <?php endif ?>        
                    <a href="review_edit.php?postid=<?= $datReviews['postid']?>">[edit]</a>  
                <?php endif ?>
                <h6>Title - 
                    <a href="review_read.php?postid=<?= $datReviews['postid']?>">
                        <?= $datReviews['post_title'] ?>
                    </a>
                </h6> 
                <p><?= $datReviews['post_content'] ?></p>        
                <h6>
                    <?= $datReviews['restaurant_rating'] ?>/10 
                    rating posted by <?= $datReviews['first_name'] ?> on                                  
                    <?php $display_date 
                        = (($datReviews['created_date']) === ($datReviews['modified_date'])) ?
                        date('F d, Y h:i A', strtotime($datReviews['created_date'])) : 
                        date('F d, Y h:i A', strtotime($datReviews['modified_date'])); ?>    
                    <?php if(isset($display_date)) echo $display_date; ?>                         
                    <a href="review_read.php?postid=<?= $datReviews['postid']?>">READ COMMENTS</a>                           
                </h6>   
                <?php if($datReviews['image_name']):?>
                    Photos:  
                    <img src="uploads/<?=$datReviews['image_name']?>" 
                        class="thumb" alt="<?=$datReviews['image_name'] ?>" /> 
                <?php endif ?>                    
            </li> 
            <hr>
        <?php endwhile ?>    
    <?php else: ?>    
        No reviews for this place yet.
    <?php endif ?>           
</ul>
<?php require_once('footer.php');