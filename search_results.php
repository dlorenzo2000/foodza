<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Dec 8, 2022
 * Updated: Dec 8, 2022 
 * Purpose: Search functionality of the site.
 *****************************************************************************/
    
    require_once('header.php');

    if(isset($_GET['search_words'])){
        $search_words = trim(filter_input(INPUT_GET, 'search_words'
        , FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $qryRestaurantSearch = "SELECT * 
                                FROM restaurant r  
                                LEFT JOIN foodcategory fc ON r.categoryid = fc.categoryid
                                WHERE r.restaurant_name 
                                    LIKE '%$search_words%' 
                                OR fc.category_name LIKE '%$search_words%' 
                                    AND r.active = 1";

        $stmRestaurantSearch = $db->prepare($qryRestaurantSearch);
        $stmRestaurantSearch->execute();                            

        $qryCategorySearch = "SELECT * 
                            FROM foodcategory
                            WHERE category_name 
                                LIKE '%$search_words%'";

        $stmCategorySearch = $db->prepare($qryCategorySearch);
        $stmCategorySearch->execute();

        $qryReviewSearch = "SELECT * 
                            FROM post p  
                            JOIN foodcategory fc ON p.categoryid = fc.categoryid
                            JOIN restaurant r ON p.restaurantid = r.restaurantid
                            WHERE  
                                p.active = 1
                            AND fc.category_name LIKE '%$search_words%'";

        $stmReviewSearch = $db->prepare($qryReviewSearch);
        $stmReviewSearch->execute();    
    }    
?>

<div class="row">

Search results for:<h5> <?= $_GET['search_words']?></h5>
<br />
<br />
REVIEWS
<?php if($stmReviewSearch->rowcount() > 0): ?> 
    <ul>
        <?php while($datReview = $stmReviewSearch->fetch()): ?>
            <li>
                Title - <?= $datReview['post_title'] ?>  
                - Review rating <?= $datReview['restaurant_rating'] ?>
                for [ <?= $datReview['restaurant_name']?></a> ] restaurant 
                <a href="review_read.php?postid=<?=$datReview["postid"] ?>" > 
                    read review.</a>
            </li>         
        <?php endwhile ?>
    </ul>
<?php endif ?>
<br />
RESTAURANTS
<?php if($stmRestaurantSearch->rowcount() > 0): ?> 
    <ul>
        <?php while($datRestaurant = $stmRestaurantSearch->fetch()):?>
            <li>
            <a href="view_restaurant.php?restaurantid=<?= $datRestaurant['restaurantid']?>">
                <?= $datRestaurant['restaurant_name'] ?></a>                 
            </li>
        <?php endwhile ?>
    </ul>
<?php endif ?>
<br /> 
FOOD CATEGORY
<?php if($stmCategorySearch->rowcount()>0): ?> 
    <ul>
        <?php while($datCategory = $stmCategorySearch->fetch()): ?>
            <li>
                <?= $datCategory['category_name'] ?>
            </li>
        <?php endwhile ?>
    </ul>
<?php endif ?>
</div>
<?php require_once('footer.php'); ?>