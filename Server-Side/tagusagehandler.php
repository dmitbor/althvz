<?php
// Set Database Connection
include 'dbconnector.php';

if (!isset($_POST["ExpandableKey"]))
	{
	header("Location: index.php");
	die();
	}
	
$SelectedCode = $_POST["ExpandableKey"];
$EndValue = $_POST["ValueGiven"];
	
// Set the query to update the tag's use
$CheckUsedQuery = "UPDATE `hvztagnums` SET `faketagused`=$EndValue WHERE `tagcode` = '$SelectedCode'";
// Run the Query
mysqli_query($DBCon, $CheckUsedQuery);
?>