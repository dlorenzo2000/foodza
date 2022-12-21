<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 19, 2022
 * Updated: Nov 19, 2022 
 * Purpose: Logged in users can update restaurants.
 ******************************************************************************/

    require_once('header.php');
     
    // if the user visits this page and isn't logged in, then redirect
    if(!($usr_dat = CheckLogin($db))){
        LoginRedirect();
    }
    else{
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

        if(isset($_GET['restaurantid'])){
            $restaurantid = filter_input(INPUT_GET, 'restaurantid'
                    , FILTER_SANITIZE_NUMBER_INT);

            $qry = "SELECT * 
                FROM restaurant
                    JOIN foodcategory 
                    JOIN city
                    JOIN province
                WHERE restaurantid = $restaurantid 
                    AND restaurant.categoryid = foodcategory.categoryid 
                    AND restaurant.cityid = city.cityid
                    AND restaurant.provinceid = province.provinceid LIMIT 1";

            $stm = $db->prepare($qry);
            $stm->execute();
            
            $dat = $stm->fetch();

            $qryActive = "SELECT * FROM restaurant
                WHERE restaurantid = $restaurantid LIMIT 1";
            $stmActive = $db->prepare($qryActive);
            $stmActive->execute();
            $datActive = $stmActive->fetch();
        }

        if($_POST && empty(trim($_POST['restaurant-name'])))
        $restaurant_name_error = "* Restaurant name cannot be blank.";

        if($_POST && empty(trim($_POST['restaurant-address'])))
            $restaurant_address_error = "* Address cannot be blank."; 

        if($_POST && isset($_POST['delete'])){ 
            $restaurantid = filter_input(INPUT_POST, 'restaurantid'
                , FILTER_SANITIZE_NUMBER_INT);
            $qry="UPDATE restaurant
                  SET active = 0 
                  WHERE restaurantid = $restaurantid";
                    
            $stm=$db->prepare($qry);        
            $stm->execute();  

            header("Location: restaurant.php");
            exit;
        }

        if($_POST && isset($_POST['reactivate'])){ 
            $restaurantid = filter_input(INPUT_POST, 'restaurantid'
                , FILTER_SANITIZE_NUMBER_INT);
            $qry="UPDATE restaurant
                  SET active = 1
                  WHERE restaurantid = $restaurantid";
                    
            $stm=$db->prepare($qry);        
            $stm->execute();  

            header("Location: restaurant.php");
            exit;
        }

        if($_SERVER['REQUEST_METHOD'] === "POST" && !empty(trim($_POST['restaurant-name'])
            && !empty(trim($_POST['restaurant-address'])))){      
                
            $restaurantid = (int)filter_input(INPUT_POST, 'restaurantid'
                , FILTER_SANITIZE_NUMBER_INT);
            $restaurant_name = trim(filter_input(INPUT_POST, 'restaurant-name'                
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $restaurant_address = trim(filter_input(INPUT_POST, 'restaurant-address'                
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $cityid = (int)trim(filter_input(INPUT_POST, 'cityid'                
                , FILTER_SANITIZE_NUMBER_INT));
            $provinceid = (int)trim(filter_input(INPUT_POST, 'provinceid'                
                , FILTER_SANITIZE_NUMBER_INT));
            $cateogryid = (int)trim(filter_input(INPUT_POST, 'categoryid'                
                , FILTER_SANITIZE_NUMBER_INT));         

            $qryRestaurant = "UPDATE restaurant 
                              SET restaurant_name=:restaurant_name 
                                , restaurant_address=:restaurant_address
                                , cityid=:cityid, provinceid=:provinceid
                                , categoryid=:categoryid 
                              WHERE restaurantid=:restaurantid";

            $stmRestaurant = $db->prepare($qryRestaurant);

            $stmRestaurant->bindValue(':restaurantid', $restaurantid, PDO::PARAM_INT);
            $stmRestaurant->bindValue(':restaurant_name', $restaurant_name, PDO::PARAM_STR);
            $stmRestaurant->bindValue(':restaurant_address', $restaurant_address, PDO::PARAM_STR);
            $stmRestaurant->bindValue(':cityid', $cityid, PDO::PARAM_INT);
            $stmRestaurant->bindValue(':provinceid', $provinceid, PDO::PARAM_INT);
            $stmRestaurant->bindValue(':categoryid', $cateogryid, PDO::PARAM_INT); 

            $stmRestaurant->execute();            
 
            header('location: restaurant.php');
            exit;
        }     
  
    }    
?>

<h1>Edit restaurant</h1>
<form action="restaurant_edit.php" method="post">
    <input type="hidden" name="restaurantid" value="<?=$dat['restaurantid']?>">
    <label for="restaurant-name">
        Restaurant name
    </label>
    <input type="text" name="restaurant-name"
     value="<?php if(isset($dat['restaurant_name'])) echo $dat['restaurant_name'];?>">
    <span>
        <?php if(isset($restaurant_name_error)) echo $restaurant_name_error; ?>
    </span> 
    <br />
    <label for="restaurant-address">
        Address
    </label>
    <input type="text" name="restaurant-address" size=100
        value="<?php if(isset($dat['restaurant_address'])) echo $dat['restaurant_address'];?>">
    <span>
        <?php if(isset($restaurant_address_error)) echo $restaurant_address_error; ?>
    </span>     
    <br/>
    <label for="cityid">City</label>
    <select name="cityid" >
        <?php if($stmCity->rowCount() > 0): ?>
            <option selected value="
                <?php if(isset($dat)): ?>
                    <?= $dat['cityid'] ?>"><?= $dat['city_name'] ?> 
                <?php endif ?>
            </option>
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
            <option selected value="
                <?php if(isset($dat)): ?> 
                    <?= $dat['provinceid'] ?>"><?= $dat['province_name'] ?> 
                <?php endif?>
            </option>
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
            <option selected value="
                <?php if(isset($dat)): ?>
                    <?= $dat['categoryid'] ?>">
                    <?= $dat['category_name'] ?> 
                <?php endif ?>
            </option> 
            <?php while($datCategory = $stmCategory->fetch()): ?>
                <option value="<?= $datCategory['categoryid'] ?>">
                    <?= $datCategory['category_name'] ?>
                </option>
            <?php endwhile ?>
        <?php endif ?>
    </select>
    <br />
    <br />
    <button type="submit" class="btn btn-secondary" 
        name="save" value="save">Save</button>        
        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
    <?php if($usr_dat['admin'] == 1): ?> 
        <?php if(isset($datActive)): ?>
            <?php if ($datActive['active'] == 1): ?>
                <button type="submit" class="btn btn-secondary" value="delete" name="delete"
                    onclick="return confirm('Are you sure?')">De-activate</button>
            <?php else: ?>        
            <button type="submit" class="btn btn-secondary" 
                value="Re-activate" name="reactivate">Re-activate</button>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>  
</form>
<br />
<br />
<br />  
<?php require_once('footer.php'); ?>