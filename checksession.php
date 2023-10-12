<?php
session_start();
 

function isAdmin() {
 if (($_SESSION['loggedin'] == true) and ($_SESSION['role'] == 9))
     return true;
 else 
     return false;
  }
//function to check if the user is logged else send to the login page 
function checkUser() {
 
    $_SESSION['URI'] = '';    
    if (($_SESSION['loggedin'] == true) and ($_SESSION['role'] == 1||$_SESSION['role'] == 9))
       return TRUE;
    else {
       $_SESSION['URI'] = 'http://localhost/'.$_SERVER['REQUEST_URI']; //save current url for redirect     
       header('Location: http://localhost/motueka/login.php', true, 303);       
    }       
}

//just to show we are are logged in
function loginStatus() {
    $em = $_SESSION['email'];
    if ($_SESSION['loggedin'] == true) 
        echo "Logged in as $em ";
    else
        echo "No login credentials";            
}

//log a user in
function login($role,$email) {
   //simple redirect if a user tries to access a page they have not logged in to
   if ($_SESSION['loggedin'] == false and !empty($_SESSION['URI']))        
        $uri = $_SESSION['URI'];          
   else { 
     $_SESSION['URI'] =  'http://localhost/motueka/listcustomers.php';         
     $uri = $_SESSION['URI'];           
   }  

   $_SESSION['loggedin'] = false;        
   $_SESSION['role'] = $role;   
   $_SESSION['email'] = $email; 
   $_SESSION['URI'] = ''; 
   header('Location: '.$uri, true, 303);        
}

//simple logout function
function logout(){
  $_SESSION['loggedin'] = false;
  $_SESSION['role'] = 0;        
  $_SESSION['email'] = '';
  $_SESSION['URI'] = '';
  
}
?>