 
<!DOCTYPE html> 
 <html lang="en"> 
   <head> 
     <title>Log Out</title> 
     <meta charset="UTF-8"> 
     <meta name="viewport" content="width=device-width, initial-scale=1"> 
   </head> 
   <body> 
   <h3> Logged out!</h3> 
     <?php 
        include "checksession.php"; 
        logout(); 
     ?> 
     
     <p><a href="index.php">You are now logged out. Return to the main page.</a></p> 
   </body> 
 </html> 