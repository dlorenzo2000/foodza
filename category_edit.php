<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 18, 2022
 * Updated: Nov 19, 2022 
 * Purpose: Logged in users can update food categories.
 ******************************************************************************/

    require_once('header.php');
     
    // if the user visits this page and isn't logged in, then redirect
    if(!($usr_dat = CheckLogin($db))){
        LoginRedirect();
    }
    else{
        if(isset($_GET['categoryid'])){
            $categoryid = filter_input(INPUT_GET, 'categoryid'
                , FILTER_SANITIZE_NUMBER_INT);            

            $qry = "SELECT * 
                    FROM foodcategory                   
                    WHERE categoryid = :categoryid LIMIT 1";

            $stm = $db->prepare($qry);           
            $stm->bindValue(':categoryid', $categoryid, PDO::PARAM_INT);
            $stm->execute();

            $dat = $stm->fetch();  
        }
            
        if($_POST && empty(trim($_POST['category_name']))){
            $category_name_error = "* Category name cannot be blank.";            
        } 
        else{      
            if($_POST && $_POST['save'] && !empty(trim($_POST['category_name']))){
                $categoryid = (int)filter_input(INPUT_POST, 'categoryid'
                    , FILTER_SANITIZE_NUMBER_INT);  
                $category_name = trim(filter_input(INPUT_POST, 'category_name'
                    , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            
                $qryCategory = "UPDATE foodcategory
                                SET category_name = :category_name
                                WHERE categoryid = :categoryid";

                $stmCategory = $db->prepare($qryCategory);
                $stmCategory->bindValue(':category_name', $category_name, PDO::PARAM_STR); 
                $stmCategory->bindValue(':categoryid', $categoryid, PDO::PARAM_INT); 
                $stmCategory->execute();     
                
                header('Location: category.php');
                exit;
            }  
            
            if($_POST && $_POST['delete']){ 
                $categoryid = filter_input(INPUT_POST, 'categoryid'
                    , FILTER_SANITIZE_NUMBER_INT);
                $qry="UPDATE foodcategory
                    SET active = 0 
                    WHERE categoryid = $categoryid";
                        
                $stm=$db->prepare($qry);        
                $stm->execute();  

                header("Location: category.php");
                exit;
            }

            if($_POST && $_POST['reactivate']){ 
                $categoryid = filter_input(INPUT_POST, 'categoryid'
                    , FILTER_SANITIZE_NUMBER_INT);
                $qry="UPDATE foodcategory
                    SET active = 1
                    WHERE categoryid = $categoryid";
                        
                $stm=$db->prepare($qry);        
                $stm->execute();  

                header("Location: category.php");
                exit;
            }
        }
    }
?>

<h1>Edit category</h1>
<form action="category_edit.php" method="post">
    <input type="hidden" name="categoryid" value="<?=$dat['categoryid']?>">
    <label for="category_name">
        Category name
    </label>
    <input type="text" name="category_name" 
        value="<?php if(isset($dat['category_name'])) echo $dat['category_name'];?>"> 
    <span>
        <?php if(isset($category_name_error)) echo $category_name_error; ?>
    </span> 
    <br />
    <br />
    <button type="submit" class="btn btn-secondary" name="save" value="save">Save</button>        
    <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
    <?php if($usr_dat['admin'] == 1): ?>
        <button type="submit" class="btn btn-secondary" value="delete" name="delete"
            onclick="return confirm('Are you sure?')">De-activate</button>
    <?php else: ?>        
        <button type="submit" class="btn btn-secondary" 
            value="Re-activate" name="reactivate">Re-activate</button>
    <?php endif ?>   
    <br />
    <br />
    <br />   
</form>
 <?php require_once('footer.php'); ?>