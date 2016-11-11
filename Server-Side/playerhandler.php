<?php
// Get Session Info
session_start();

// Set Database Connection
include 'dbconnector.php';

if (!isset($_POST["Action"]))
	{
	header("Location: index.php");
	die();
	}

$Action = $_POST["Action"];
$TargetID = $_POST["FedID"];
	
// Feeding the Zmabie
if ($Action == 1)
	{
	$date = date('Y-m-d H:i:s');
	
	// Set the feeing Query
	$FeedingQuery = "UPDATE `hvzuserstate` SET `userlastfed`='$date' WHERE `userid` = $TargetID";
	// Run the Query
	mysqli_query($DBCon, $FeedingQuery);
	}
else if ($Action == 2)
	{
	// New Number of missed missions
	$NewMissedMissions = $_POST["NewValue"];
	// Set up the query
	$ChangeMissdQuery = "UPDATE `hvzuserstate` SET `missedmissions` = $NewMissedMissions WHERE `userid` = $TargetID";
	// Run the Query
	mysqli_query($DBCon, $ChangeMissdQuery);
	}
?>