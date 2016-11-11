<?php
session_start();
if (!isset($_SESSION["userId"]))
	{
	header("Location: index.php");
	die();
	}
	
	// Get our UserName and userId
	$MyId = $_SESSION["userId"];
	
	// Set Database Connection
	include 'dbconnector.php';
	
	// Get our info.
	$GetBasicInfoQuery = "SELECT `userid`,`userteam`,`usergame`,`usrname`,`usrdesc`,`usravy`,`usrEmail`,`usrEmailState` FROM `hvzuserstate` LEFT JOIN `hvzusrinfo` ON `userid` = `usrid` LEFT JOIN `hvzusers` ON `userid` = `hvzusers`.`usrID` WHERE `userid` = '$MyId'";
	$UserInfoResult = mysqli_query($DBCon, $GetBasicInfoQuery);
	
	// Get results.
	$UserInfo = mysqli_fetch_row($UserInfoResult);
	// Get Our Team and our Game
	$UserTeam = $UserInfo[1];
	$UserGame = $UserInfo[2];
	$UserName = $UserInfo[3];
	$UserDesc = $UserInfo[4];
	$UserAvy = $UserInfo[5];
	$UserEmail = $UserInfo[6];
	$UserEmailState = $UserInfo[7];
	
	echo "<!DOCTYPE html>";
	echo "<html>";
	echo "<head>";
		echo "<link href=\"CSS/profiledit.css\" rel=\"Stylesheet\" type=\"text/css\">";
		echo "<link href=\"CSS/general.css\" rel=\"Stylesheet\" type=\"text/css\">";
		echo "<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>";
		echo "<script src=\"JS/infoedit.js\"></script>";
	echo "</head>";
	echo "<body>";
	
	// TopBar + Log Out
	include 'pagetopper.php';
	
	if (isset($_POST['LoadError']))
		{
		echo "<div class=\"UploadError\">";
		switch ($_POST['LoadError'])
			{
			case 1:
			echo "File With Such Name<br>Already In Use";
			break;
			case 2:
			echo "File Given Is Too Large<br>Larget Than 1 Megabyte";
			break;
			case 3:
			echo "Please Only Upload Images<br>PNG, GIF, and JPG Formats Only";
			break;
			case 4:
			echo "Wrong Password Provided";
			break;
			case 5:
			echo "New Passwords Provided Do Not Match";
			break;
			}
		echo "</div>";
		}
	else if (isset($_POST['NewPass']))
		{
		echo "<div class=\"NewPass\">";
		echo "Your new Password is: " . $_POST['NewPass'];
		echo "<br>It has been sent to your e-mail.";
		echo "<br>If you wish, you may immediately change the password to one of your choosing.";
		echo "</div>";
		}
	
	echo "<form action=\"profiledit.php\" class=\"EditForm\" method=\"post\" enctype=\"multipart/form-data\">";
		echo "<p class=\"NameLbl\">Display Name:</p>";
		echo "<input type=\"text\" id=\"UserNameText\" name=\"NewName\" class=\"UserNameText\" value=\"" . $UserName .  "\" maxlength=\"20\" autocomplete=\"off\">";
		echo "<p class=\"DescLbl\">Player Description:</p>";
		echo "<textarea id=\"UserDescTextArea\" name=\"NewDesc\" class=\"UserDescTextArea\" rows=\"5\" autocomplete=\"off\">" . $UserDesc . "</textarea>";
		if ($UserAvy == "")
			{
			echo "<img class=\"AvyPrev\" id=\"AvyPrev\" src=\"Images/DefaultAvatars/Default.png\">";
			}
		else
			{
			echo "<img class=\"AvyPrev\" id=\"AvyPrev\" src=\"Images/Avatars//" . $UserAvy .  "\">";
			}
		echo "<input type=\"file\" name=\"AvyToUpload\" id=\"AvyToUpload\" class=\"AvyToUpload\">";
		echo "<p class=\"AvySelPar\">Available Avatars:</p>";
		echo "<select id=\"AvySelect\" name=\"AvySelect\" class=\"AvySelect\" onchange=\"changeAvyPrev()\">";
			echo "<option value=\"" . $UserAvy .  "\">Do Not Change</option>";
			echo "<option value=\"Remove\">Remove Avatar</option>";
			echo "<option value=\"Def1.png\">Default 1</option>";
			echo "<option value=\"Def2.png\">Default 2</option>";
			echo "<option value=\"Def3.png\">Default 3</option>";
			echo "<option value=\"Def4.png\">Default 4</option>";
		echo "</select>";
	echo "<p class=\"AvyWarnPar\" id=\"AvyWarnPar\">Warning: This will<br>remove your avatar!</p>";
	echo "<input type=\"submit\" value=\"Update Info\" name=\"UpdateBtn\" class=\"UpdateBtn\">";
	echo "</form>";
	echo "<form action=\"profiledit.php\" id=\"MovedForm\" class=\"EditForm2\" method=\"post\" enctype=\"multipart/form-data\">";
		echo "<p class=\"NewPassLbl1\">New Password:</p>";
		echo "<input type=\"text\" id=\"NewPassText1\" name=\"NewPassText1\" class=\"NewPassText1\" value=\"\" onkeyup=\"CheckRequirements()\" maxlength=\"20\" autocomplete=\"off\" placeholder=\"7+ Character\">";
		echo "<p class=\"NewPassLbl2\">Repeat New Password:</p>";
		echo "<input type=\"text\" id=\"NewPassText2\" name=\"NewPassText2\" class=\"NewPassText2\" value=\"\" onkeyup=\"CheckRequirements()\" maxlength=\"20\" autocomplete=\"off\" placeholder=\"7+ Character\">";
		echo "<p class=\"NewEmailLbl\">New E-Mail:</p>";
		echo "<input type=\"text\" id=\"NewEmailText\" name=\"NewEmailText\" class=\"NewEmailText\" value=\"\" onkeyup=\"CheckRequirements()\" maxlength=\"20\" autocomplete=\"off\" placeholder=\"" . $UserEmail . "\">";
		
		echo "<p class=\"AllowNewsMailLbl\">Allow News E-Mails:</p>";
		echo "<input type=\"checkbox\" class=\"AllowNewsEmailBox\" name=\"AllowNewsEmailBox\" ";
		if ($UserEmailState == 1 || $UserEmailState == 2)
			{
			echo " checked ";
			}
		echo "value=\"AllowNewsEmailBox\" onclick=\"MoveChangesUp()\">";
		echo "<p class=\"AllowMssnMailLbl\">Allow Mission E-Mails:</p>";
		echo "<input type=\"checkbox\" class=\"AllowMssnEmailBox\" name=\"AllowMssnEmailBox\" ";
		if ($UserEmailState == 1 || $UserEmailState == 3)
			{
			echo " checked ";
			}
		echo "value=\"AllowMssnEmailBox\" onclick=\"MoveChangesUp()\">";
		
		echo "<div class=\"PassConfDiv\">";
			echo "<p class=\"CurPassLbl\">Current Password:</p>";
			echo "<input type=\"password\" id=\"CurPassText\" name=\"CurPassText\" class=\"CurPassText\" value=\"\" maxlength=\"18\" autocomplete=\"off\">";
			echo "<input type=\"submit\" value=\"Update Info\" name=\"EmailPassUpdateBtn\" class=\"UpdateEmailPassBtn\">";
		echo "</div>";
	echo "</form>";
	echo "</body>";
	echo "</html>";
?>