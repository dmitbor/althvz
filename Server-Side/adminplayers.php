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
$ViewerResultsUnamanged = mysqli_query($DBCon, $CheckIfAdminQuery);
$ViewerResults = mysqli_fetch_array($ViewerResultsUnamanged);
$ViewerTeam = $ViewerResults[0];
$ViewerGame = $ViewerResults[1];

// If we are not an admin, kick us out!
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
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="JS/adminplayer.js"></script>
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// If we are in a game, we can only mod people in the same game:
	$GetPlayerInfoQuery = "SELECT `userid`,`userteam`,`userlastfed`,`usrname`,`usrdesc`,`usravy`,`missedmissions` FROM (SELECT * FROM `hvzuserstate` LEFT JOIN `hvzusrinfo` ON `hvzuserstate`.`userid`=`hvzusrinfo`.`usrid`) AS UsersInfo LEFT JOIN `hvzgame`ON UsersInfo.`usergame` = `hvzgame`.`gameId` WHERE `usergame` = $UserGame";
	$PlayerInfoResult = mysqli_query($DBCon, $GetPlayerInfoQuery);
	
	echo "<div class=\"AllPlayersDiv\">";
	while($row = mysqli_fetch_array($PlayerInfoResult))
		{
		echo "<form action=\"playerchanges.php\" name=\"PlayerForm\" method=\"post\" class=\"PlayerForm\">";
			echo "<p class=\"PlayerName\">" . $row[3] . "</p>";
			echo "<img class=\"PlayerAvatar\"";
			// Actual Avatar
			if ($row[5] != "")
				{
				echo "src=\"Images/Avatars//" . $row[5] . "\">";
				}
			// Zombie
			else if ($row[1] < 2)
				{
				echo " src=\"Images/DefaultAvatars/DefaultZombieAv.png\">";
				}
			// Human
			else if ($row[1] > 1 && $row[1] < 5)
				{
				echo "  src=\"Images/DefaultAvatars/DefaultHumanAv.png\">";
				}
			// OZ
			else if ($row[1] > 4)
				{
				echo " src=\"Images/DefaultAvatars/DefaultOZAv.png\">";
				}
				
			echo "<input type=\"hidden\" id=\"UserIDHidden\" name=\"UserIDHidden\" value=\"" . $row[0] . "\">";
			echo "<input type=\"hidden\" id=\"UserTeamHidden\" name=\"UserTeamHidden\" value=\"" . $row[1] . "\">";
			echo "<input type=\"hidden\" id=\"UserNameHidden\" name=\"UserNameHidden\" value=\"" . $row[3] . "\">";
			echo "<input type=\"hidden\" id=\"UserDescHidden\" name=\"UserDescHidden\" value=\"" . $row[4] . "\">";
			echo "<input type=\"hidden\" id=\"UserGameHidden\" name=\"UserGameHidden\" value=\"" . $UserGame . "\">";
			echo "<button type=\"button\" class=\"EditBtn\" onmousedown=\"EditPlayerMenu(this.form)\">Edit Player Info</button>";
				
			// Zombies gets Starvation Counter
			if ($row[1] < 2 && $row[1] > -1)
				{
				echo "<p class=\"StarveCounter\" id=\"StarveCounter\">Last Fed: " . date("jS F, Y \a\\t g:i a", strtotime($row[2])) . "<br>Will Die On: " . date("jS F, Y \a\\t g:i a", strtotime($row[2] . "+2 days")) . "</p>";
				}
			// Humans get a Missed Mission Counter
			else if ($row[1] > 1)
				{
				echo "<p class=\"MissedMissions\">Missed Missions: " . $row[6] . "</p>";
				echo "<button type=\"button\" id=\"AddMissMis\" name=\"AddMissMis\" class=\"AddMissMis\" onmousedown=\"ChangeMissedMissions(this.form," . $row[0] . ",'Add')\">+</button>";
				echo "<button type=\"button\" id=\"RemMissMis\" name=\"RemMissMis\" ";
				// Only allow to remove missed missions if any were missed
				if ($row[6] == 0)
					{
					echo "class=\"RemMissMisHid\" onmousedown=\"ChangeMissedMissions(this.form," . $row[0] . ",'Remove')\">-</button>";
					}
				else
					{
					echo "class=\"RemMissMis\" onmousedown=\"ChangeMissedMissions(this.form," . $row[0] . ",'Remove')\">-</button>";
					}
				
				echo "<input type=\"hidden\" id=\"UserMissedMiss\" name=\"UserMissedMiss\" value=\"" . $row[6] . "\">";
				}
				
			switch ($row[1])
				{
				case -2:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\deadminmark.png\">";
				echo "<input type=\"submit\" id=\"GoodBtn\" name=\"Resurrect\" value=\"Resurrect\" class=\"GoodBtn\">";
				break;
				case -1:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\deadmark.png\">";
				echo "<input type=\"submit\" id=\"GoodBtn\" name=\"Resurrect\" value=\"Resurrect\" class=\"GoodBtn\">";
				break;
				case 0:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\zombiemark.png\">";
				echo "<input type=\"submit\" id=\"Good2Btn\" name=\"TurnHuman\" value=\"Turn Human\" class=\"Good2Btn\">";
				echo "<button type=\"button\" class=\"OkBtn\" onmousedown=\"FeedPlayer(this.form," . $row[0] . ")\">Feed</button>";
				echo "<input type=\"submit\" id=\"Bad2Btn\" name=\"Starve\" value=\"Starve\" class=\"Bad2Btn\">";
				break;
				case 1:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\zombiemodmark.png\">";
				echo "<input type=\"submit\" id=\"Good2Btn\" name=\"TurnHuman\" value=\"Turn Human\" class=\"Good2Btn\">";
				echo "<button type=\"button\" class=\"OkBtn\" onmousedown=\"FeedPlayer(this.form," . $row[0] . ")\">Feed</button>";
				echo "<input type=\"submit\" id=\"Bad2Btn\" name=\"Starve\" value=\"Starve\" class=\"Bad2Btn\">";
				break;
				case 2:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\mercmark.png\">";
				echo "<input type=\"submit\" id=\"GoodBtn\" name=\"TurnSurv\" value=\"Turn Survivor\" class=\"GoodBtn\">";
				echo "<input type=\"submit\" id=\"BadBtn\" name=\"Zombify\" value=\"Zombify\" class=\"BadBtn\">";
				break;
				case 3:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\survivormark.png\">";
				echo "<input type=\"submit\" id=\"GoodBtn\" name=\"TurnMerc\" value=\"Turn Merc\" class=\"GoodBtn\">";
				echo "<input type=\"submit\" id=\"BadBtn\" name=\"Zombify\" value=\"Zombify\" class=\"BadBtn\">";
				break;
				case 4:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\livemodmark.png\">";
				echo "<input type=\"submit\" id=\"BadBtn\" name=\"Zombify\" value=\"Zombify\" class=\"BadBtn\">";
				break;
				case 5:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\ozmark.png\">";
				echo "<input type=\"submit\" id=\"BadBtn\" name=\"Zombify\" value=\"Reveal\" class=\"BadBtn\">";
				break;
				case 6:
				echo "<img class=\"PlayerTeam\" src=\"Images\PlayerStates\\viableozmark.png\">";
				echo "<input type=\"submit\" id=\"GoodBtn\" name=\"TurnMerc\" value=\"Turn Merc\" class=\"GoodBtn\">";
				echo "<input type=\"submit\" id=\"BadBtn\" name=\"Zombify\" value=\"Zombify\" class=\"BadBtn\">";
				break;
				}
				
			$GetChatStateQuery = "SELECT `ChatState` FROM `hvzusers` WHERE `usrID` = " . $row[0];
			$ChatState = mysqli_fetch_row(mysqli_query($DBCon, $GetChatStateQuery))[0];
			
			if ($ChatState == 0 && ($row[1] != 1 && $row[1] != 4 && $row[1] != -2))
				{
				echo "<input type=\"submit\" id=\"ChatBanBtn\" name=\"ChatBan\" value=\"Ban Chat\" class=\"ChatBanBtn\">";
				}
			else if ($ChatState == 1 && ($row[1] != 1 && $row[1] != 4 && $row[1] != -2))
				{
				echo "<input type=\"submit\" id=\"ChatBanBtn\" name=\"ChatUnBan\" value=\"Allow Chat\" class=\"ChatBanBtn\">";
				}
				
			if ($row[1] != 1 && $row[1] != 4 && $row[1] != -2)
				{
				echo "<input type=\"submit\" id=\"KickBtn\" name=\"KickBtn\" value=\"Kick Player\" class=\"KickBtn\">";
				}
		echo "</form>";
		}
	echo "</div>";
	
	echo "<form  action=\"playerchanges.php\" name=\"InfoEditDiv\" method=\"post\" id=\"InfoEditDiv\" class=\"InfoEditDiv\">";
	echo "<p class=\"NewPlayerNameText\">New Player Name:</p>";
	echo "<input type=\"text\" id=\"EditPlayerName\" name=\"EditPlayerName\" class=\"EditPlayerName\" maxlength=\"20\"  autocomplete=\"off\" value=\"\">";
	echo "<p class=\"NewPlayerDescText\">New Player Description:</p>";
	echo "<textarea id=\"NewPlayerDesc\" name=\"NewPlayerDesc\" class=\"NewPlayerDesc\" rows=\"5\" autocomplete=\"off\"></textarea>";
	echo "<input type=\"submit\" id=\"SavePlayerInfoBtn\" name=\"SavePlayerInfoBtn\" value=\"Save\" class=\"SavePlayerInfoBtn\">";
	echo "<p class=\"RemovePlayerAvyText\">Remove Player's Avatar?<input type=\"checkbox\" value=\"DropAvyCheck\" name=\"DropAvyCheck\"></p>";
	echo "<input type=\"hidden\" id=\"UserChangingIDHidden\" name=\"UserChangingIDHidden\" value=\"\">";
	echo "</form>";
	?>
	</body>
</html>