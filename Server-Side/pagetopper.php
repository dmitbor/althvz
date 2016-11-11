<?php
	// Get User's Name and ID
	$MyId = $_SESSION["userId"];
	
	// Set Database Connection
	include 'dbconnector.php';
	
	// Get OUR info.
	$GetBasicInfoQuery = "SELECT `userteam`,`userlastfed`,`usergame`,`checknews`,`checkmissions` FROM `hvzuserstate` WHERE `userid` = '$MyId'";
	$UserInfoResult = mysqli_query($DBCon, $GetBasicInfoQuery);
	
	// Get results.
	$UserInfo = mysqli_fetch_row($UserInfoResult);
	// Get Our Team and our Game
	$UserTeam = $UserInfo[0];
	$UserGame = $UserInfo[2];
	$UserNewsCheck = $UserInfo[3];
	$UserMissionCheck = $UserInfo[4];

	if (isset($ChatLoad))
		{
		if ($ChatLoad == 1)
			{
			// Load Human or Zombie chat based on user's team.
			// Admins load both of them.
			if ($UserTeam < 2)
				{
				echo "<body onload=\"LoadChat('Zombies',$UserGame);setInterval(function(){LoadChat('Zombies',$UserGame)},2500);\">";
				}
			else if ($UserTeam > 1)
				{
				echo "<body onload=\"LoadChat('Humans',$UserGame);setInterval(function(){LoadChat('Humans',$UserGame)},2500);\">";
				}
			}
		}
	
	// Page topper small logo.
	echo "<div class=\"MiniLogo\"><p class=\"MnLogoTxt\">HVZ <span class=\"MnLogoTxtSmall\">Source</span></p>";
	// Zombies are always shown that they hunger and their dying counter
	if ($UserTeam < 2 && $UserTeam > -1)
		{
		echo "<p class=\"StarveWhen\">";
		$DateOne = strtotime("now");
		$DateTwo = strtotime($UserInfo[1] . "+2 days");
		
		if ($DateOne < $DateTwo)
			{
			$TimeDifference = abs($DateTwo - $DateOne);
			
			$DaysLeft = floor($TimeDifference / (60 * 60 * 24));
			$HoursLeft = floor(($TimeDifference - ($DaysLeft * 60 * 60 * 24)) / (60 * 60));
			$MinutesLeft = floor(($TimeDifference - ($DaysLeft * 60 * 60 * 24) - ($HoursLeft * 60 * 60 )) / 60);
			
			echo "Time Until Starvation: ";
			
			// If a day is left:
			if ($DaysLeft > 0)
				{
				echo $DaysLeft . " day";
				if ($DaysLeft > 1)
					{
					echo "s";
					}
				}
			if ($HoursLeft > 0)
				{
				echo " " . $HoursLeft . " hours " ;
				}
			else
				{
				echo " and ";
				}
			echo $MinutesLeft . " minutes";
			}
		else
			{
			echo "You Have Starved";
			}
		echo "</p>";
		}
	// Always tell us when we're dead!
	else if ($UserTeam < 0)
		{
		echo "<p class=\"StarveWhen\">You Have Starved</p>";
		}
	echo "</div>";
		
	// Top Menu Bar
	echo "<ul id=\"TopBar\">";
		if ($UserGame > 0)
			{
			echo "<li><a ";
			if ($UserNewsCheck == 1 || $UserMissionCheck == 1)
						{
						echo "style=\"color: #E87511;\"";
						}
			echo " href=\"gamestats.php\">&#709; Game Stats</a>";
				echo "<ul>";
					echo "<li><a ";
					if ($UserNewsCheck == 1)
						{
						echo "style=\"color: #E87511;\"";
						}
					echo " href=\"index.php\">> News</a></li>";
					echo "<li><a href=\"story.php\">> Story So Far...</a></li>";
					echo "<li><a ";
					if ($UserMissionCheck == 1)
						{
						echo "style=\"color: #E87511;\"";
						}
					echo " href=\"missionlist.php\">> Missions</a></li>";
					if ($UserTeam > 1)
						{
						echo "<li><a href=\"arsenal.php\">> Arsenal/Rentals</a></li>";
						}
					else if($UserTeam < 2)
						{
						echo "<li><a href=\"arsenal.php\">> Rentals Return</a></li>";
						}
				echo "</ul>";
			echo "</li>";
			}
		else if ($UserGame == 0) 
			{
			echo "<li><a ";
			if ($UserNewsCheck == 1)
				{
				echo "style=\"color: #E87511;\"";
				}
			echo" href=\"index.php\">News</a></li>";
			echo "<li><a href=\"joingame.php\">Join a Game</a></li>";
			}
		echo "<li>";
			echo "<a href=\"playerstats.php\">&#709; My Profile</a>";
			echo "<ul>";
				echo "<li><a href=\"editplayerstats.php\">> Edit Profile</a></li>";
			echo "</ul>";
		echo "</li>";
		if ($UserTeam > 1 && $UserTeam != 5)
			{
			if ($UserGame != 0) 
				{
				echo "<li><a href=\"tagpage.php\">My Code</a></li>";
				}
			echo "<li><a href=\"groups.php\">Squads</a></li>";
			}
		else if($UserTeam < 2 || $UserTeam == 5)
			{
			if ($UserGame != 0 && $UserTeam != -1) 
				{
				echo "<li><a href=\"tagpage.php\">Report Tag</a></li>";
				}
			echo "<li><a href=\"groups.php\">Hordes</a></li>";
			}
		echo "<li><a href=\"rules.php\">Rules/FAQ</a></li>";
		if ($UserTeam == 1 || $UserTeam == 4 || $UserTeam == -2)
			{
			echo "<li><a href=\"\">&#709; Admin Options</a>";
				echo "<ul>";
					echo "<li><a href=\"admingame.php\">> Game Managment</a></li>";
					if ($UserGame != 0)
						{
						echo "<li><a href=\"adminmissions.php\">> Mission Managment</a></li>";
						echo "<li><a href=\"adminplayers.php\">> Player Managment</a></li>";
						echo "<li><a href=\"adminstory.php\">> Story Managment</a></li>";
						}
					echo "<li><a href=\"adminarsenal.php\">> Arsenal Managment</a></li>";
					echo "<li><a href=\"adminnews.php\">> News Managment</a></li>";
					echo "<li><a href=\"admincomunity.php\">> Chat/Group Managment</a></li>";
				echo "</ul>";
			echo "</li>";
			}
	echo "</ul>";
	
	// Log Out Button
	echo "<form action=\"logregdealer.php\" name=\"logoutform\" method=\"post\">";
	echo "<input type=\"submit\" name=\"logout\" value=\"Log Out\" class=\"LogOffBtn\">";
	echo "</form>";
?>