<?php
if (!isset($_POST["ChangeMissionState"]) && !isset($_POST["SaveMissionData"]) && !isset($_POST["SetHumanWinner"]) && !isset($_POST["SetZombieWinner"]) && (!isset($_POST["HiddenDeleteFlag"]) || $_POST["HiddenDeleteFlag"] == "") && !isset($_POST["SetPrimary"]) && !isset($_POST["SetSecondary"]) && !isset($_POST["HideMission"]) && !isset($_POST["SavePlayerSelection"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
	
// Let's find out our current game right off the bat:
session_start();
$myID = $_SESSION["userId"];

$GetCurrentGameQuery = "SELECT `usergame` FROM `hvzuserstate` WHERE `userid` = $myID";
$CurrentGame = mysqli_fetch_row(mysqli_query($DBCon, $GetCurrentGameQuery))[0];
	
// Sets the mission to End State - Tie State, Initially
if (isset($_POST["ChangeMissionState"]) && $_POST["ChangeMissionState"] == "End Mission")
	{
	$MissionID = $_POST["HiddenID"];
	
	// Set the mission's status to 2, complete
	$MissionCompletionQuery = "UPDATE `hvzgamemissions` SET `missionState` = 2 WHERE `missionId` = $MissionID";
	// Run Query
	mysqli_query($DBCon, $MissionCompletionQuery);
	
	// Alright, update everyone who is in the game, to go look at the mission changes:
	$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame";
	// Run Query
	mysqli_query($DBCon, $GetEveryonetoCheckMissionsQuery);
	}
// Sets the mission to 1, revealed and ongoing.
else if (isset($_POST["ChangeMissionState"]) && $_POST["ChangeMissionState"] == "Start Mission")
	{
	$MissionID = $_POST["HiddenID"];
	
	// Set the mission's status to 1, started
	$MissionCompletionQuery = "UPDATE `hvzgamemissions` SET `missionState` = 1 WHERE `missionId` = $MissionID";
	// Run Query
	mysqli_query($DBCon, $MissionCompletionQuery);
	
	// Get Info on the fact that mission is for specific players.
	$SpecialMissionCheckQuery = "SELECT `missionSpecificPlayers` FROM `hvzgamemissions` WHERE `missionId` = $MissionID";
	// Get the value for the mission
	$Special = mysqli_fetch_row(mysqli_query($DBCon, $SpecialMissionCheckQuery))[0];
	
	// If true, get a specific list of mission-relevant players
	if ($Special == 1)
		{
		// Get all the relevant player IDs.
		$GetRelevantPlayerIDsQuery = "SELECT `playerID` FROM `hvzmissionplayerassoc` WHERE `missionID` = $MissionID";
		// Run query
		$RelevantPlayerIDs = mysqli_query($DBCon, $GetRelevantPlayerIDsQuery);
		
		// Starting query to get relevant player's info
		$PlayerEmailTeam = "SELECT `usrEmail`,`userteam` FROM `hvzusers` LEFT JOIN `hvzuserstate` ON `usrID` = `userID` WHERE `usergame` = $CurrentGame AND (`usrEmailState` = 1 OR `usrEmailState` = 3) AND (";
		// Starting query to get relevant players to have updated UI
		$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame AND (";
		
		// Set up a query to get the info or relevant players
		while($PlayerRow = mysqli_fetch_array($RelevantPlayerIDs))
			{
			$PlayerEmailTeam = $PlayerEmailTeam . "`usrID` = " . $PlayerRow[0];
			$PlayerEmailTeam = $PlayerEmailTeam . " OR ";
			$GetEveryonetoCheckMissionsQuery = $GetEveryonetoCheckMissionsQuery . "`usrID` = " . $PlayerRow[0];
			$GetEveryonetoCheckMissionsQuery = $GetEveryonetoCheckMissionsQuery . " OR ";
			}
		$PlayerEmailTeam = $PlayerEmailTeam . "0)";
		$GetEveryonetoCheckMissionsQuery = $GetEveryonetoCheckMissionsQuery . "0)";
		}
	// If false, use general list of players.
	else if ($Special == 0)
		{
		// If both sides of the mission have names, do a regular user pick up
		if ($_POST["missionHumanName"] != "" && $_POST["missionZombieName"] != "")
			{
			// Get All Players in the game to send mission to
			$PlayerEmailTeam = "SELECT `usrEmail`,`userteam` FROM `hvzusers` LEFT JOIN `hvzuserstate` ON `usrID` = `userID` WHERE `usergame` = $CurrentGame AND (`usrEmailState` = 1 OR `usrEmailState` = 3)";
			
			// Alright, update everyone who is in the game, to go look at the mission changes:
			$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame";
			}
		// Zombie-Only mission
		else if ($_POST["missionHumanName"] == "")
			{
			// Get All Players in the game to send mission to
			$PlayerEmailTeam = "SELECT `usrEmail`,`userteam` FROM `hvzusers` LEFT JOIN `hvzuserstate` ON `usrID` = `userID` WHERE `usergame` = $CurrentGame AND (`usrEmailState` = 1 OR `usrEmailState` = 3) AND `userteam` < 2";
			
			// Alright, update everyone who is in the game, to go look at the mission changes:
			$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame AND `userteam` < 2";
			}
		// Survivors-Only missions
		else if ($_POST["missionZombieName"] == "")
			{
			// Get All Players in the game to send mission to
			$PlayerEmailTeam = "SELECT `usrEmail`,`userteam` FROM `hvzusers` LEFT JOIN `hvzuserstate` ON `usrID` = `userID` WHERE `usergame` = $CurrentGame AND (`usrEmailState` = 1 OR `usrEmailState` = 3) AND `userteam` > 1";
			
			// Alright, update everyone who is in the game, to go look at the mission changes:
			$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame AND `userteam` > 1";
			}
		}
	// Run UI Update Query
	mysqli_query($DBCon, $GetEveryonetoCheckMissionsQuery);
	
	// Set up the mailing info.
	$subjectHum = 'New KSC HVZ Mission (' . $_POST["missionHumanName"] . ')';
	$subjectZom = 'New KSC HVZ Mission (' . $_POST["missionZombieName"] . ')';
	
	$messageHum = $_POST["MissionHumanDesc"] . "\r\n\r\n\r\n(Please do not reply to this automated message.)";
	$messageZom = $_POST["MissionZombieDesc"] . "\r\n\r\n\r\n(Please do not reply to this automated message.)";
	
	$headers = 'From: survivorradio@kschvz.com' . "\r\n" .
    'Reply-To: survivorradio@kschvz.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
	// Get the players who should get the email.
	$PlayerResult = mysqli_query($DBCon, $PlayerEmailTeam);
	
	// Go through every email, and send the message to all.
	while($PlayerRow = mysqli_fetch_array($PlayerResult))
		{
		// For Humans
		if ($PlayerRow[1] > 1)
			{
			$to = $PlayerRow[0];
			mail($to, $subjectHum, $messageHum, $headers);
			}
		// For Zombies
		else if ($PlayerRow[1] < 2)
			{
			$to = $PlayerRow[0];
			mail($to, $subjectZom, $messageZom, $headers);
			}
		}
	}
// Creates a new Mission, Puts it into Database. Immediately makes it visible, too.
else if (isset($_POST["ChangeMissionState"]) && $_POST["ChangeMissionState"] == "Start Off Mission")
	{
	$HumanName = mysqli_real_escape_string($DBCon, $_POST["missionHumanName"]);
	$ZombieName = mysqli_real_escape_string($DBCon, $_POST["missionZombieName"]);
	$HumanDesc = mysqli_real_escape_string($DBCon, $_POST["MissionHumanDesc"]);
	$ZombieDesc = mysqli_real_escape_string($DBCon, $_POST["MissionZombieDesc"]);
	$HumanEnd = mysqli_real_escape_string($DBCon, $_POST["MissionHumanEnd"]);
	$ZombieEnd = mysqli_real_escape_string($DBCon, $_POST["MissionZombieEnd"]);
	
	$PublishNewGameQuery = "INSERT INTO `hvzgamemissions`(`gameId`, `missionState`, `missionHumanTitle`, `missionZombieTitle`, `missionHumanText`, `missionZombieText`, `missionPostHumanText`, `missionPostZombieText`) VALUES ($CurrentGame,1,'$HumanName','$ZombieName','$HumanDesc','$ZombieDesc','$HumanEnd','$ZombieEnd')";
	// Run Query
	mysqli_query($DBCon, $PublishNewGameQuery);
	
	// Set up the mailing info.
	$subjectHum = 'New KSC HVZ Mission (' . $_POST["missionHumanName"] . ')';
	$subjectZom = 'New KSC HVZ Mission (' . $_POST["missionZombieName"] . ')';
	
	$messageHum = $_POST["MissionHumanDesc"] . "\r\n\r\n\r\n(Please do not reply to this automated message. It is not worth the time spent, since no one will actually see it as it's one-way.)";
	$messageZom = $_POST["MissionZombieDesc"] . "\r\n\r\n\r\n(Please do not reply to this automated message. It is not worth the time spent, since no one will actually see it as it's one-way.)";
	
	$headers = 'From: survivorradio@kschvz.com' . "\r\n" .
    'Reply-To: survivorradio@kschvz.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
	// If both sides of the mission have names, do a regular user pick up
		if ($_POST["missionHumanName"] != "" && $_POST["missionZombieName"] != "")
			{
			// Get All Players in the game to send mission to
			$PlayerEmailTeam = "SELECT `usrEmail`,`userteam` FROM `hvzusers` LEFT JOIN `hvzuserstate` ON `usrID` = `userID` WHERE `usergame` = $CurrentGame AND (`usrEmailState` = 1 OR `usrEmailState` = 3)";
			
			// Alright, update everyone who is in the game, to go look at the mission changes:
			$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame";
			}
		// Zombie-Only mission
		else if ($_POST["missionHumanName"] == "")
			{
			// Get All Players in the game to send mission to
			$PlayerEmailTeam = "SELECT `usrEmail`,`userteam` FROM `hvzusers` LEFT JOIN `hvzuserstate` ON `usrID` = `userID` WHERE `usergame` = $CurrentGame AND (`usrEmailState` = 1 OR `usrEmailState` = 3) AND `userteam` < 2";
			
			// Alright, update everyone who is in the game, to go look at the mission changes:
			$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame AND `userteam` < 2";
			}
		// Survivors-Only missions
		else if ($_POST["missionZombieName"] == "")
			{
			// Get All Players in the game to send mission to
			$PlayerEmailTeam = "SELECT `usrEmail`,`userteam` FROM `hvzusers` LEFT JOIN `hvzuserstate` ON `usrID` = `userID` WHERE `usergame` = $CurrentGame AND (`usrEmailState` = 1 OR `usrEmailState` = 3) AND `userteam` > 1";
			
			// Alright, update everyone who is in the game, to go look at the mission changes:
			$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame AND `userteam` > 1";
			}
	
	// Run UI Update Query
	mysqli_query($DBCon, $GetEveryonetoCheckMissionsQuery);
	
	// Get the players who should get the email.
	$PlayerResult = mysqli_query($DBCon, $PlayerEmailTeam);
	
	// Go through every email, and send the message to all.
	while($PlayerRow = mysqli_fetch_array($PlayerResult))
		{
		// For Humans
		if ($PlayerRow[1] > 1)
			{
			$to = $PlayerRow[0];
			mail($to, $subjectHum, $messageHum, $headers);
			}
		// For Zombies
		else if ($PlayerRow[1] < 2)
			{
			$to = $PlayerRow[0];
			mail($to, $subjectZom, $messageZom, $headers);
			}
		}
	}
// Saves new data for existng mission to creates a new mission.
else if (isset($_POST["SaveMissionData"]))
	{
	$HumanName = mysqli_real_escape_string($DBCon, $_POST["missionHumanName"]);
	$ZombieName = mysqli_real_escape_string($DBCon, $_POST["missionZombieName"]);
	$HumanDesc = mysqli_real_escape_string($DBCon, $_POST["MissionHumanDesc"]);
	$ZombieDesc = mysqli_real_escape_string($DBCon, $_POST["MissionZombieDesc"]);
	$HumanEnd = mysqli_real_escape_string($DBCon, $_POST["MissionHumanEnd"]);
	$ZombieEnd = mysqli_real_escape_string($DBCon, $_POST["MissionZombieEnd"]);
	$MissionID = mysqli_real_escape_string($DBCon, $_POST["HiddenID"]);
	
	if ($_POST["SaveMissionData"] == "Create")
		{
		$PublishNewGameQuery = "INSERT INTO `hvzgamemissions`(`gameId`, `missionState`, `missionHumanTitle`, `missionZombieTitle`, `missionHumanText`, `missionZombieText`, `missionPostHumanText`, `missionPostZombieText`) VALUES ($CurrentGame,0,'$HumanName','$ZombieName','$HumanDesc','$ZombieDesc','$HumanEnd','$ZombieEnd')";
		// Run Query
		mysqli_query($DBCon, $PublishNewGameQuery);
		}
	else if ($_POST["SaveMissionData"] == "Save")
		{
		$UpdateMissionInfoQuery = "UPDATE `hvzgamemissions` SET `missionHumanTitle`='$HumanName',`missionZombieTitle`='$ZombieName',`missionHumanText`='$HumanDesc',`missionZombieText`='$ZombieDesc',`missionPostHumanText`='$HumanEnd',`missionPostZombieText`='$ZombieEnd' WHERE `missionId` = $MissionID";
		// Run Query
		mysqli_query($DBCon, $UpdateMissionInfoQuery);
		
		// Get mission's state:
		$MissStateQuery = "SELECT * FROM `hvzgamemissions` WHERE `missionId` = $MissionID";
		$MissState = mysqli_fetch_row(mysqli_query($DBCon, $MissStateQuery))[0];
		
		// If the mission is not hidden, update checks for everyone.
		if ($MissState > 0)
			{
			// Alright, update everyone who is in the game, to go look at the mission changes:
			$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame";
			// Run Query
			mysqli_query($DBCon, $GetEveryonetoCheckMissionsQuery);
			}
		}
	}
// If a mission has a Deletion Flag set, delete it.
else if (isset($_POST["HiddenDeleteFlag"]) && $_POST["HiddenDeleteFlag"] != "")
	{
	$MissionID = $_POST["HiddenDeleteFlag"];
	$DeleteMissionQuery = "DELETE FROM `hvzgamemissions` WHERE `missionId`=$MissionID";
	$DeleteMissionPlayerAssociationQuery = "DELETE FROM `hvzmissionplayerassoc` WHERE `missionID` = $MissionID";
	// Run Queries
	mysqli_query($DBCon, $DeleteMissionQuery);
	mysqli_query($DBCon, $DeleteMissionPlayerAssociationQuery);
	}
// Sets zombies as winners of the mission.
else if (isset($_POST["SetZombieWinner"]))
	{
	$MissionID = mysqli_real_escape_string($DBCon, $_POST["HiddenID"]);
	
	if ($_POST["SetZombieWinner"] == "Set Zombies as Winners")
		{
		$ZombieWinQuery = "UPDATE `hvzgamemissions` SET `missionState`= 3 WHERE `missionId` = $MissionID";
		}
	else
		{
		$ZombieWinQuery = "UPDATE `hvzgamemissions` SET `missionState`= 2 WHERE `missionId` = $MissionID";
		}
	// Run Query
	mysqli_query($DBCon, $ZombieWinQuery);
	
	// Alright, update everyone who is in the game, to go look at the mission changes:
	$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame";
	// Run Query
	mysqli_query($DBCon, $GetEveryonetoCheckMissionsQuery);
	}
// Sets Humans as winners of the mission
else if (isset($_POST["SetHumanWinner"]))
	{
	$MissionID = mysqli_real_escape_string($DBCon, $_POST["HiddenID"]);
	
	if ($_POST["SetHumanWinner"] == "Set Humans as Winners")
		{
		$HumieWinQuery = "UPDATE `hvzgamemissions` SET `missionState`= 4 WHERE `missionId` = $MissionID";
		}
	else 
		{
		$HumieWinQuery = "UPDATE `hvzgamemissions` SET `missionState`= 2 WHERE `missionId` = $MissionID";
		}
	// Run Query
	mysqli_query($DBCon, $HumieWinQuery);
	
	// Alright, update everyone who is in the game, to go look at the mission changes:
	$GetEveryonetoCheckMissionsQuery = "UPDATE `hvzuserstate` SET `checkmissions` = 1 WHERE `usergame` = $CurrentGame";
	// Run Query
	mysqli_query($DBCon, $GetEveryonetoCheckMissionsQuery);
	}
// We're trying to set specific mission to be Primary
else if (isset($_POST["SetPrimary"]))
	{
	$MissionID = mysqli_real_escape_string($DBCon, $_POST["HiddenID"]);
	
	$SetPrimaryQuery = "UPDATE `hvzgamemissions` SET `ismisprimary` = 1 WHERE `missionId` = $MissionID";
	// Run Query
	mysqli_query($DBCon, $SetPrimaryQuery);
	}
// Set mission to Bonus, Secondary
else if (isset($_POST["SetSecondary"]))
	{
	// Get Mission ID
	$MissionID = mysqli_real_escape_string($DBCon, $_POST["HiddenID"]);
	
	$SetSecondaryQuery = "UPDATE `hvzgamemissions` SET `ismisprimary` = 0 WHERE `missionId` = $MissionID";
	// Run Query
	mysqli_query($DBCon, $SetSecondaryQuery);
	}
// Hide the mission from everyone. Also resets it to not won.
else if (isset($_POST["HideMission"]))
	{
	// Get Mission ID
	$MissionID = mysqli_real_escape_string($DBCon, $_POST["HiddenID"]);
	
	// Set mission to state 0, hidden.
	$SetMissionHidenQuery = "UPDATE `hvzgamemissions` SET `missionState`=0 WHERE `missionId` = $MissionID";
	mysqli_query($DBCon, $SetMissionHidenQuery);
	}
// Set up the mission-associated players
else if (isset($_POST["SavePlayerSelection"]))
	{
	// A: Delete existing player-mission associations for the existing mission
	$MissionID = $_POST["HiddenMissionID"];
	$DeleteExistingAssociationQuery = "DELETE FROM `hvzmissionplayerassoc` WHERE `missionID` = $MissionID";
	mysqli_query($DBCon, $DeleteExistingAssociationQuery);
	
	if (isset($_POST["MissionPlayersSelected"]))
		{
		$UsersArray = $_POST["MissionPlayersSelected"];
		// B: Create new associations between players and the mission.
		$AddingUserQuery = "INSERT INTO `keenehvz`.`hvzmissionplayerassoc` (`missionID` ,`playerID`) VALUES ";
		foreach ($UsersArray as $CurUser)
			{
			$AddingUserQuery = $AddingUserQuery . "('$MissionID',  '$CurUser'),";
			}
		$AddingUserQuery = substr($AddingUserQuery, 0, strlen($AddingUserQuery) - 1) . ";";
		mysqli_query($DBCon, $AddingUserQuery);
		// C: If there are associated players in the mission, set it as having such.
		$ChangeMissionAssocStateQuery = "UPDATE `hvzgamemissions` SET `missionSpecificPlayers`= 1 WHERE `missionId` = $MissionID";
		mysqli_query($DBCon, $ChangeMissionAssocStateQuery);
		}
	else
		{
		// Set Mission's player association variable to 0.
		$ChangeMissionAssocStateQuery = "UPDATE `hvzgamemissions` SET `missionSpecificPlayers`= 0 WHERE `missionId` = $MissionID";
		mysqli_query($DBCon, $ChangeMissionAssocStateQuery);
		}
	}
	
// After it's finishing the command, go back to mission list.
header("Location: adminmissions.php");
die();
?>