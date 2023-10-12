<!DOCTYPE HTML>
<html><head> <title>Booking details</title></head>
<body>
<h1>Booking details view</h1> <h2>
<a href='currentbookings.php'>[Return to the booking listing]</a>
<a href='index.php'>[Return to the main page]</a></h2>
<?php
include "config.php"; //load in any variables
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}
//do some simple validation to check if id exists
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
 echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
 exit;
} 

//prepare a query and send it to the server
$query = "SELECT * FROM
booking,room WHERE bookingID='$id' AND booking.roomID=room.roomID"; 
$result = mysqli_query($db_connection,$query); 
$row_count= mysqli_num_rows($result);// check the result set for data 
if($row_count > 0) 
{  
   $row = mysqli_fetch_assoc($result);
   echo "<fieldset><legend>Room detail #".$row['roomID']."</legend><dl>"; 
   echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>";
   echo "<dt>Checkin date:</dt><dd>".$row['checkindate']."</dd>";
   echo "<dt>Checkout date:</dt><dd>".$row['checkoutdate']."</dd>";
   echo "<dt>Contact number:</dt><dd>".$row['contactnumber']."</dd>"; 
   echo "<dt>Extras:</dt><dd>".$row['bookingextras']."</dd>"; 
   echo "<dt>Room review:</dt><dd>".$row['roomreview']."</dd>"; 
   echo '</dl></fieldset>';  
}
else
{
	echo "<h2>No booking found!</h2>"; //suitable feedback
}
mysqli_free_result($result); //free any memory used by the query
mysqli_close($db_connection); //close the connection once done
?>
 
</body>
</html>
