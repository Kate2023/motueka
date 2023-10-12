<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit a booking</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>


</head>

<body>

    <h1>Edit a booking</h1>
    <h2><a href='currentbookings.php'>[Return to the bookings listing]</a><a href='index.php'>[Return to the main page]</a></h2>
    <?php
    include "config.php"; //load in any variables
    include "cleaninput.php";
    $db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
    //insert DB code from here onwards
    //check if the connection was good
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit; //stop processing the page further
    }

    //retrieve the bookingID from the URL
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking ID</h2>"; //simple error feedback
            exit;
        }
    }

    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {

        /* validate incoming data  */
        $error = 0; //clear our error flag
        $msg = 'Error: ';


        /* bookingID (sent via a form it is a string not a number so we try
   a type conversion!) */
        if (
            isset($_POST['id']) and !empty($_POST['id'])
            and is_integer(intval($_POST['id']))
        ) {
            $id = cleanInput($_POST['id']);
        } else {
            $error++; //bump the error flag
            $msg .= 'Invalid booking ID'; //append error message
            $id = 0;
        }

        if (
            isset($_POST['roomID']) &&
            !empty($_POST['roomID'])
        ) {
            $roomID = cleanInput($_POST['roomID']);
        } else {
            $error++;
            $msg .= ' Invalid room ID';
        }



        if (
            isset($_POST['checkindate']) &&
            !empty($_POST['checkindate'])
        ) {
            $checkindate = cleanInput($_POST['checkindate']);
        } else {
            $error++;
            $msg .= ' Invalid checkin date';
        }

        if (
            isset($_POST['checkoutdate']) &&
            !empty($_POST['checkoutdate'])&&($_POST['checkoutdate']>$_POST['checkindate'])
        ) {
            $checkoutdate = cleanInput($_POST['checkoutdate']);
        } else {
            $error++;
            $msg .= 'Invalid check out date';
        }



        if (
            isset($_POST['contactnumber']) &&
            !empty($_POST['contactnumber'])&& (is_numeric($_POST['contactnumber'])==1)
        ) {
            $contactnumber = cleanInput($_POST['contactnumber']);
        } else {
            $error++;
            $msg .= 'Invalid contactnumber';
        }

        if (
            isset($_POST['bookingextras']) &&
            !empty($_POST['bookingextras'])
        ) {
            $bookingextras = cleanInput($_POST['bookingextras']);
        }

        if (
            isset($_POST['roomreview']) &&
            !empty($_POST['roomreview'])
        ) {
            $roomreview = cleanInput($_POST['roomreview']);
        }



        //save the member data if the error flag is still clear and member id is > 0
        if ($error == 0 and $id > 0) {
            $query = "UPDATE booking SET roomID=?,checkindate=?,checkoutdate=?,contactnumber=?,bookingextras=?,roomreview=? WHERE bookingID=? ";
            $stmt = mysqli_prepare($db_connection, $query); //prepare the query
            mysqli_stmt_bind_param($stmt, 'isssssi', $roomID, $checkindate, $checkoutdate, $contactnumber, $bookingextras, $roomreview, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Booking details updated.</h2>"; 
             } else {
            echo "<h2>$msg</h2>";
        }
    }
    //locate the booking to edit by using the roomID
    //we also include thebooking ID in our form for sending it back for saving the data
    $query = "SELECT bookingID,roomID,checkindate,checkoutdate,contactnumber,bookingextras,roomreview FROM booking WHERE bookingID=" . $id;
    $result = mysqli_query($db_connection, $query);
    $rowcount = mysqli_num_rows($result);
    if ($rowcount > 0) {
        $row = mysqli_fetch_assoc($result);
    ?>

    <form method="POST" action="editbooking.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <p>
            <label for="roomID">Room (name, type,beds):</label>
            <select  name="roomID" id="roomID" required>
                <option value="1">Kellie,S,5</option>
                <option value="2">Herman,D,5</option>
                <option value="3">Scarlett,D,2</option>
                <option value="4">Jelani,S,2</option>
                <option value="5">Sonya,S,5</option>
                <option value="6">Miranda,S,4</option>
                <option value="7">Helen,S,2</option>
                <option value="8">Octavia,D,3</option>
                <option value="9">Gretchen,D,3</option>
                <option value="10">Bernard,S,5</option>
                <option value="11">Dacey,D,2</option>
                <option value="12">Preston,D,2</option>
                <option value="13">Dane,S,4</option>
                <option value="14">Cole,S,1</option>
            </select>
            
        </P>
        <p>
            <label for="checkindate">Checkin date: </label>
            <input type="text" class="datepicker" id="checkindate" name="checkindate" value="<?php echo $row['checkindate']; ?>" required>
        </p>
        <p>
            <label for="checkoutdate">Checkout date: </label>
            <input type="text" class="datepicker" id="checkoutdate" name="checkoutdate" value="<?php echo $row['checkoutdate']; ?>" required>
        </p>
        <p>
            <label for="contactnumber">Contact number:</label>
            <input type="tel" id="contactnumber" name="contactnumber" value="<?php echo $row['contactnumber']; ?>" maxlength="10" required>
        </p>
        <p>
            <label for="bookingextras">Booking extras: </label>
            <input type="text" id="bookingextras" name="bookingextras" maxlength="200" value="<?php echo $row['bookingextras']; ?>">
        </p>

        <p>
            <label for="roomreview">Room review: </label>
            <input type="text" id="roomreview" name="roomreview" maxlength="100" value="<?php echo $row['roomreview']; ?>">
        </p>

        <input type="submit" name="submit" value="Update">
        <a href='currentbookings.php'>[Cancel]</a>
    </form>
     
    <?php
    } else {
        echo "<h2>Booking not found with that ID</h2>"; //simple error feedback
    }
    mysqli_close($db_connection); //close the connection once done
    ?>


    <script>

        $(document).ready(function(){
            // get saved ID
            let selectedId = "<?php echo $row['roomID']; ?>";
            // get selectedID-th child of option list and add "selected" attribute
            $("#roomID").children().eq(selectedId - 1).attr("selected", "selected")
        })

        $(function() {
            $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
        });
    </script>
</body>

</html>