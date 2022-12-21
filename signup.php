<?php
/****************************************************************************** 
 * Name: Dean Lorenzo
 * Student number: 0367298
 * Course: Web Development - 2008 (228566)
 * Assignment: Final Project
 * Created: Nov 12, 2022
 * Updated: Nov 15, 2022 
 * Purpose: Manage the sign up process.
 *****************************************************************************/

    require_once('header.php'); 

    if($_SERVER['REQUEST_METHOD'] === "POST"){
        if($_POST && empty($_POST['first-name'])){
            $first_name_error = "* Please enter your first name.";
        }
        if($_POST && empty($_POST['last-name'])){
            $last_name_error = "* Please enter your last name.";
        }
        if($_POST && empty($_POST['email'])){
            $email_error = "* Please enter your email address.";
        }
        if($_POST && empty($_POST['username'])){
            $username_error = "* Please enter a username.";
        }
        if($_POST && empty($_POST['pwd1'])){
            $password_error1 = "* Please enter a password.";
        }
        if($_POST && empty($_POST['pwd2'])){
            $password_error2 = "* Please re-enter the password.";
        }
    }

    if($_POST && !empty($_POST['first-name']) && !empty($_POST['last-name'])
        && !empty($_POST['email']) && !empty($_POST['username']) 
        && !empty($_POST['pwd1']) && !empty($_POST['pwd2'])){
        
        $first_name = filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pwd1 = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pwd2 = filter_input(INPUT_POST, 'pwd2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if($pwd1 === $pwd2){
            $pwd = password_hash($pwd1, PASSWORD_DEFAULT); 
            
            $qry = "INSERT INTO User (first_name, last_name, email, username, pwd) 
                VALUES (:first_name, :last_name, :email, :username, :pwd)";
        
            $stm = $db->prepare($qry);

            $stm->bindvalue(':first_name', $first_name, PDO::PARAM_STR);
            $stm->bindvalue(':last_name', $last_name, PDO::PARAM_STR);
            $stm->bindvalue(':email', $email, PDO::PARAM_STR);
            $stm->bindvalue(':username', $username, PDO::PARAM_STR);
            $stm->bindvalue(':pwd', $pwd, PDO::PARAM_STR);
            
            $stm->execute();

            header("Location: login.php");
            die;
        }  
        else{
                $password_error2 = "* The passwords do not match";            
        }      
    }    
?>

<div class="row">
    <form method="post" action="signup.php">    
    <br /> 
        <br /> 
        <br />         
        <h2>Sign up</h2> 
        <br />
        <label for="first-name">First name</label>
        <input type="text" name="first-name" 
            value="<?php if(isset($_POST['first-name'])) echo $_POST['first-name']; ?>">
        <span><?php if(isset($first_name_error)) echo $first_name_error; ?></span>                
        <br />
        <br />
        <label for="last-name">Last name</label>
        <input type="text" name="last-name"
            value="<?php if(trim(isset($_POST['last-name']))) echo $_POST['last-name']; ?>">
        <span><?php if(isset($last_name_error)) echo $last_name_error; ?></span>
        <br />
        <br />
        <label for="email">Email</label>
        <input type="email " name="email" 
            value="<?php if(trim(isset($_POST['email']))) echo $_POST['email']; ?>">
        <span><?php if(isset($email_error)) echo $email_error; ?></span>
        <br />
        <br />     
        <label for="username">Username</label>
        <input type="text" name="username"
            value="<?php if(trim(isset($_POST['username']))) echo $_POST['username']; ?>">
        <span><?php if(isset($username_error)) echo $username_error; ?></span>
        <br />
        <br />
        <label for="pwd1">Password</label>
        <input type="password" name="pwd1"
            value="<?php if(trim(isset($_POST['pwd1']))) echo $_POST['pwd1']; ?>">   
        <span><?php if(isset($password_error1)) echo $password_error1; ?></span>      
        <br />
        <br />        
        <label for="pwd2">Re-enter password</label>
        <input type="password" name="pwd2"
            value="<?php if(trim(isset($_POST['pwd2']))) echo $_POST['pwd2']; ?>">
        <span><?php if(isset($password_error2)) echo $password_error2; ?></span>   
        <br />
        <br />           
        <button type="submit" class="btn btn-secondary" id="submit">Register</button> 
        <button type="button" class="btn btn-secondary" 
            onclick="window.location.replace('index.php')">Cancel</button>
        <br />
        <br />
        <a href="login.php">Click here to login</a>
    </form>
</div>    
<?php require_once('footer.php'); ?> 