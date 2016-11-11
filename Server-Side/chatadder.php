<?php
// Get Session Info
session_start();

// Current Time
date_default_timezone_set("America/New_York");

// Is User logged in?
if(isset($_SESSION["userName"]))
	{
	// Cool, get text, like a cool kid.
    $ChatLine = $_POST["Message"];
    // Also team of the sender.
	$Team = $_POST["Team"];
	// Also game from which the sender sent the message
	$Game = $_POST["Game"];
	
	// Depending on which team sent it, open appropriate file:
	if ($Team == "Zombies")
		{
		$fp = fopen("ChatLogs/$Game/ZombLog.html", 'a');
		}
	else if ($Team == "Humans")
		{
		$fp = fopen("ChatLogs/$Game/HumanLog.html", 'a');
		}
    fwrite($fp, "<div>(".date("H:i").") <b>".$_SESSION['userName']."</b>: ".stripslashes(htmlspecialchars($ChatLine))."<br></div>");
    fclose($fp);
	}
?>