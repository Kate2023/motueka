<?php 
 include "checksession.php"; 
  

 ?> 
 <!DOCTYPE html> 
 <html lang="en"> 
     <head> 
         <title>Customer Login</title> 
         <meta charset="UTF-8"> 
         <meta name="viewport" content="width=device-width, initial-scale=1"> 
     </head> 
 <body> 
 
 <?php 
     include('config.php'); 
     //check if we are saving data first by checking if the submit button exists in the array
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Login')) {
     $db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE); 
     if (mysqli_connect_errno()) { 
         echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ; 
         exit; //stop processing the page further 
     }; 
 // if the login form has been filled in 
     if (isset($_POST['email'])) 
     { 
         $email = $_POST['email']; 
         $password = $_POST['password']; 
 //prepare a query and send it to the server 
        $query = 'SELECT customerID, password, role FROM customer WHERE email="'.$email.'"';
        $result = mysqli_query($db_connection, $query);
        $rowcount = mysqli_num_rows($result); 
    if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
        $customerID = $row['customerID'];
        $hashpassword=$row['password'];
        $role=$row['role'];
    }	 
    }
         
         if(!$customerID) 
         { 
             echo '<p class="error">Unable to find member with email!'.$email.'</p>'; 
         } 
         else 
         { 
             if (password_verify($password, $hashpassword)) 
             { 
                  $_SESSION['loggedin'] = true; 
                  $_SESSION['email'] = $email; 
                  $_SESSION['role'] = $role;
                  $_SESSION['customerID'] = $customerID; 
                  loginStatus(); 
                  echo  'Customer ID='. $_SESSION['customerID'];
                 if ( $_SESSION['role'] ==1)
                 {echo '<p><a href="makeabooking.php">Make a booking </a></p>'; 
                  return;
                  }
                 else
                 {echo '<p><a href="index.php">Return to the main page.</a></p>';
                  return; 
                }
             } 
             else 
             { 
                 echo '<p>Username/password combination is wrong!</p>'; 
             } 
         } 
         echo '<p><a href="index.php">Return to the main page.</a></p>'; 
     } 
     mysqli_close($db_connection); //close the connection once done
     return;
    }
 ?> 
 <!-- the action is to this page so the form will also submit to this page --> 
 <form method="POST" action="login.php"> 
     <h1>Customer Login</h1> 
     <label for="email">Email address: </label> 
     <input type="email" id="email" size="30" name="email" required>  
     <p> 
     <label for="password">Password: </label> 
     <input type="password" id="password" size="15" name="password" min="8" max="32" required> 
     </p>  
     <input type="submit" name="submit" value="Login"> 
 </form> 
 </body> 
 </html> 