<?php
// If we have not pressed one of the bajillion buttons, get kicked!
if (!isset($_POST["TurnHuman"]) && !isset($_POST["Starve"]) && !isset($_POST["TurnSurv"]) && !isset($_POST["Zombify"]) && !isset($_POST["TurnMerc"]) && !isset($_POST["ChatBan"]) && !isset($_POST["ChatUnBan"]) && !isset($_POST["KickBtn"]) && !isset($_POST["SavePlayerInfoBtn"]) && !isset($_POST["Resurrect"]) && !isset($_POST["AddMissMis"]) && !isset($_POST["RemMissMis"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
$date = date('Y-m-d H:i:s');
	
// If we want to turn a zombie into a survivor:
if (isset($_POST["TurnHuman"]))
	{
	$TargetID = $_POST["UserIDHidden"];
	$TargetTeam = $_POST["UserTeamHidden"];
	$TargetGame = $_POST["UserGameHidden"];
	
	// Mod Zombies becomes Live Mods
	if ($TargetTeam == 1)
		{
		$SetHumanQuery = "UPDATE `hvzuserstate` SET `userteam` = 4 WHERE `userid` = $TargetID";
		}
	else
		{
		$SetHumanQuery = "UPDATE `hvzuserstate` SET `userteam` = 3 WHERE `userid` = $TargetID";
		}
	// Run Query
	mysqli_query($DBCon, $SetHumanQuery);
	
	// Provide the user with a new Tag
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
		17 => "G",
		18 => "H",
		19 => "I",
		20 => "J",
		21 => "K",
		22 => "L",
		23 => "M",
		24 => "N",
		25 => "P",
		26 => "R",
		27 => "T",
		28 => "U",
		29 => "V",
		30 => "W",
		31 => "X",
		32 => "Y",
	);

	// Get Info on currently existing codes
	$GetUsedTagsQuery = "SELECT `tagcode` FROM `hvztagnums`";
	$UsedTagsResult = mysqli_query($DBCon, $GetUsedTagsQuery);

	// Have not found a unique code, yet.
	$FoundUnique = 0;
	
	// While we are trying to generate a unique code.
	while ($FoundUnique == 0)
		{
		// Set to found, so we can prove it false.
		$FoundUnique = 1;
		// Creates a new Code
		$CurrCode = $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)];
		
		// Go through all results and hope that none of them were used before.
		while($row = mysqli_fetch_array($UsedTagsResult))
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
		
	// Found a unique? Shove it up there!
	$SetUserTagQuery = "INSERT INTO `hvztagnums`(`userId`, `gameId`, `tagcode`) VALUES ($TargetID,$TargetGame,'$CurrCode')";
	mysqli_query($DBCon, $SetUserTagQuery);
	}
// Turn a human or a merc into a Zombie
else if (isset($_POST["Zombify"]))
	{
	$TargetID = $_POST["UserIDHidden"];
	$TargetTeam = $_POST["UserTeamHidden"];
	
	// Human Admins becomes Zombie Admins.
	if ($TargetTeam == 4)
		{
		$SetZedQuery = "UPDATE `hvzuserstate` SET `userteam` = 1,`userlastfed` = '$date' WHERE `userid` = $TargetID";
		}
	// Regular Humans are the usual shamblers.
	else
		{
		$SetZedQuery = "UPDATE `hvzuserstate` SET `userteam` = 0,`userlastfed` = '$date' WHERE `userid` = $TargetID";
		}
	// Run Query
	mysqli_query($DBCon, $SetZedQuery);
	
	// Remove their current tag, since they are dead to stop them from self-feedingan suchlike.
	$RemoveTagQuery = "DELETE FROM `hvztagnums` WHERE `userId` = $TargetID";
	mysqli_query($DBCon, $RemoveTagQuery);
	
	// Change all active Arsenal Entries for the tagged player to state 2 (Zombiefied, Must Return)
	$RequestArsenalReturn = "UPDATE `hvzarsenalclaims` SET `claimstate`= 2,`claimdate`='$date' WHERE `claimerid` = $TargetID AND `claimstate` = 1";
	mysqli_query($DBCon, $RequestArsenalReturn);
	// Kill all non-active Queries: Zombies need no guns
	$RemoveNonReturnQueries = "DELETE FROM `hvzarsenalclaims` WHERE `claimerid` = $TargetID AND NOT `claimstate` = 2";
	mysqli_query($DBCon, $RemoveNonReturnQueries);
	}
else if (isset($_POST["Starve"]))
	{
	// Current Time
	date_default_timezone_set("America/New_York");
	$date = date('Y-m-d H:i:s');
	
	$TargetID = $_POST["UserIDHidden"];
	$TargetTeam = $_POST["UserTeamHidden"];
	$TargetGame = $_POST["UserGameHidden"];
	
	// Kills Zombie Mod into a Dead Mod
	if ($TargetTeam == 1)
		{
		$SetHungeredQuery = "UPDATE `hvzuserstate` SET `userteam` = -2 WHERE `userid` = $TargetID";
		}
	// All else are simple dead!
	else
		{
		$SetHungeredQuery = "UPDATE `hvzuserstate` SET `userteam` = -1 WHERE `userid` = $TargetID";
		}
	// Run Query
	mysqli_query($DBCon, $SetHungeredQuery);
	
	// Set Starvation Tag
	$SetStarvedTag = "INSERT INTO `hvztags`(`tagerid`, `taggedid`, `tagdate`, `taggameid`) VALUES (0,$TargetID,'$date',$TargetGame)";
	// Run Query
	mysqli_query($DBCon, $SetStarvedTag);
	// Set Starvation MiniEvent
	$SetStarveEventQuery = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES (10,'$date',$TargetID,$TargetGame)";
	// Run Query
	mysqli_query($DBCon, $SetStarveEventQuery);
	}
else if (isset($_POST["Resurrect"]))
	{
	$TargetID = $_POST["UserIDHidden"];
	$TargetTeam = $_POST["UserTeamHidden"];
	$TargetGame = $_POST["UserGameHidden"];
	
	// Return Mod Dead into Mod Zombies
	if ($TargetTeam == -2)
		{
		$SetRezQuery = "UPDATE `hvzuserstate` SET `userteam` = 1 WHERE `userid` = $TargetID";
		}
	// All other trash comes back as generic mash
	else
		{
		$SetRezQuery = "UPDATE `hvzuserstate` SET `userteam` = 0 WHERE `userid` = $TargetID";
		}
	// Run Query
	mysqli_query($DBCon, $SetRezQuery);
	
	// Remove Starvation events from the general view.
	$RemoveStarvationQuery = "DELETE FROM `hvztags` WHERE `taggedid`= $TargetID AND `tagerid` = 0 AND `taggameid`= $TargetGame";
	// Run Query
	mysqli_query($DBCon, $RemoveStarvationQuery);
	// Remove Starvation mini-event
	$RemoveMiniEventQuery = "DELETE FROM `hvzsmallevents` WHERE `evntType` = 10 AND `usrSubjctId` = $TargetID AND `relevantId` = $TargetGame";
	// Run Query
	mysqli_query($DBCon, $RemoveMiniEventQuery);
	}
// Turn a merc into a Survivor
else if (isset($_POST["TurnSurv"]))
	{
	$TargetID = $_POST["UserIDHidden"];
	$TargetTeam = $_POST["UserTeamHidden"];
	
	// Set team to 3, Survivors
	$SetRezQuery = "UPDATE `hvzuserstate` SET `userteam` = 3 WHERE `userid` = $TargetID";
	// Run Query
	mysqli_query($DBCon, $SetRezQuery);
	}
// Turn survivor into a Merc
else if (isset($_POST["TurnMerc"]))
	{
	$TargetID = $_POST["UserIDHidden"];
	$TargetTeam = $_POST["UserTeamHidden"];
	
	// Set team to 2, Mercenaries
	$SetRezQuery = "UPDATE `hvzuserstate` SET `userteam` = 2 WHERE `userid` = $TargetID";
	// Run Query
	mysqli_query($DBCon, $SetRezQuery);
	}
// Ban user from user chat
else if (isset($_POST["ChatBan"]))
	{
	$TargetID = $_POST["UserIDHidden"];
	// Shut em!
	$SetSilencingQuery = "UPDATE `hvzusers` SET `ChatState`= 1 WHERE `usrID` = $TargetID";
	// Run Query
	mysqli_query($DBCon, $SetSilencingQuery);
	}
// Unban user from chat
else if (isset($_POST["ChatUnBan"]))
	{
	$TargetID = $_POST["UserIDHidden"];
	// Shut em!
	$SetSilencingQuery = "UPDATE `hvzusers` SET `ChatState`= 0 WHERE `usrID` = $TargetID";
	// Run Query
	mysqli_query($DBCon, $SetSilencingQuery);
	}
// Kick the user from the game.
else if (isset($_POST["KickBtn"]))
	{
	// Start a session feed
	session_start();
	// Get User's Name and ID
	$AdminId = $_SESSION["userId"];
	$TargetID = $_POST["UserIDHidden"];
	
	// Get the player out of the game
	$SetEmRunningQuery = "UPDATE `hvzuserstate` SET `usergame` = 0 WHERE `userid` = $TargetID";
	// Run Kicking Query
	mysqli_query($DBCon, $SetEmRunningQuery);
	// Remove user's Tag
	$RemoveKickedTags = "DELETE FROM `hvztagnums` WHERE `userId` = $TargetID";
	// Run Tag Removal Query
	mysqli_query($DBCon, $RemoveKickedTags);
	// Give them a small message about getting kicked
	$PunishedEventQuery = "INSERT INTO `keenehvz`.`hvzsmallevents` (`evntId` ,`evntType` ,`evtDate` ,`usrSubjctId` ,`relevantId`) VALUES (NULL , '6', '$date' , '$TargetID', $AdminId)";
	// Run Small Event Query
	mysqli_query($DBCon, $PunishedEventQuery);
	}
// Changing user info
else if (isset($_POST["SavePlayerInfoBtn"]))
	{
	$TargetID = $_POST["UserChangingIDHidden"];
	$GetNewName = $_POST["EditPlayerName"];
	$GetNewDesc = $_POST["NewPlayerDesc"];
	
	// Start the query to edit info
	$SetNewInfoQuery = "UPDATE `hvzusrinfo` SET `usrname`='$GetNewName',`usrdesc`='$GetNewDesc'";
	// Extra options to drop player's avatar.
	if (isset($_POST["DropAvyCheck"]))
		{
		$SetNewInfoQuery = $SetNewInfoQuery . ",`usravy`=''";
		
		// Get Avy File name, to remove.
		$GetAvy = "SELECT `usravy` FROM `hvzusrinfo` WHERE `usrid` = $TargetID";
		$AvyName = mysqli_fetch_row(mysqli_query($DBCon, $GetAvy))[0];
		
		// Do not remove Default avatars, other may use them.
		if (substr($CurrentAvy, 0,3) != "Def")
			{
			unlink('Images/Avatars//' . $AvyName);
			}
		}
	// Finishes up the query.
	$SetNewInfoQuery = $SetNewInfoQuery . " WHERE `usrid` = $TargetID";
	
	// Run the Query
	mysqli_query($DBCon, $SetNewInfoQuery);
	}
	
header("Location: adminplayers.php");
die();
?>