<?php
// Start a session feed
session_start();

// Kick us out if we don't have a Tag Code given.
if (!isset($_POST["TagCode"]))
	{
	header("Location: tagpage.php");
	die();
	}

// Set Database Connection
include 'dbconnector.php';

// Get User's Name and ID
$MyId = $_SESSION["userId"];

// Current Date
$date = date('Y-m-d H:i:s');

// Which mission this is associated with?
$RelevantMissionID = $_POST["MissionAssoc"];

// Get the given tag.
$RecievedTag = mysqli_real_escape_string($DBCon, $_POST["TagCode"]);

// Get OUR info.
$GetBasicInfoQuery = "SELECT `userteam`,`usergame` FROM `hvzuserstate` WHERE `userid` = '$MyId'";
$UserInfoResult = mysqli_query($DBCon, $GetBasicInfoQuery);
	
// Get results.
$UserInfo = mysqli_fetch_row($UserInfoResult);
// Get Our Team and our Game
$UserTeam = $UserInfo[0];
$UserGame =  $UserInfo[1];

// Check if our game is not on pause:
$CheckForPause = "SELECT `gameState` FROM `hvzgame` WHERE `gameId` = $UserGame";
$GameState = mysqli_fetch_row(mysqli_query($DBCon, $CheckForPause))[0];

// If game is not immediately running.
if ($GameState != 2)
	{
	echo "<html>";
		echo "<body onload=\"document.frm1.submit()\">";
			echo "<form action=\"tagpage.php\" method=\"post\" name=\"frm1\">";
				echo "<input type=\"hidden\" name=\"LoadError\" value=\"1\"/>";
			echo "</form>";
		echo "</body>";
	echo "</html>";
	die();
	}

// It's an NPC?
if (strpos($RecievedTag, 'NPC') == 0)	
	{
	$FindVictimQuery = "SELECT `tagid`,`userId` FROM `hvztagnums` WHERE `tagcode` = '$RecievedTag'";
	$VictimFound = mysqli_query($DBCon, $FindVictimQuery);
	}
// Nope, human
else
	{
	// Try to see if we can find an appropriate tag
	$FindVictimQuery = "SELECT `tagid`,`userId` FROM `hvztagnums` WHERE `tagcode` = '$RecievedTag' AND `gameId` = $UserGame";
	$VictimFound = mysqli_query($DBCon, $FindVictimQuery);
	}

// If there were no rows found,
// Kick us back and tell us that given code is wrong.
if ($VictimFound->num_rows==0)
	{
	echo "<html>";
		echo "<body onload=\"document.frm1.submit()\">";
			echo "<form action=\"tagpage.php\" method=\"post\" name=\"frm1\">";
				echo "<input type=\"hidden\" name=\"LoadError\" value=\"2\"/>";
			echo "</form>";
		echo "</body>";
	echo "</html>";
	die();
	}
	
// Get Which tag to remove and which user to tag.
$TagRow = mysqli_fetch_array($VictimFound);
$TagToRemoveId = $TagRow[0];
$UserTaggedId = $TagRow[1];


// Creates a new tag entry.
$CreateTagQuery = "INSERT INTO `hvztags`(`tagerid`, `taggedid`, `tagdate`, `taggameid`) VALUES ($MyId, $UserTaggedId, '$date', $UserGame)";
mysqli_query($DBCon, $CreateTagQuery);

// Find the last query we just made
$FindLastTagQuery = "SELECT `tagid` FROM `hvztags` WHERE `tagerid` = $MyId AND `taggedid` = $UserTaggedId AND `taggameid` = $UserGame ORDER BY `tagid` DESC LIMIT 1";
$LastTagResult = mysqli_query($DBCon, $FindLastTagQuery);
$LastTagID = mysqli_fetch_array($LastTagResult)[0];

// Start setting the Zombie Feeding Query
$FeedZmabiesQuery = "UPDATE `hvzuserstate` SET `userlastfed`= '$date' WHERE `userid` = $MyId";

// If extra feds are set:
if (isset($_POST["Zomb1"]))
	{
	// Save value into variables
	$Extra = $_POST["Zomb1"];
	// If not zero, add to the query.
	if ($Extra != 0)
		{
		$FeedZmabiesQuery = $FeedZmabiesQuery . " OR `userid` = $Extra";
		}
	}
	
