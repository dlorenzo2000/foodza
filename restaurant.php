<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 19, 2022
 * Updated: Nov 20, 2022 
 * Purpose: Logged in users can create and update restaurants.
 ******************************************************************************/

    require_once('header.php');
     
    // if the user visits this page and isn't logged in, then redirect
    if(!($usr_dat = CheckLogin($db))){
    }
    else{
        if($_SERVER['REQUEST_METHOD'] === "POST" && !empty(trim($_POST['restaurant-name'])
            && !empty(trim($_POST['restaurant-address'])))){

            $restaurant_name = trim(filter_input(INPUT_POST, 'restaurant-name'                
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $restaurant_address = trim(filter_input(INPUT_POST, 'restaurant-address'                
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $cityid = trim(filter_input(INPUT_POST, 'cityid'                
                , FILTER_SANITIZE_NUMBER_INT));
            $provinceid = trim(filter_input(INPUT_POST, 'provinceid'                
                , FILTER_SANITIZE_NUMBER_INT));
            $cateogryid = trim(filter_input(INPUT_POST, 'categoryid'                
                , FILTER_SANITIZE_NUMBER_INT));         

            $qryRestaurant = "INSERT INTO restaurant (restaurant_name, restaurant_address
                                ,cityid, provinceid, categoryid) 
                              VALUES (:restaurant_name, :restaurant_address, :cityid,
                                :provinceid, :categoryid)";

            $stmRestaurant = $db->prepare($qryRestaurant);

            $stmRestaurant->bindValue(':restaurant_name', $restaurant_name);
            $stmRestaurant->bindValue(':restaurant_address', $restaurant_address);
            $stmRestaurant->bindValue(':cityid', $cityid);
            $stmRestaurant->bindValue(':provinceid', $provinceid);
            $stmRestaurant->bindValue(':categoryid', $cateogryid); 

            $stmRestaurant->execute();

            header('location: restaurant.php');
        }     
        else{
            if($_POST && empty(trim($_POST['restaurant-name'])))
                $restaurant_name_error = "* Restaurant name cannot be blank.";
    
            if($_POST && empty(trim($_POST['restaurant-address'])))
                $restaurant_address_error = "* Address cannot be blank."; 
        }    
    }                    
        
    // fill in drop down field for city
    $qryCity = "SELECT * FROM city";
    $stmCity = $db->prepare($qryCity);
    $stmCity->execute();

    // fill in drop down field for province
    $qryProvince = "SELECT * FROM province";
    $stmProvince = $db->prepare($qryProvince);
    $stmProvince->execute();

    // fill in drop down field for category
    $qryCategory = "SELECT * FROM foodcategory ORDER BY category_name";
    $stmCategory = $db->prepare($qryCategory);
    $stmCategory->execute();

    $qryRestaurants = "SELECT * FROM restaurant ORDER BY restaurant_name";
    $stmRestaurants = $db->prepare($qryRestaurants);
    $stmRestaurants->execute();
?>

<h1>Restaurants</h1>
<?php if(isset($usr_dat)):?>
<form action="restaurant.php" method="post">
    <label for="restaurant-name">
        Restaurant name
    </label>
    <input type="text" name="restaurant-name">
    <span class="error-message">
        <?php if(isset($restaurant_name_error)) echo $restaurant_name_error; ?>
    </span> 
    <br />
    <label for="restaurant-address">
        Address
    </label>
    <input type="text" name="restaurant-address" size=100>
    <span class="error-message">
        <?php if(isset($restaurant_address_error)) echo $restaurant_address_error; ?>
    </span>     
    <br/>
    <label for="cityid">City</label>
    <select name="cityid" >
        <?php if($stmCity->rowCount() > 0): ?>
            <?php while ($datCity = $stmCity->fetch()): ?>
                <option value="<?= $datCity['cityid']?>"> 
                    <?= $datCity['city_name'] ?>
                </option>
            <?php endwhile ?>
        <?php endif ?>
    </select>
    <label for="provinceid">Province</label>
    <select name="provinceid">
        <?php if($stmProvince->rowCount() >0): ?>
            <?php while($datProvince = $stmProvince->fetch()): ?>
                <option value="<?= $datProvince['provinceid'] ?>">
                    <?= $datProvince['province_name'] ?>
                </option>
            <?php endwhile ?>
        <?php endif ?>
    </select>
    <label for="categoryid">Category</label>
    <select name="categoryid">
        <?php if($stmCategory->rowCount() >0): ?>
            <?php while($datCategory = $stmCategory->fetch()): ?>
                <option value="<?= $datCategory['categoryid'] ?>">
                    <?= $datCategory['category_name'] ?>
                </option>
            <?php endwhile ?>
        <?php endif ?>
    </select>
    <br />
    <br />
    <button type="submit" class="btn btn-secondary">Add</button>
    <?php if($usr_dat['admin'] === 1): ?>
        <button type="button" class="btn btn-secondary" 
            onclick="window.location.replace('my_reviews.php')">Edit/button>
        <button type="button" class="btn btn-secondary" 
            onclick="window.location.replace('my_reviews.php')">De-activate</button>
    <?php endif ?>
    <button type="button" class="btn btn-secondary" 
        onclick="window.location.reload()">Clear</button>
</form>
<?php endif ?>
<br />
<?php if($stmRestaurants->rowCount() > 0): ?> 
    <?php while($datRestaurants = $stmRestaurants->fetch()): ?>
        <a href="view_restaurant.php?restaurantid=<?= $datRestaurants['restaurantid']?>"><?= $datRestaurants['restaurant_name'] ?></a>          
        <?php if(isset($usr_dat)): ?>
            <a href="restaurant_edit.php?restaurantid=<?= $datRestaurants['restaurantid']?>">[edit]</a> 
            <?php if($usr_dat['admin'] == 1 ) echo " - active " . $datRestaurants['active']; ?>                  
        <?php endif ?>
        <br />
    <?php endwhile ?> 
<?php endif ?>    
<?php require_once('footer.php'); ?>