<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 18, 2022
 * Updated: Nov 21, 2022 
 * Purpose: Page to view individual category and restaurants found.
 ******************************************************************************/
    
    require_once('header.php');

    if(isset($_GET['categoryid'])){
        $categoryid = filter_input(INPUT_GET, 'categoryid'
                , FILTER_SANITIZE_NUMBER_INT);

        $qry = "SELECT * 
                FROM restaurant  
                WHERE categoryid = $categoryid"; 

        $stm = $db->prepare($qry);
        $stm->execute(); 

        $qryCategory = "SELECT * FROM foodcategory WHERE categoryid = $categoryid LIMIT 1";
        $stmCategory = $db->prepare($qryCategory);
        $stmCategory->execute();
        
        $datCategory = $stmCategory->fetch();
    }
?> 
<h1><?=$datCategory['category_name']?> joints</h1> 
<br />
<ul> 
    <?php if($stm->rowCount() > 0): ?>
        <?php while($dat = $stm->fetch()): ?>
            <li>
                <a href= "view_restaurant.php?restaurantid=<?=$dat['restaurantid']?>"><?= $dat['restaurant_name']?></a>
            </li>            
        <?php endwhile ?>
    <?php else: ?>
        No restaurants in this category yet.
    <?php endif ?> 

</ul>
<?php require_once('footer.php');