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

$CheckIfAdminQuery = "SELECT `userteam` FROM `hvzuserstate` WHERE `userid`=$ViewerID";
$ViewerTeam = mysqli_fetch_array(mysqli_query($DBCon, $CheckIfAdminQuery))[0];

if ($ViewerTeam != 1 && $ViewerTeam != 4 && $ViewerTeam != -2)
	{
	header("Location: playerstats.php");
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
		<script src="JS/gameedit.js"></script>
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// If we are in a game:
	if ($UserGame != 0)
		{
		echo "<form action=\"gamechanges.php\" name=\"GameMNGR\" class=\"GameMNGR\" method=\"post\" enctype=\"multipart/form-data\">";
		$GetGameInfoQuery = "SELECT `gameId`,`gameName`,`gameAcsCode`,`gameState`,`gameIcon` FROM `hvzgame` WHERE `gameId` = $UserGame";
		$GameInfo = mysqli_fetch_row(mysqli_query($DBCon, $GetGameInfoQuery));
		$GameState = $GameInfo[3];
		$GameID = $GameInfo[0];
		
		$IconDir = 'Images/GameIcons';
		$IconFiles = scandir($IconDir);
		$IconList = "<option value=\"" . $GameInfo[4] . "\">Current</option>";
	
		for ($Counter = 2; $Counter < count($IconFiles);$Counter++)
			{
			$IconList = $IconList . "<option value=\"" . $IconFiles[$Counter] . "\">" . $IconFiles[$Counter] . "</option>";
			}
		
		echo "<p class=\"IconDesc\">Game Icon:</p>";
		echo "<select class=\"IconList\" id=\"IconList\" name=\"IconList\" onchange=\"changeIconPrev()\">";
		echo $IconList;
		echo "</select>";
		echo "<img src=\"Images/GameIcons//" . $GameInfo[4] . "\" id=\"GameIcon\" class=\"GameIcon\">";
		echo "<input type=\"file\" name=\"GameIconToUpload\" id=\"GameIconToUpload\" class=\"GameIconToUpload\">";
		echo "<p class=\"GameIconResetBox\">Reset Icon: <input type=\"checkbox\" name=\"resetIcon\" value=\"resetIcon\"></p>";
		echo "<p class=\"NameDesc\">Game Name:</p>";
		echo "<input type=\"text\" name=\"GameName\" class=\"GameName\" maxlength=\"20\" value=\"" . $GameInfo[1] . "\">";
		echo "<p class=\"CodeDesc\">Game Access Code:</p>";
		echo "<p class=\"GameCode\">" . $GameInfo[2] . "</p>";
		echo "<input type=\"submit\" id=\"saveinfo\" name=\"saveinfo\" value=\"Save\" class=\"saveBtn\">";
		// Game is not yet started. We can start it.
		if ($GameState == 0)
			{
			echo "<input type=\"submit\" id=\"StrtBtn\" name=\"StrtBtn\" value=\"Start Game\" class=\"StrtBtn\">";
			}
		// Game Paused. We can continue.
		else if ($GameState == 1)
			{
			echo "<input type=\"submit\" id=\"StrtBtn\" name=\"StrtBtn\" value=\"Continue\" class=\"StrtBtn\">";
			}
		// Game Started. We can pause it
		else if ($GameState == 2)
			{
			echo "<input type=\"submit\" id=\"StrtBtn\" name=\"StrtBtn\" value=\"Pause Game\" class=\"StrtBtn\">";
			}
		echo "<p class=\"InfoDesc\">Game Status:</p>";
		echo "<div class=\"GameInfoDiv\">";
		$GetZumbas = mysqli_fetch_row(mysqli_query($DBCon, "SELECT count(*) FROM `hvzuserstate` where `userteam` < 2 AND `userteam` > -1 AND `usergame` = $GameID"));
		$GetHumies = mysqli_fetch_row(mysqli_query($DBCon,"SELECT count(*) FROM `hvzuserstate` where `userteam` > 2 AND `userteam` < 5 AND `usergame` = $GameID"));
		$GetMercs = mysqli_fetch_row(mysqli_query($DBCon,"SELECT count(*) FROM `hvzuserstate` where `userteam` = 2 AND `usergame` = $GameID"));
		$GetDedZoombas = mysqli_fetch_row(mysqli_query($DBCon, "SELECT count(*) FROM `hvzuserstate` where `userteam` = -1 AND `usergame` = $GameID"));
		$CheckForOZs = mysqli_fetch_row(mysqli_query($DBCon,"SELECT count(*) FROM `hvzuserstate` WHERE `userteam` = 5 AND `usergame` = $GameID"))[0];
		echo "<p class=\"Umies\">Pure Humans Left: " . $GetHumies[0] . "</p>";
		echo "<p class=\"Mercs\">Mercenaries Left: " . $GetMercs[0] . "</p>";
		echo "<p class=\"Zumbas\">Active Zombies Left: " . $GetZumbas[0] . "</p>";
		echo "<p class=\"Ded\">Starved Zombies: " . $GetDedZoombas[0] . "</p>";
		// OZ is still out for blood.
		if ($CheckForOZs > 0)
			{
			echo "<p class=\"OZStatus\">OZs Operating: " . $CheckForOZs . "</p>";
			echo "<input type=\"submit\" id=\"RevealOZBtn\" name=\"RevealOZBtn\" value=\"Reveal OZs\" class=\"RevealOZBtn\">";
			}
		echo "<button type=\"button\" class=\"EndButton\" onmousedown=\"areYouSure()\">End Game</button>";
		echo "<input type=\"hidden\" name=\"gameID\" value=\"$UserGame\"/>";
		echo "<input type=\"hidden\" id=\"delConfirm\" name=\"delConfirm\">";
		echo "<input type=\"hidden\" id=\"DefaultIcon\" name=\"DefaultIcon\" value=\"" . $GameInfo[4] . "\">";
		echo "</div>";
		echo "</form>";
		
		if ($GameState == 0)
			{
			$GetOZsandCandidates = "SELECT `userid`,`userteam`,`usrname`,`usravy` FROM `hvzuserstate` LEFT JOIN `hvzusrinfo` ON `userid`=`usrid` WHERE `userteam` > 4 AND `usergame` = $GameID";
			$OZandCand = mysqli_query($DBCon, $GetOZsandCandidates);
			if ($OZandCand->num_rows>0)
				{
				echo "<div class=\"OZSelectionDiv\">";
					echo "<div class=\"OZsDiv\">";
					while($CandRow = mysqli_fetch_array($OZandCand))
						{
						echo "<form action=\"gamechanges.php\" name=\"OZMngmt\" method=\"post\"  class=\"OZMgmntForm\"";
						if ($CandRow[1] == 6)
							{
							echo "style=\"background-color: #E87511;\">";
							echo "<input type=\"submit\" id=\"OZMngmtBtn\" name=\"TurnOzMgmt\" value=\"Turn OZ\" class=\"OZMngmtBtn\">";
							}
						else if ($CandRow[1] == 5)
							{
							echo "style=\"background-color: #AF1E2D;\">";
							echo "<input type=\"submit\" id=\"OZMngmtBtn\" name=\"UnOzMgmt\" value=\"Un-OZ\" class=\"OZMngmtBtn\">";
							}
						
						// If they have an avatar.
						if ($CandRow[3] == "")
							{
							echo "<img class=\"OZAvy\" src=\"Images/DefaultAvatars/DefaultHumanAv.png\">";
							}
						// Otherwise use OZ Icon
						else
							{
							echo "<img class=\"OZAvy\" src=\"Images/Avatars//" . $CandRow[3] .  "\">";
							}
						echo "<input type=\"hidden\" name=\"OZID\" value=\"" . $CandRow[0] . "\"/>";
						echo "<p class=\"OZsPlayerName\">" . $CandRow[2] . "</p>";
						echo "</form>";
						}
					echo "</div>";
				echo "</div>";
				}
			}
		}
	// We don't have a game. Create a new one.
	else if ($UserGame == 0)
		{
		echo "<form action=\"gamechanges.php\" name=\"GameMNGR\" class=\"GameMNGR2\" method=\"post\" enctype=\"multipart/form-data\">";
		echo "<p class=\"NewNameDesc\">Name of the New Game:</p>";
		echo "<input type=\"text\" name=\"NewGameName\" class=\"NewGameName\" maxlength=\"20\"  autocomplete=\"off\" value=\"\">";
		echo "<p class=\"NewIconDesc\">Custom Icon:</p>";
		echo "<input type=\"file\" name=\"NewIconToUpload\" id=\"NewIconToUpload\" class=\"NewIconToUpload\">";
		echo "<p class=\"NewIconWarn\">If no custom icon is chosen,<br>then default Keene Owl is used,<br>instead.</p>";
		echo "<p class=\"NewPrimaryDesc\">Primary Game:</p>";
		echo "<input type=\"checkbox\" class=\"NewPrimaryCheck\" value=\"NewPrimaryCheck\" name=\"NewPrimaryCheck\">";
		echo "<p class=\"NewPrimaryWarn\">Primary Game status is shown on the front page.<br>Only one Primary Game can be run at a time.<br>If a Primary Game is already happening, you may not create a new Primary Game.</p>";
		echo "<input type=\"submit\" id=\"NewGameBtn\" name=\"NewGameBtn\" value=\"Create Game\" class=\"NewGameBtn\">";
		}
	echo "</form>";
	// We have an error. Inform the user.
	if (isset($_POST["LoadError"]))
		{
		echo "<div class=\"ErrorDiv\">";
		switch ($_POST["LoadError"])
			{
			case 1:
			echo "A file with such name is already in use<br>Please change the file's name.";
			break;
			case 2:
			echo "The File given is too large.<br>Please select a file of 1 MB or less in size.";
			break;
			case 3:
			echo "The file given is not an image.<br>Please select an image file (GIF/JPG/PNG).";
			break;
			case 4:
			echo "A Primary Game is already in session.<br>End it, wait until it is over, or start a Non-Primary Game.";
			break;
			}
		echo "</div>";
		}
	?>
	</body>
</html>