if (isset($_POST["Zomb2"]))
	{
	// Save value into variables
	$Extra = $_POST["Zomb2"];
	// If not zero, add to the query.
	if ($Extra != 0)
		{
		$FeedZmabiesQuery = $FeedZmabiesQuery . " OR `userid` = $Extra";
		}
	}
	
if (isset($_POST["Zomb3"]))
	{
	// Save value into variables
	$Extra = $_POST["Zomb3"];
	// If not zero, add to the query.
	if ($Extra != 0)
		{
		$FeedZmabiesQuery = $FeedZmabiesQuery . " OR `userid` = $Extra";
		}
	}
	
// Runs the Query to feed the zombies
mysqli_query($DBCon, $FeedZmabiesQuery);

// As long as it's not an NPC
if ($UserTaggedId != 0)
	{
	// Get the victim's team.
	$GetVictimTeamQuery = "SELECT `userteam` FROM `hvzuserstate` WHERE `userid` = $UserTaggedId";
	$VictimTeam = mysqli_fetch_array(mysqli_query($DBCon, $GetVictimTeamQuery))[0];

	$NextTeam = 0;

	if ($VictimTeam == 2 || $VictimTeam == 3)
		{
		$NextTeam = 0;
		}
	else if ($VictimTeam == 4)
		{
		$NextTeam = 1;
		}

	// Turn victim into a Zmabie
	$ConvertVictimToZambyQuery = "UPDATE `hvzuserstate` SET `userteam`=$NextTeam,`userlastfed`='$date' WHERE `userid` = $UserTaggedId";
	mysqli_query($DBCon, $ConvertVictimToZambyQuery);

	// We are regular zombie tag
	if ($UserTeam < 2)
		{
		$SmallTagType = 2;
		}
	// We are an OZ
	else if ($UserTeam == 5)
		{
		$SmallTagType = 4;
		}

	// Create a new small event for tagger.
	$CreateMiniEventQuery = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES ($SmallTagType, '$date', $MyId, $LastTagID)";
	mysqli_query($DBCon, $CreateMiniEventQuery);
		
	// We are regular zombie tag
	if ($UserTeam < 2)
		{
		$SmallTagType = 3;
		}
	// We are an OZ
	else if ($UserTeam == 5)
		{
		$SmallTagType = 5;
		}
		
	// Create a new small event for tagged user.
	$CreateMiniEventQuery = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES ($SmallTagType, '$date', $UserTaggedId, $LastTagID)";
	mysqli_query($DBCon, $CreateMiniEventQuery);

	// If we want to associate a tag with a mission:
	if ($RelevantMissionID != 0)
		{
		$CreateNewTagAssocQuery = "INSERT INTO `hvzmissionstagassoc`(`tagid`, `missionid`) VALUES ($LastTagID,$RelevantMissionID)";
		mysqli_query($DBCon, $CreateNewTagAssocQuery);
		}
		
	// Change all active Arsenal Entries for the tagged player to state 2 (Zombiefied, Must Return)
	$RequestArsenalReturn = "UPDATE `hvzarsenalclaims` SET `claimstate`= 2,`claimdate`='$date' WHERE `claimerid` = $UserTaggedId AND `claimstate` = 1";
	mysqli_query($DBCon, $RequestArsenalReturn);
	// Kill all non-active Queries: Zombies need no guns
	$RemoveNonReturnQueries = "DELETE FROM `hvzarsenalclaims` WHERE `claimerid` = $UserTaggedId AND NOT `claimstate` = 2";
	mysqli_query($DBCon, $RemoveNonReturnQueries);
	}
// Oh, we tagged an NPC. Good on you.
else if ($UserTaggedId == 0)
	{
	// Create a new small event for tagger.
	$CreateMiniEventQuery = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES (7, '$date', $MyId, $UserGame)";
	mysqli_query($DBCon, $CreateMiniEventQuery);
	}
// Remove the victim's tag, so it can't be reused.
$DeleteVictimTag = "DELETE FROM `hvztagnums` WHERE `tagid` = $TagToRemoveId";
mysqli_query($DBCon, $DeleteVictimTag);

// Go back to game stats to see the fruit of our labor
header("Location: gamestats.php");
die();
?>