<!DOCTYPE html>
<html>
	<head>
		<title>Keene State: Humans VS Zombies</title>
		<link rel="shortcut icon" type="image/x-icon" href="Images/favicon.ico">
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
		<link href="CSS/index.css" rel="Stylesheet" type="text/css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="JS/index.js"></script>
	</head>
	<body>
	<?php
	// Start a session feed
	session_start();
	// If we do not have a set user id, we're probably not loged in.
	if (!isset($_SESSION["userId"]))
		{
		echo "<div class=\"TopLogo\">";
			echo "<p class=\"KeeneSourceLogo\">HVZ</p>";
			echo "<p class=\"KeeneSourceSubLogo\"><span class=\"KSCRed\">alt</span><br>Source</p>";
		echo "</div>";
		
		echo "<div class=\"OverMenu\">";
			echo "<button type=\"button\" value=\"Login\" class=\"logswitch\" id=\"ShowLogBtn\" onclick=\"ShowLog()\">Login</button>";
			echo "<button type=\"button\" value=\"Register\" class=\"regswitch\" id=\"ShowRegBtn\" onclick=\"ShowReg()\">Register</button>";
		echo "</div>";
	
		if (isset($_POST['error']))
			{
			echo"<div class=\"ErrorBox\">";
			switch ($_POST['error'])
				{
				case 1:
				echo "Provided Passwords<br>Do Not Match";
				break;
				case 2:
				echo "Given Login or Email<br>Already in Use";
				break;
				case 3:
				echo "Provided Login or Password<br>Do Not Match";
				break;
				case 4:
				echo "Check Your Email<br>For Further Instructions";
				break;
				}
			echo "</div>";
			echo "<div class=\"ErrLogMenu\" id=\"LogMenu\">";
			}
		else
			{
			echo "<div class=\"LogMenu\" id=\"LogMenu\">";
			}
			
			echo "<form action=\"logregdealer.php\" name=\"logform\" method=\"post\">";
				echo "<input type=\"text\" name=\"usrlogin\" class=\"lognamebox\" maxlength=\"18\" placeholder=\"Log In\">";
				echo "<input type=\"password\" name=\"usrpass\" class=\"logpassbox\" maxlength=\"18\" autocomplete=\"off\" placeholder=\"Password\">";
				echo "<input type=\"submit\" name=\"login\" value=\"Login\" class=\"logbtn\">";
				echo "<p class=\"frgtbtn\" onclick=\"ShowForgotPass()\">Forgot Password?</p>";
			echo "</form>";
		echo "</div>";
		
	if (isset($_POST['error']))
		{
		echo "<div class=\"ErrFrgtMenu\" id=\"FrgtMenu\">";
		}
	else
		{
		echo "<div class=\"FrgtMenu\" id=\"FrgtMenu\">";
		}		
			echo "<form action=\"logregdealer.php\" name=\"frgtform\" method=\"post\">";
				echo "<input type=\"text\" name=\"frgtemail\" class=\"frgtemailbox\" maxlength=\"36\" autocomplete=\"off\" placeholder=\"Your E-Mail\">";
				echo "<input type=\"submit\" name=\"sendpass\" value=\"Send Password\" class=\"frgtsendbtn\">";
			echo "</form>";
		echo "</div>";
	
		if (isset($_POST['error']))
			{
			echo"<div class=\"ErrRegMenu\" id=\"RegMenu\">";
			}
		else
			{
			echo"<div class=\"RegMenu\" id=\"RegMenu\">";
			}
			
		echo "<form action=\"logregdealer.php\" name=\"regform\" method=\"post\">";
			echo "<input type=\"text\" id=\"newlogtext\" name=\"newlog\" class=\"reglogbox\" maxlength=\"18\" autocomplete=\"off\" placeholder=\"Log In (7+ Characters)\" onkeyup=\"checkFilled()\">";
			echo "<input type=\"text\" id=\"newpasstext\" name=\"newpass\" class=\"regpassbox\" maxlength=\"18\" autocomplete=\"off\" placeholder=\"Password (7+ Characters)\" onkeyup=\"checkFilled()\">";
			echo "<input type=\"text\" id=\"newpassconftext\" name=\"newpasscheck\" class=\"regpasschkbox\" maxlength=\"18\" autocomplete=\"off\" placeholder=\"Confirm Password\" onkeyup=\"checkFilled()\">";
			echo "<input type=\"text\" id=\"newemailtext\" name=\"newemail\" class=\"regemailbox\" maxlength=\"36\" autocomplete=\"off\" placeholder=\"E-Mail\" onkeyup=\"checkFilled()\">";
			echo "<input type=\"submit\" id=\"RegBtn\" name=\"register\" value=\"Register\" class=\"regbtn\" disabled>";
		echo "</form>";
		echo "</div>";
		}
	else
		{
		// Set Database Connection
		$DBCon = mysqli_connect("localhost","DataHandler","IHandleAllTheData","keenehvz");
		$MyId = $_SESSION["userId"];
	
		// If we just checked out the missions, set our Check Mission state to null.
		$ResetMissionCheckCounter = "UPDATE `hvzuserstate` SET `checknews`= 0 WHERE `userid` = $MyId";
		$MissionResults = mysqli_query($DBCon, $ResetMissionCheckCounter);
		
		// TopBar + Log Out
		include 'pagetopper.php';
		}
		
	// Set connection if not previously set.
	if (!isset($DBCon))
		{
		// Set Database Connection
		$DBCon = mysqli_connect("localhost","DataHandler","IHandleAllTheData","keenehvz");
		}
		
	// Always have news.
	echo "<div class=\"InfoDiv\">";
	$GetPrimaryGame = "SELECT * FROM `hvzgame` WHERE `gameIsPrimary` = 1";
	$Results = mysqli_query($DBCon, $GetPrimaryGame);
	
	echo "<div class=\"StatDiv\">";
	if ($Results->num_rows>0)
		{
		$PrimaryGameResults = mysqli_fetch_array($Results);
		$GetPrimaryGame = "SELECT `gameId`,`gameName`,`gameIcon` FROM `hvzgame` WHERE `gameIsPrimary` = 1";
		$PrimaryGameResults = mysqli_fetch_array(mysqli_query($DBCon, $GetPrimaryGame));
		$GameID = $PrimaryGameResults[0];
		
		$GetSurvNumberQuery = "SELECT count(*) FROM `hvzuserstate` WHERE `userteam` > 1 AND `usergame` = $GameID";
		$SurvNums = mysqli_fetch_array(mysqli_query($DBCon, $GetSurvNumberQuery))[0];
		$GetZombNumberQuery = "SELECT count(*) FROM `hvzuserstate` WHERE `userteam` < 2 AND `userteam`> -1 AND `usergame` = $GameID";
		$ZombNums = mysqli_fetch_array(mysqli_query($DBCon, $GetZombNumberQuery))[0];
		$GetSurvVicQuery = "SELECT count(*) FROM `hvzgamemissions` WHERE `missionState` = 4 AND `gameId` = $GameID";
		$SurvVics = mysqli_fetch_array(mysqli_query($DBCon, $GetSurvVicQuery))[0];
		$GetZombVicQuery = "SELECT count(*) FROM `hvzgamemissions` WHERE `missionState` = 3 AND `gameId` = $GameID";
		$ZomVics = mysqli_fetch_array(mysqli_query($DBCon, $GetZombVicQuery))[0];
		
		if ($PrimaryGameResults[2] != "")
			{
			echo "<img class=\"GameIcon\" src=\"Images/GameIcons//" . $PrimaryGameResults[2] . "\">";
			}
		else
			{
			echo "<img class=\"GameIcon\" src=\"Images//GameIcons//DefGameIcon.png\">";
			}
			
		echo "<p class=\"MainGameName\">" . $PrimaryGameResults[1] . "</p>";
		
		if ($SurvNums != 0 && $ZombNums != 0 && $SurvVics != 0 && $ZomVics != 0)
			{
			// Humans are always in lesser numbers
			$PlayerPerHum = floor(($SurvNums / ($SurvNums + $ZombNums)) * 100);
			// There is always more zmobies
			$PlayerPerZom = ceil(($ZombNums / ($SurvNums + $ZombNums)) * 100);
			// Human Mission Victories, always lesser.
			$MissionsPerHum = floor(($SurvVics / ($SurvVics + $ZomVics)) * 100);
			// And in opposite, zombies.
			$MissionsPerZom = ceil(($ZomVics / ($SurvVics + $ZomVics)) * 100);
			}
		else
			{
			if ($SurvNums == 0)
				{
				$PlayerPerHum = 0;
				$PlayerPerZom = 100;
				}
			else if ($ZombNums == 0)
				{
				$PlayerPerHum = 100;
				$PlayerPerZom = 0;
				}
			else
				{
				// Humans are always in lesser numbers
				$PlayerPerHum = floor(($SurvNums / ($SurvNums + $ZombNums)) * 100);
				// There is always more zmobies
				$PlayerPerZom = ceil(($ZombNums / ($SurvNums + $ZombNums)) * 100);
				}
				
			if (($SurvVics + $ZomVics) == 0)
				{
				$MissionsPerZom = 50;
				$MissionsPerHum = 50;
				}
			else
				{
				// Human Mission Victories, always lesser.
				$MissionsPerHum = floor(($SurvVics / ($SurvVics + $ZomVics)) * 100);
				// And in opposite, zombies.
				$MissionsPerZom = ceil(($ZomVics / ($SurvVics + $ZomVics)) * 100);
				}
			}
			
		echo "<div class=\"PlayerGraph\">";
			if ($PlayerPerHum > 0)
				{
				echo "<div class=\"HumanStatDiv\" style=\"width: " . $PlayerPerHum . "%\">";
				echo "<p class=\"HumPerCountTxt\">&nbsp" . $PlayerPerHum . "% Humans</p>";
				echo "</div>";
				}
			if ($PlayerPerZom > 0)
				{
				echo "<div class=\"ZombieStatDiv\" style=\"width: " . $PlayerPerZom . "%\">";
				echo "<p class=\"ZomPerCountTxt\">Zombies " . $PlayerPerZom . "%&nbsp</p>";
				echo "</div>";
				}
			echo "<p class=\"GraphTitle\">Players Active</p>";
		echo "</div>";
		echo "<div class=\"MissionGraph\">";
			if ($MissionsPerHum > 0)
				{
				echo "<div class=\"HumanStatDiv2\" style=\"width: " . $MissionsPerHum . "%\">";
				echo "&nbsp" . $MissionsPerHum . "% Human";
				echo "</div>";
				}
			if ($MissionsPerZom > 0)
				{
				echo "<div class=\"ZombieStatDiv2\" style=\"width: " . $MissionsPerZom . "%\">";
				echo "Zombie " . $MissionsPerZom . "%&nbsp";
				echo "</div>";
				}
			echo "<p class=\"GraphTitle\">Mission Victories</p>";
		echo "</div>";
			
		}
	else
		{
		echo "<p class=\"NoGame1\">No Game is Currently in Session</p>";
		echo "<p class=\"NoGame2\">Please wait until a new game is started or join a secondary game.</p>";
		}
	echo "</div>";
	
	
	// Let's get the news going.
	$GetNews = "SELECT `newsTitle`,`newsText`,`newsTime` FROM `hvzglobalnews` ORDER BY `newsTime` DESC LIMIT 10";
	$NewsResults = mysqli_query($DBCon, $GetNews);
	
	// Herein be the news.
		echo "<div class=\"NewsDiv\">";
		// Going through the news, one by one.
		while($row = mysqli_fetch_array($NewsResults))
			{
			echo "<div class=\"NewsEntryDiv\">";
				echo "<div class=\"NewsTitle\">";
				echo $row[0];
				echo "</div>";
				echo "<div class=\"NewsDesc\">";
				echo nl2br($row[1]);
				echo "</div>";
				echo "<div class=\"NewsDate\">";
				echo date("j/m/y", strtotime($row[2]));
				echo "</div>";
			echo "</div>";
			}
		echo "</div>";
	echo "</div>";
	?>
</body>
</html>