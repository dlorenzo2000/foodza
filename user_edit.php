<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 20, 2022
 * Updated: Nov 20, 2022 
 * Purpose: Manage the users
 *****************************************************************************/

    require_once('header.php'); 

    // if the user visits this page and isn't logged in, then redirect
    if(!($usr_dat = CheckLogin($db)) && $usr_dat['admin'] != 1){
        LoginRedirect();
    }
    else{

        // get the userid from the selected user
        if(isset($_GET['userid'])){
            $userid = filter_input(INPUT_GET, 'userid', FILTER_SANITIZE_NUMBER_INT);

            $qryUser = "SELECT * FROM user WHERE userid = $userid LIMIT 1";

            $stmUser = $db->prepare($qryUser);
                    
            $stmUser->execute();

            $datUser = $stmUser->fetch(); 
        } 

        if($_POST && isset($_POST['delete'])){           
            $userid = filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_NUMBER_INT);
            
            $qry="UPDATE user
                SET active = 0 
                WHERE userid = $userid";
                    
            $stm=$db->prepare($qry);        
            $stm->execute();  

            header("Location: users.php");
            exit;
        }

        if($_POST && isset($_POST['reactivate'])){  
            $userid = filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_NUMBER_INT);

            $qry="UPDATE user
                SET active = 1
                WHERE userid = $userid";
                    
            $stm=$db->prepare($qry);        
            $stm->execute();  

            header("Location: users.php");
            exit;
        }

        if($_POST && isset($_POST['set-admin'])){  
            $userid = filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_NUMBER_INT);

            $qry="UPDATE user
                SET admin = 1
                WHERE userid = $userid";
                    
            $stm=$db->prepare($qry);        
            $stm->execute();  

            header("Location: users.php");
            exit;
        }

        if($_POST && isset($_POST['remove-admin'])){  
            $userid = filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_NUMBER_INT);

            $qry="UPDATE user
                SET admin = 0
                WHERE userid = $userid";
                    
            $stm=$db->prepare($qry);        
            $stm->execute();  

            header("Location: users.php");
            exit;
        }

        if($_POST && empty(trim($_POST['first-name']))){ 
            header("Location: user_edit.php?userid=$userid");        
            $first_name_error = "* First name cannot be blank.";
        }

        if($_POST && empty(trim($_POST['last-name'])))
            $last_name_error = "* Last name cannot be blank.";
        
        if($_POST && empty(trim($_POST['email'])))
            $email_error = "* Email address cannot be blank.";
        
        if($_POST && empty(trim($_POST['username'])))
            $username_error = "* Username cannot be blank.";
    
        if($_POST && empty(trim($_POST['pwd'])))
            $password_error = "* Password cannot be blank.";

        if($_POST && trim($_POST['pwd'] != trim($_POST['pwd2'])))
            $password2_error = "* The passwords don't match.";

        
        if($_POST && !empty(trim($_POST['first-name'])) && !empty(trim($_POST['last-name']))
            && !empty(trim($_POST['email'])) && !empty(trim($_POST['username'])) 
            && trim($_POST['pwd'] == trim($_POST['pwd2']))){

            $userid = filter_input(INPUT_POST, 'userid'
                , FILTER_SANITIZE_NUMBER_INT);        
            $first_name = trim(filter_input(INPUT_POST, 'first-name'
                , trim(FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
            $last_name = trim(filter_input(INPUT_POST, 'last-name'
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $email = trim(filter_input(INPUT_POST, 'email'
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $username = trim(filter_input(INPUT_POST, 'username'
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $pwd = (trim(filter_input(INPUT_POST, 'pwd'
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS)));  

            $qry = "UPDATE user 
                    SET first_name=:first_name 
                    , last_name=:last_name 
                    , email=:email, username=:username
                    , pwd=:pwd
                    , admin=:admin
                    WHERE userid=:userid";
        
            $stm = $db->prepare($qry);

            $encrypted = password_hash($pwd, PASSWORD_DEFAULT);

            $stm->bindvalue(':first_name', $first_name, PDO::PARAM_STR);
            $stm->bindvalue(':last_name', $last_name, PDO::PARAM_STR);
            $stm->bindvalue(':email', $email, PDO::PARAM_STR);
            $stm->bindvalue(':username', $username, PDO::PARAM_STR);
            $stm->bindvalue(':pwd', $encrypted, PDO::PARAM_STR);
            $stm->bindvalue(':userid', $userid, PDO::PARAM_INT); 
            
            $stm->execute();

            header("Location: users.php"); 
            die;      
        }                   
    }      
?>
 
<h1>Manage user</h1>
<div class="row">
    <form method="post" action="user_edit.php"> 
        <input type="hidden" name="userid" value="<?=$datUser['userid']?>">
        <br />              
        <label for="first-name">First name</label>
        <input type="text" name="first-name" 
            value="<?php if(isset($datUser['first_name'])) 
                echo $datUser['first_name']; ?>">
        <span><?php if(isset($first_name_error)) echo $first_name_error; ?></span>                
        <br />
        <br />
        <label for="last-name">Last name</label>
        <input type="text" name="last-name"
            value="<?php if(trim(isset($datUser['last_name']))) 
                echo $datUser['last_name']; ?>">
        <span><?php if(isset($last_name_error)) echo $last_name_error; ?></span>
        <br />
        <br />
        <label for="email">Email</label>
        <input type="email " name="email" 
            value="<?php if(trim(isset($datUser['email']))) 
                echo $datUser['email']; ?>">
        <span><?php if(isset($email_error)) echo $email_error; ?></span>
        <br />
        <br />     
        <label for="username">Username</label>
        <input type="text" name="username"
            value="<?php if(trim(isset($datUser['username']))) 
                echo $datUser['username']; ?>">
        <span><?php if(isset($username_error)) echo $username_error; ?></span>
        <br />
        <br />
        <label for="pwd1">Password</label>
        <input type="password" name="pwd"
            value="<?php if(trim(isset($datUser['pwd']))) 
                echo $datUser['pwd']; ?>">   
        <span><?php if(isset($password_error)) 
            echo $password_error; ?></span>      
        <br />
        <br />
        <label for="pwd2">Re-enter Password</label>
        <input type="password" name="pwd2"
            value="<?php if(trim(isset($datUser['pwd']))) 
                echo $datUser['pwd']; ?>">   
        <span><?php if(isset($password2_error)) 
            echo $password2_error; ?></span>      
        <br />
        <br />           
        <button type="submit" class="btn btn-secondary" id="submit">Save</button> 
        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
            <?php if($usr_dat['admin'] == 1): ?>
                <?php if ($datUser['admin'] == 0): ?>
                    <button type="submit" class="btn btn-secondary" value="set-admin" name="set-admin"
                            onclick="return confirm('Are you sure?')">Set admin</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-secondary" value="remove-admin" name="remove-admin"
                            onclick="return confirm('Are you sure?')">Remove admin</button>
                <?php endif ?>

                <?php if(isset($datUser) && ($datUser['active'] == 1)): ?>
                    <button type="submit" class="btn btn-secondary" value="delete" name="delete"
                        onclick="return confirm('Are you sure?')">De-activate</button>
                <?php else: ?>        
                    <button type="submit" class="btn btn-secondary" 
                        value="Re-activate" name="reactivate">Re-activate</button>
                <?php endif ?>
            <?php endif ?> 
        <br />
        <br />
    </form>
</div>    
<?php require_once('footer.php'); ?> 