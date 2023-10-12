<?php
include "checksession.php"; 
if (!isAdmin())
{   echo 'Admin only';
    echo '<p><a href="index.php">Return to the main page.</a></p>';
    return;}
?> 


<!DOCTYPE html> 
<html lang="en"> 
    <head> 
        <title>List customers</title> 
        <script> 
function searchResult(searchstr) 
{ 
    if (searchstr.length==0) 
    { 
        return; 
    } 
    let xhRequest = new XMLHttpRequest(); 

    console.log("Searching for '" + searchstr + "'")

//call our php file that will look for a customer or customers matching the search string 
    xhRequest.open("GET","customersearch.php?searchfor="+searchstr,true); 
    xhRequest.responseType = 'json'; 
    xhRequest.send(); 
 
    xhRequest.onload = function() 
    { 
        console.log(xhRequest);
        if(xhRequest.status == 200) 
        { 
            const matchedCustomers = xhRequest.response; 
            var table = document.getElementById("tablecustomers"); //find the table in the HTML 
//clear any existing rows from any previous searches 
//if this is not cleared rows will just keep being added 
            var rowCount = table.rows.length; 
            for(var i = 1; i < rowCount; i++) 
            { 
//delete from the top - row 0 is the table header we keep 
                table.deleteRow(1);  
            } 
//if there are no surname matches then the matchedCustomers value will be null, so don't populate the table 
            if(matchedCustomers == null) 
            { 
                document.getElementById("message").innerText = "No matching names"; 
            } 
//otherwise, populate the table 
            else 
            { 
// matchedCustomers.length is the size of our array 
                console.log(matchedCustomers.length)
               for(var i=0; i < matchedCustomers.length; i++) 
                { 
                    var customerID = matchedCustomers[i]['customerID']; 
                    var fn = matchedCustomers[i]['firstname']; 
                    var ln = matchedCustomers[i]['lastname']; 
      
//concatenate our actions urls into a single string 
                    var urls = '<a href="viewcustomer.php?id='+customerID+'">[view]</a>'; 
                    urls += '<a href="editcustomer.php?id='+customerID+'">[edit]</a>'; 
                    urls += '<a href="deletecustomer.php?id='+customerID+'">[delete]</a>'; 
         
//create a table row with three cells   
                    tr = table.insertRow(-1); 
                    var tabCell = tr.insertCell(-1); 
                    tabCell.innerHTML = ln; //lastname 
                    var tabCell = tr.insertCell(-1); 
                    tabCell.innerHTML = fn; //firstname       
                    var tabCell = tr.insertCell(-1); 
                    tabCell.innerHTML = urls; //action URLS             
                } 
                document.getElementById("message").innerText = matchedCustomers.length + " customers found"; 
            } 
        } 
        else 
        { 
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
<h1>Customer List Search by Lastname</h1> 
<h2><a href='registercustomer.php'>[Create new Customer]</a><a href='index.php'>[Return to main page]</a> 
</h2> 
<form> 
  <label for="lastname">Lastname: </label> 
<!--  
    the onkeyup event calls the function on every key press 
    the onclick event sets the value of the text input field to an empty string if the mouse is clicked in the field 
--> 
  <input id="lastname" type="text" size="30"  
         onkeyup="searchResult(this.value)"  
         onclick="this.value = ''"  
         placeholder="Start typing a last name"> 
</form> 
<table id="tablecustomers" border="1"> 
<thead><tr><th>Last name</th><th>First name</th><th>Actions</th></tr></thead> 
</table> 
<p id="message"></p> 
</body> 
</html>