<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 18, 2022
 * Updated: Nov 19, 2022 
 * Purpose: Logged in users can create and update food categories.
 ******************************************************************************/

     require_once('header.php');
     
    // if the user visits this page and isn't logged in, then redirect
    if(!($usr_dat = CheckLogin($db))){
        LoginRedirect();        
    }
    else{
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            if($_POST && empty(trim($_POST['category-name']))){
                $category_name_error = "* Field cannot be blank.";            
            }
            else{
                $category_name = trim(filter_input(INPUT_POST, 'category-name'
                    , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    
                $qryCategory = "INSERT INTO foodcategory (category_name) 
                    VALUES (:category_name)";
    
                $stmCategory = $db->prepare($qryCategory);
                $stmCategory->bindValue(':category_name', $category_name);
                $stmCategory->execute();     
                
                header('Location: category.php');
            }
        }        
    }

    $qryCategories = "SELECT * FROM foodcategory ORDER BY category_name";
    $stmCategories = $db->prepare($qryCategories);
    $stmCategories->execute();
?>

<h1>Food category</h1>
<form action="category.php" method="post">
    <label for="category-name">
        Category name
    </label>
    <input type="text" name="category-name">
    <span>
        <?php if(isset($category_name_error)) echo $category_name_error; ?>
    </span> 
    <br />
    <br />
    <button type="submit" class="btn btn-secondary" id="submit">Add</button>        
    <button type="button" class="btn btn-secondary" 
        onclick="window.location.reload()">Clear</button>
    <br />
    <br />
    <br />  
</form> 
<br>
<a href="restaurant.php">Add restaurant</a>
<br />
<br />
<?php if($stmCategories->rowCount() > 0): ?> 
    <?php while($datCategories = $stmCategories->fetch()): ?>        
        <a href="view_category.php?categoryid=<?=$datCategories['categoryid'] ?>">
            <?= $datCategories['category_name'] ?>
        </a>
        <a href="category_edit.php?categoryid=<?= $datCategories['categoryid']?>">[edit]</a> 
        <?php if($usr_dat['admin'] == 1) echo "- active " . $datCategories['active']; ?>             
        <br />
    <?php endwhile ?> 
<?php endif ?>  
<?php require_once('footer.php'); ?>