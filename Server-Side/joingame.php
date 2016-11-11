<?php
// Start a session feed
session_start();
// If we do not have a set user id, we're probably not loged in. Kick us out to Login page.
if (!isset($_SESSION["userId"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';

$ViewerID = $_SESSION["userId"];

$CheckIfAdminQuery = "SELECT `usergame` FROM `hvzuserstate` WHERE `userid`=$ViewerID";
$ViewerGame = mysqli_fetch_array(mysqli_query($DBCon, $CheckIfAdminQuery))[0];

if ($ViewerGame != 0)
	{
	header("Location: gamestats.php");
	die();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Keene State: Humans VS Zombies</title>
		<link rel="shortcut icon" type="image/x-icon" href="Images/favicon.ico">
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
		<link href="CSS/joingame.css" rel="Stylesheet" type="text/css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	</head>
	<body>
<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// Get list of open games.
	$FindOpenGamesQuery = "SELECT `gameId`,`gameName`,`gameIcon` FROM `hvzgame` WHERE `gameState` = 0";
	$OpenGamesResults = mysqli_query($DBCon, $FindOpenGamesQuery);
	
	echo "<div class=\"GamesDiv\">";
	// If there are currently any games to join:
	if ($OpenGamesResults->num_rows>0)
		{
		// Go through results.
		while($Game = mysqli_fetch_array($OpenGamesResults))
			{
			echo "<div class=\"GameSubDiv\">";
			if ($Game[2] == "")
				{
				echo "<img class=\"GameIcon\" src=\"Images/GameIcons/DefGameIcon.png\">";
				}
			else
				{
				echo "<img class=\"GameIcon\" src=\"Images/GameIcons//" . $Game[2] . "\">";
				}
			echo "<p class=\"GameName\">" . $Game[1] . "</p>";
			echo "<form action=\"gameenter.php\" name=\"joinform\" method=\"post\">";	
			echo "<input type=\"hidden\" name=\"gameID\" value=\"" . $Game[0] . "\">";
			echo "<input type=\"submit\" name=\"joinbtn\" value=\"Join\" class=\"GameJoinBtn\">";
			echo "</form>";
			echo "<div class=\"GameBorder\"></div>";
			echo "</div>";
			}
		}
	else
		{
		echo "<p class=\"NoGames\">No Games are Currently Available to Join<br>Please Try Again, Later.</p>";
		}
	echo "</div>";
	?>
	</body>
</html>