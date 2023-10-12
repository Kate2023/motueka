<?php
include "checksession.php"; 
if (!isAdmin())
{   echo 'Admin only';
    echo '<p><a href="index.php">Return to the main page.</a></p>';
    return;}
?> 


<!DOCTYPE HTML>
<html lang="en"> 
<head><title>Current bookings</title> </head>
<body>
<?php
include "config.php"; //load in any variables
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//prepare a query and send it to the server
$query = 'SELECT R.roomname,B.bookingID,B.checkindate, B.checkoutdate,
C.firstname,C.lastname 
FROM room R, booking B, customer C
WHERE  B.customerID = C.customerID AND B.roomID=R.roomID
 ORDER BY C.lastname
  ';
$result = mysqli_query($db_connection, $query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Current bookings</h1>
<h2><a href='makeabooking.php'>[Make a booking ]</a><a href='index.php'>[Return to mainpage]</a></h2>
<table border= "1">
<thead><tr><th>Booking (room,dates)</th><th>Customer</th><th>Actions</th></tr></thead>
<?php
if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
	  $id = $row['bookingID'];	
	  echo '<tr><td>'.$row['roomname'].", ".$row['checkindate'].", ".$row['checkoutdate'].'</td><td>'.$row['lastname'].", " .$row['firstname'].'</td>';
	  echo '<td><a href="bookingdetails.php?id='.$id.'">[view]</a>';
	  echo '<a href="editbooking.php?id='.$id.'">[edit]</a>';
      echo '<a href="deletebooking.php?id='.$id.'">[delete]</a></td>';
      echo '</tr>';
   }
} else echo "<h2>No booking found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($db_connection); //close the connection once done
?>
 
</table>
</body>
</html>