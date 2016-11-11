<?php
	// Cool, get text, like a cool kid.
    $NewChat = $_POST["Message"];
    // Also team of the sender.
	$Team = $_POST["Team"];
	// Also game from which the sender sent the message
	$Game = $_POST["Game"];
	
	// Depending on which team sent it, open appropriate file:
	if ($Team == "Zombies")
		{
		$fp = fopen("ChatLogs/$Game/ZombLog.html", 'w');
		}
	else if ($Team == "Humans")
		{
		$fp = fopen("ChatLogs/$Game/HumanLog.html", 'w');
		}
		
	fwrite($fp, $NewChat);
	fclose($fp);
?>