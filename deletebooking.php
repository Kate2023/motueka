<!DOCTYPE HTML><html>
<head> <title>Delete booking</title></head>
<body>
<h1>Booking preview before deletion </h1> <h2>
<a href='currentbookings.php'>[Return to the booking listing]</a>
<a href='index.php'>[Return to the main page]</a></h2>
 
<?php
include "config.php"; //load in any variables
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD,DBDATABASE);
//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}
 
 
//function to clean input but not validate type and content
function cleanInput($data) {  
    return htmlspecialchars(stripslashes(trim($data)));
  }
  
  //retrieve the bookingID from the URL
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
      $id = $_GET['id'];
      if (empty($id) or !is_numeric($id)) {
          echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
          exit;
      } 
  }

 //check if we are saving data first by checking if the submit button exists in the array
 if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {     
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
function clean_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
};
//bookingID (sent via a form it is a string not a number so we try a type conversion!)    
if (isset($_POST['id']) and !empty($_POST['id']) 
    and is_integer(intval($_POST['id'])))
{
    $id = clean_input($_POST['id']); 
}
else
{
    $error++; //bump the error flag
    $msg .= 'Invalid booking ID '; //append error message
    $id = 0;  
}        

//save the booking data if the error flag is still clear and booking id is > 0
if ($error == 0 and $id > 0) {
    $query = "DELETE FROM booking WHERE bookingID=?";
    $stmt = mysqli_prepare($db_connection, $query); //prepare the query
    mysqli_stmt_bind_param($stmt,'i', $id); 
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);    
    echo "<h2>Booking deleted.</h2>";     
    return;
    
} else { 
  echo "<h2>$msg</h2>";
}      
}

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
?>
    <form method= "POST" action= "deletebooking.php">
     <h2> Are you sure you want to delete the booking?</h2>
     <input type="hidden" name="id" value="<?php echo $id; ?>">
     <input type="submit" name="submit" value="Delete">
     <a href= "currentbookings.php">[Cancel]</a>
    </form>   
<?php }
else
{
	echo "<h2>No booking found,possibly deleted! </h2>"; //suitable feedback
}
mysqli_free_result($result); //free any memory used by the query
mysqli_close($db_connection); //close the connection once done
?>
 
    
 

</body>
</html>