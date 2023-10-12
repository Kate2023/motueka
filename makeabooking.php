<?php
include "checksession.php"; 

?> 

<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Make a booking</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

</head>

<body>

    <h1>Make a booking</h1>
    <h2><a href='currentbookings.php'>[Return to the bookings listing]</a><a href='index.php'>[Return to the main page]</a></h2>
    <?php
    include "config.php"; //load in any variables
    include "cleaninput.php";
    $db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
    if (mysqli_connect_errno()) {
        echo  "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit;
    }
    checkUser();
    //the data was sent using a form therefore we use the $_POST instead of $_GET
    //check if we are saving data first by checking if the submit button exists in the array
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {

        $error = 0; // set an error flag
        $msg = 'Error: ';



        // validate incoming data
        if (
            isset($_POST['customerID']) &&
            !empty($_POST['customerID'])
        ) {
            $customerID = cleanInput($_POST['customerID']);
        } else {
            $error++;
            $msg .= ' Invalid customer ID';
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
              
        
        // save the booking data if the error flag is still clear
        if ($error == 0) {
            $query = "INSERT INTO booking (roomID, checkindate, checkoutdate, contactnumber, bookingextras, roomreview,customerID) VALUES (?,?,?,?,?,?,?)";
            $stmt = mysqli_prepare($db_connection, $query); //prepare the query
            mysqli_stmt_bind_param($stmt, 'isssssi', $roomID, $checkindate, $checkoutdate, $contactnumber, $bookingextras, $roomreview, $customerID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Booking saved</h2>";
            return;
        } else {
            echo "<h2>$msg</h2>";
        }
        mysqli_close($db_connection); //close the connection once done
    }
    ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="customerID" value="<?php echo $_SESSION['customerID']; ?>">  
        <label>Room (name, type,beds):</label>
        <select name="roomID" id="roomID" required>
            <?php
            $query = 'SELECT * FROM room ORDER BY roomID';
            $result = mysqli_query($db_connection, $query);
            $row_count = mysqli_num_rows($result); // check the result set for data 
            if ($row_count > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value=" . $row['roomID'] . " >" . $row['roomname'] . "," . $row['roomtype'] . "," . $row['beds'] . " </option>";
                }
            } else {
                echo "<h2>No room found!</h2>"; //suitable feedback
            }
            mysqli_free_result($result); //free any memory used by the query
            mysqli_close($db_connection); //close the connection once done
            ?>
        </select>

        <p>
            <label for="checkindate">Checkin date: </label>
            <input type="text" class="datepicker" id="checkindate" name="checkindate" required>

        </p>
        <p>
            <label for="checkoutdate">Checkout date: </label>
            <input type="text" class="datepicker" id="checkoutdate" name="checkoutdate" required>
        </p>
        <p>
            <label for="contactnumber">Contact number:</label>
            <input type="tel" id="contactnumber" name="contactnumber" placeholder="0211234567" maxlength="10" required>
        </p>
        <p>
            <label for="extras">Booking extras: </label>
            <input type="text" id="bookingextras" name="bookingextras" maxlength="200">
        </p>

        <p>
            <label for="review">Room review: </label>
            <input type="text" id="roomreview" name="roomreview" maxlength="100">
        </p>
        <input type="submit" name="submit" value="Add"> <a href='currentbookings.php'>[Cancel]</a>
    </form>

    <br>

    <hr>

 

    <script>
    function searchResult(startdate,enddate) {
        let xhRequest = new XMLHttpRequest();
        xhRequest.open("GET", "roomsearch.php?startdate=" + startdate + "&" + "enddate=" + enddate, true);
        xhRequest.responseType = 'json';
        xhRequest.send();

        xhRequest.onload = function() {
            if (xhRequest.status == 200) {
                var rooms = this.response
                var table = document.getElementById("tablerooms"); //find the table in the HTML 
                //clear any existing rows from any previous searches 
                //if this is not cleared rows will just keep being added 
                var rowCount = table.rows.length;
                for (var i = 1; i < rowCount; i++) {
                    //delete from the top - row 0 is the table header we keep 
                    table.deleteRow(1);
                }
                //if there are no room matches then the matchedCustomers value will be null, so don't populate the table 
                if (rooms == null) {
                    document.getElementById("message").innerText = "No matching rooms";
                }
                //otherwise, populate the table 
                else {
                    // rooms.length is the size of our array 
                    for (var i = 0; i < rooms.length; i++) {
                        var roomid = rooms[i]['roomID'];
                        var roomname = rooms[i]['roomname'];
                        var roomtype = rooms[i]['roomtype'];
                        var beds = rooms[i]['beds'];


                        //create a table row with four cells  
                        tr = table.insertRow(-1);
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = roomid; //roomID
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = roomname; //roomname      
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = roomtype; //room type
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = beds; //beds  
                    }
                    document.getElementById("message").innerText = rooms.length + " rooms found";
                }
            } else {
                alert("Response status was " + xhRequest.status);
            }
        }

        xhRequest.onerror = function() 
    {  
        alert('Connection error'); 
    } 
} 
    
</script>
</head>

<body>

    <h1>Search for room availability </h1>

    <form>

        <label for="startdate">Start date: </label>
        <input type="text" class="datepicker" id="startdate" name="startdate" required>
        
        <label for="enddate">End date: </label>
        <input type="text" class="datepicker" id="enddate" name="enddate" required>
        <button type="button" onclick="searchResult(document.getElementById('startdate').value,document.getElementById('enddate').value)">Search availability</button>


    </form>
    <br>

    <table id="tablerooms" border="1">
        <thead>
            <tr><th>Room #</th><th>Room name </th><th>Type</th><th>Beds</th></tr>
        </thead>

    </table>
    <p id="message"></p>
    <script>
        $(function() {
            $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
        });
    </script>
</body>

</html>