<?php
// Start a session feed
session_start();
// If we don't have a UserID and if we got here without a game code, get kicked out!
if (!isset($_SESSION["userId"]) || !isset($_POST["GameCode"]))
	{
	header("Location: index.php");
	die();
	}
if (!isset($_POST["GameID"]))
	{
	header("Location: playerstats.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
$date = date('Y-m-d H:i:s');
	
// Get the variables we'll need.
$GameID = $_POST["GameID"];
$MyID = $_SESSION["userId"];
$GivenCode = $_POST["GameCode"];
$Team = 3;
$FoundUnique = 0;

// Find the Game's access code:
$GetGameCodeQuery = "SELECT `gameAcsCode` FROM `hvzgame` WHERE `gameId` = $GameID";
$GameCode = mysqli_fetch_array(mysqli_query($DBCon, $GetGameCodeQuery))[0];

// Check agains the code. Throw an error if it's wrong.
if ($GivenCode != $GameCode)
	{
	echo "<html>";
		echo "<body onload=\"document.frm1.submit()\">";
			echo "<form action=\"gameenter.php\" method=\"post\" name=\"frm1\">";
				echo "<input type=\"hidden\" name=\"LoadError\" value=\"1\"/>";
				echo "<input type=\"hidden\" name=\"gameID\" value=\"$GameID\"/>";
			echo "</form>";
		echo "</body>";
	echo "</html>";
	die();
	}

// Check if we're an Admin:
$CheckPlayerSide = "SELECT `userteam` FROM `hvzuserstate` WHERE `userid` = $MyID";
$AdminCheck = mysqli_fetch_array(mysqli_query($DBCon, $CheckPlayerSide))[0];
		
if (isset($_POST["AmMerc"]))
	{
	$Team = 2;
	}
else if (isset($_POST["WannaOZ"]))
	{
	$Team = 6;
	}
else if ($AdminCheck == 1 || $AdminCheck == 4 || $AdminCheck == -2)
	{
	$Team = 4;
	}

// Tag Code values array.
$CharAray = array(
	1 => "1",
	2 => "2",
	3 => "3",
	4 => "4",
	5 => "5",
	6 => "6",
	7 => "7",
	8 => "8",
	9 => "9",
	10 => "0",
	11 => "A",
	12 => "B",
	13 => "C",
	14 => "D",
	15 => "E",
	16 => "F",
	17 => "H",
	18 => "I",
	19 => "J",
	20 => "K",
	21 => "L",
	22 => "M",
	23 => "N",
	24 => "P",
	25 => "R",
	26 => "T",
	27 => "U",
	28 => "V",
	29 => "W",
	30 => "X",
	31 => "Y",
);

// Get Info on currently existing codes
	$GetTagsQuery = "SELECT `tagcode` FROM `hvztagnums`";
	$UserInfoResult = mysqli_query($DBCon, $GetTagsQuery);

// While we are trying to generate a unique code.
while ($FoundUnique == 0)
	{
	// Set to found.
	$FoundUnique = 1;
	// Creates a new Code
	$CurrCode = $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)];
	
	// Go through all results and hope that none of them were used before.
	while($row = mysqli_fetch_array($UserInfoResult))
		{
		// If we find anything that is like the one we generated, mark this code as a failure and redo it all over.
		if ($row[0] == $CurrCode)
			{
			$FoundUnique = 0;
			}
		// Do not allow for INF (Info)/NPC (Non Player Tag) tags to get mixed in.
		if (substr($CurrCode, 0, 3) == "INF" || substr($CurrCode, 0, 3) == "NPC")
			{
			$FoundUnique = 0;
			}
		}
	}
	
// Hey, we've got through, go to next step!
// Insert a new tag for the player.
$InsertPlayerTagQuery = "INSERT INTO `hvztagnums`(`userId`, `gameId`, `tagcode`) VALUES ($MyID,$GameID,'$CurrCode')";
mysqli_query($DBCon, $InsertPlayerTagQuery);

// Set Player's game and role.
$PlaceUserIntoGameQuery = "UPDATE `hvzuserstate` SET `userteam`= $Team ,`userlastfed` = '$date',`usergame`= $GameID,`missedmissions`= 0 WHERE `userid` = $MyID";
mysqli_query($DBCon, $PlaceUserIntoGameQuery);

// Create a mini event for the user about joining the game.
$JoinEvent = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES (1, '$date', $MyID, $GameID)";
mysqli_query($DBCon, $JoinEvent);

// Go to the stats of the game which player just joined.
header("Location: gamestats.php");
die();
?>