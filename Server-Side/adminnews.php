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
		<script src="JS/adminnews.js"></script>
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	$GetNewsQuery = "SELECT `newsId`,`newsTitle`,`newsText`,`newsTime`,`newsEmailSent` FROM `hvzglobalnews` ORDER BY `newsTime` DESC";
	$NewsResults = mysqli_query($DBCon, $GetNewsQuery);
	
	echo "<div class=\"NewsDiv\">";
		echo "<div class=\"NewsListDiv\">";
		while($NewsRow = mysqli_fetch_array($NewsResults))
			{
			echo "<form class=\"NewsEntry\" action=\"newschanges.php\" name=\"newschangeform\" method=\"post\">";
			echo "<p class=\"NewsDate\">" . date("j/m/y", strtotime($NewsRow[3])) . "</p>";
			echo "<p class=\"NewsTitle\">" . $NewsRow[1] . "</p>";
			echo "<input type=\"hidden\" id=\"NewsIDHidden\" name=\"NewsIDHidden\" value=\"" . $NewsRow[0] . "\">";
			echo "<input type=\"hidden\" id=\"NewsNameHidden\" name=\"NewsNameHidden\" value=\"" . $NewsRow[1] . "\">";
			echo "<input type=\"hidden\" id=\"NewsDescHidden\" name=\"NewsDescHidden\" value=\"" . $NewsRow[2] . "\">";
			if ($NewsRow[4] == 1)
				{
				echo "<img class=\"EmailSent\" src=\"Images/mail.png\">";
				}
			echo "<button type=\"button\" class=\"GetNewsBtn\" onmousedown=\"GetNews(this.form)\">Edit</button>";
			echo "<input type=\"submit\" id=\"Delete\" name=\"Delete\" value=\"Delete\" class=\"DeleteNewsBtn\">";
			echo "</form>";
			}
		echo "</div>";
		echo "<form class=\"EditNewsDiv\" action=\"newschanges.php\" name=\"newschangeform\" method=\"post\">";
			echo "<p class=\"NewsTitleTxt\">News Title:</p>";
			echo "<input type=\"text\" id=\"NewsTitleText\" name=\"NewsTitleText\" class=\"NewsTitleText\" value=\"\" maxlength=\"30\" autocomplete=\"off\">";
			echo "<p class=\"NewsDescTxt\">News Text:</p>";
			echo "<textarea id=\"NewsDescText\" name=\"NewsDescText\" class=\"NewsDescText\" rows=\"5\" autocomplete=\"off\"></textarea>";
			echo "<p class=\"ResetNewsTimer\"><input type=\"checkbox\" name=\"ResetNewsTimer\" value=\"ResetNewsTimer\">Reset the Posting Date</p>";
			echo "<input type=\"hidden\" id=\"NewsSaveIDHidden\" name=\"NewsSaveIDHidden\" value=\"\">";
			echo "<input type=\"submit\" id=\"EmailNews\" name=\"EmailNews\" value=\"Send as Email\" class=\"EmailNewsBtn\">";
			echo "<input type=\"submit\" id=\"NewNews\" name=\"NewNews\" value=\"New Entry\" class=\"NewNewsBtn\">";
			echo "<input type=\"submit\" id=\"SaveNews\" name=\"SaveNews\" value=\"Save Entry\" class=\"SaveNewsBtn\">";
		echo "</div>";
	echo "</div>";
	?>
	</body>
</html>