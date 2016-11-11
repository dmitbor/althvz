<?php
// Start a session feed
session_start();
if (!isset($_SESSION["userId"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';

$ViewerID = $_SESSION["userId"];

$CheckIfAdminQuery = "SELECT `userteam`, `usergame` FROM `hvzuserstate` WHERE `userid`=$ViewerID";
$ViewerTeam = mysqli_fetch_array(mysqli_query($DBCon, $CheckIfAdminQuery))[0];
$ViewerGame = mysqli_fetch_array(mysqli_query($DBCon, $CheckIfAdminQuery))[1];

if ($ViewerTeam != 1 && $ViewerTeam != 4 && $ViewerTeam != -2)
	{
	header("Location: playerstats.php");
	die();
	}

// If we are not in a game, we can't access this.
if ($ViewerGame == 0)
	{
	header("Location: joingame.php");
	die();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Keene State: Humans VS Zombies</title>
		<link rel="shortcut icon" type="image/x-icon" href="Images/favicon.ico">
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
		<link href="CSS/admin.css" rel="Stylesheet" type="text/css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="JS/adminmission.js"></script>
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	echo "<div class=\"MissionBackground\">";
		echo "<div class=\"MissionSelector\">";
		// Get Missions
		$GetAllGameMissionsQuery = "SELECT `missionId`,`missionState`,`missionHumanTitle`,`missionZombieTitle`,`missionHumanText`,`missionZombieText`,`missionPostHumanText`,`missionPostZombieText`,`ismisprimary`,`missionSpecificPlayers` FROM `hvzgamemissions` WHERE `gameId` = $UserGame";
		$Missions = mysqli_query($DBCon, $GetAllGameMissionsQuery);
		
		if ($Missions->num_rows>0)
			{
			while($MissionRow = mysqli_fetch_array($Missions))
				{
				echo "<form action=\"missionchanges.php\" method=\"post\" name=\"missionForm\" class=\"missionForm\">";
				echo "<p class=\"MissionName\">" . $MissionRow[2] . " / " . $MissionRow[3] . "</p>";
				echo "<p class=\"MissionState\">";
				if ($MissionRow[8] == 1)
					{
					$State = "Primary";
					}
				else if ($MissionRow[8] == 0)
					{
					$State = "Secondary";
					}
				
				switch ($MissionRow[1])
					{
					case 0:
					echo "(" . $State . " - Hidden)";
					break;
					case 1:
					echo "(" . $State . " - Shown)";
					break;
					case 2:
					echo "(" . $State . " - Finished - Tie)";
					break;
					case 3:
					echo "(" . $State . " - Finished - Zombies Win)";
					break;
					case 4:
					echo "(" . $State . " - Finished - Humans Win)";
					break;
					}
				if ($MissionRow[9] != 0)
					{
					echo " (Selected Players)";
					}
					
				echo "</p>";
				echo "<input type=\"hidden\" id=\"MissionIDHidden\" name=\"MissionIDHidden\" value=\"" . $MissionRow[0] . "\">";
				echo "<input type=\"hidden\" id=\"MissionNameHumanHidden\" name=\"MissionNameHumanHidden\" value=\"" . $MissionRow[2] . "\">";
				echo "<input type=\"hidden\" id=\"MissionNameZombieHidden\" name=\"MissionNameZombieHidden\" value=\"" . $MissionRow[3] . "\">";
				echo "<input type=\"hidden\" id=\"MissionDescHumanHidden\" name=\"MissionDescHumanHidden\" value=\"" . $MissionRow[4] . "\">";
				echo "<input type=\"hidden\" id=\"MissionDescZombieHidden\" name=\"MissionDescZombieHidden\" value=\"" . $MissionRow[5] . "\">";
				echo "<input type=\"hidden\" id=\"MissionEndingHumanHidden\" name=\"MissionEndingHumanHidden\" value=\"" . $MissionRow[6] . "\">";
				echo "<input type=\"hidden\" id=\"MissionEndingZombieHidden\" name=\"MissionEndingZombieHidden\" value=\"" . $MissionRow[7] . "\">";
				echo "<input type=\"hidden\" id=\"MissionState\" name=\"MissionState\" value=\"" . $MissionRow[1] . "\">";
				echo "<input type=\"hidden\" id=\"MissionPrime\" name=\"MissionPrime\" value=\"" . $MissionRow[8] . "\">";
				echo "<input type=\"hidden\" id=\"MissionAssociatedPlayers\" name=\"MissionAssociatedPlayers\" value=\"";
				$GetReleveantPlayersQuery = "SELECT `playerID`,`usrname` FROM `hvzmissionplayerassoc` LEFT JOIN `hvzusrinfo`  ON `playerID` = `usrid` WHERE `missionID` = " . $MissionRow[0] . "";
				$RelevantPlayers = mysqli_query($DBCon, $GetReleveantPlayersQuery);
				while($AssocRow = mysqli_fetch_array($RelevantPlayers))
					{
					echo "&lt;option value=&#39;" . $AssocRow[0] . "&#39;>" . $AssocRow[1] . "&lt;/option>";
					}
				echo "\">";
				echo "<button type=\"button\" class=\"ChngMsnBtn\" onmousedown=\"EditMissionMenu(this.form)\">Edit Mission Info</button>";
				echo "<button type=\"button\" class=\"DeleteMsnBtn\" onmousedown=\"DeleteMission(this.form)\">Delete Mission</button>";
				echo "</form>";
				}
			}
		echo "</div>";
		// Info Entry Form
		echo "<form action=\"missionchanges.php\" name=\"missionInfoForm\" method=\"post\">";
		echo "<p class=\"HumTitTxt\">Human Mission Title:</p>";
		echo "<input type=\"text\" id=\"missionHumanName\" name=\"missionHumanName\" class=\"missionHumanName\" value=\"\" maxlength=\"20\" autocomplete=\"off\">";
		echo "<p class=\"ZomTitTxt\">Zombie Mission Title:</p>";
		echo "<input type=\"text\" id=\"missionZombieName\" name=\"missionZombieName\" class=\"missionZombieName\" value=\"\" maxlength=\"20\" autocomplete=\"off\">";
		echo "<p class=\"HumDesTxt\">Human Mission Description:</p>";
		echo "<textarea id=\"MissionHumanDesc\" name=\"MissionHumanDesc\" class=\"MissionHumanDesc\" rows=\"5\" autocomplete=\"off\"></textarea>";
		echo "<p class=\"ZomDesTxt\">Zombie Mission Description:</p>";
		echo "<textarea id=\"MissionHumanEnd\" name=\"MissionHumanEnd\" class=\"MissionHumanEnd\" rows=\"5\" autocomplete=\"off\"></textarea>";
		echo "<p class=\"HumEndTxt\">Human Mission Ending Text:</p>";
		echo "<textarea id=\"MissionZombieDesc\" name=\"MissionZombieDesc\" class=\"MissionZombieDesc\" rows=\"5\" autocomplete=\"off\"></textarea>";
		echo "<p class=\"ZomEndTxt\">Zombie Mission Ending Text:</p>";
		echo "<textarea id=\"MissionZombieEnd\" name=\"MissionZombieEnd\" class=\"MissionZombieEnd\" rows=\"5\" autocomplete=\"off\"></textarea>";
		echo "<button type=\"button\" class=\"NewMsnBtn\" onmousedown=\"SetNewMission()\">Clear</button>";
		echo "<input type=\"submit\" id=\"SaveMissionData\" name=\"SaveMissionData\" value=\"Create\" class=\"SaveMissionData\">";
		echo "<input type=\"submit\" id=\"HideMission\" name=\"HideMission\" value=\"Hide\" class=\"HideMission\">";
		echo "<input type=\"submit\" id=\"SetSecondary\" name=\"SetSecondary\" value=\"Set as Secondary\" class=\"SetSecondary\">";
		echo "<input type=\"submit\" id=\"SetPrimary\" name=\"SetPrimary\" value=\"Set as Primary\" class=\"SetPrimary\">";
		echo "<input type=\"submit\" id=\"ChangeMissionState\" name=\"ChangeMissionState\" value=\"Start Off Mission\" class=\"ChangeMissionState\">";
		echo "<input type=\"submit\" id=\"SetHumanWinner\" name=\"SetHumanWinner\" value=\"Set Humans as Winners\" class=\"SetHumanWinner\">";
		echo "<input type=\"submit\" id=\"SetZombieWinner\" name=\"SetZombieWinner\" value=\"Set Zombies as Winners\" class=\"SetZombieWinner\">";
		echo "<input type=\"hidden\" id=\"HiddenID\" name=\"HiddenID\" value=\"\">";
		echo "<input type=\"hidden\" id=\"HiddenDeleteFlag\" name=\"HiddenDeleteFlag\" value=\"\">";
		echo "</form>";
	echo "</div>";
	echo "<div class=\"MissionUsersSide\" id=\"MissionUsersSide\">";
		echo "<form action=\"missionchanges.php\" name=\"missionInfoForm\" method=\"post\">";
		echo "<p class=\"PlayerListTxt\">List of Players:</p>";
		// Get players
		$GetAllPlayersQuery = "SELECT `usrid`,`usrname` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `hvzusrinfo`.`usrid` = `hvzuserstate` .`userid`  WHERE `usergame` = $UserGame";
		$Players = mysqli_query($DBCon, $GetAllPlayersQuery);
		echo "<select class=\"MissionPlayerAdderSelect\" size=\"1\">";
		while($PlayerRow = mysqli_fetch_array($Players))
			{
			echo "<option value=\"" . $PlayerRow[0] . "\">" . $PlayerRow[1] . "</option>";
			}
		echo "</select>";
		echo "<button type=\"button\" class=\"AddPlayerToMissionBtn\" onmousedown=\"AddPlayer(this.form)\">Add Player</button>";
		echo "<p class=\"PlayerAssocListTxt\">List of Associated Players:</p>";
		echo "<select name=\"MissionPlayersSelected[]\" id=\"MissionPlayersSelected\" class=\"MissionPlayersSelected\" size=\"5\" multiple onchange=\"ShowDeletion()\">";
		
		echo "</select>";
		echo "<button type=\"button\" id=\"DelPlyerFrmMsnBtn\" class=\"RemovePlayerFromMissionBtn\" onmousedown=\"RemovePlayer(this.form)\">Remove Player</button>";
		echo "<input type=\"submit\" id=\"SavePlayerSelection\" name=\"SavePlayerSelection\" value=\"Save Player Selection\" class=\"SavePlayerSelection\" onclick=\"SelectAll()\">";
		echo "<input type=\"hidden\" id=\"HiddenMissionID\" name=\"HiddenMissionID\" value=\"\">";
		echo "</form>";
	echo "</div>";
	?>
	</body>
</html>