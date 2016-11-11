<?php
// This allows us to use the Hash functions that is otherwise not available from GoDaddy, because they are rude bloody people like that.
require "Hasher/lib/password.php";

// Someone is trying to register:
if (isset($_POST['register']))
	{
	// Connect to the database.
	include 'dbconnector.php';
	$date = date('Y-m-d H:i:s');
	
	// Get the info.
	$NewLogin = mysqli_real_escape_string($DBCon, $_POST['newlog']);
	$NewPass = mysqli_real_escape_string($DBCon, $_POST['newpass']);
	$NewPassConf = mysqli_real_escape_string($DBCon, $_POST['newpasscheck']);
	$NewEmail = mysqli_real_escape_string($DBCon, $_POST['newemail']);
	
	// Are pass confirmations the same? If not, throw them at the main page.
	if ($NewPass != $NewPassConf)
		{
		echo "<html>";
			echo "<body onload=\"document.frm1.submit()\">";
				echo "<form action=\"index.php\" method=\"post\" name=\"frm1\">";
					echo "<input type=\"hidden\" name=\"error\" value=\"1\"/>";
				echo "</form>";
			echo "</body>";
		echo "</html>";
		die();
		}
	// They pass basic bot test. Good job!
	else
		{
		// Check if the Login/Email already used:
		$UsedQuery = "SELECT * FROM `hvzusers` WHERE `usrLogin` like '$NewLogin' OR `usrEmail` like '$NewEmail'";
		
		$UsedResult = mysqli_query($DBCon, $UsedQuery);
		
		// No one of same name found:
		if ($UsedResult->num_rows==0)
			{
			// Create a new random salt for them.
			$Salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
			
			// Set the new salt and the cost.
			$SaltingOptions =
			[
			'salt' => $Salt,
			'cost' => 10,
			];
			
			// Create the new Hash.
			$Hashy = password_hash($NewPass, PASSWORD_BCRYPT, $SaltingOptions);
			
			// Insert user info into the database.
			$NewUserQuery = "INSERT INTO `keenehvz`.`hvzusers` (`usrID` ,`usrLogin` ,`usrSaltyPass` ,`Salt` ,`usrEmail`)VALUES (NULL , '$NewLogin', '$Hashy', '$Salt', '$NewEmail');";
			mysqli_query($DBCon, $NewUserQuery);
			
			// Get UserID for other tables.
			$GetNewID = "SELECT `usrID` FROM `hvzusers` WHERE `usrLogin` like '$NewLogin' and `usrEmail` like '$NewEmail'";
			$NewIDResult = mysqli_query($DBCon, $GetNewID);
			$NewUserID =  mysqli_fetch_array($NewIDResult)[0];
			
			// Insert generic used info into the database.
			$NewUserInfoQuery = "INSERT INTO `keenehvz`.`hvzusrinfo` (`usrid` ,`usrname` ,`usrdesc` ,`usravy` ) VALUES ($NewUserID, '$NewLogin', NULL , NULL);";
			mysqli_query($DBCon, $NewUserInfoQuery);

			// Set basic user preferences.
			$NewUserBaseQuery = "INSERT INTO `keenehvz`.`hvzuserstate` (`userid`, `userteam`, `usergame`) VALUES ($NewUserID, $UserClass, 0);";
			mysqli_query($DBCon, $NewUserBaseQuery);
			
			// Create a new small event for the user's registration.
			$NewUserRegistratonQuery = "INSERT INTO `keenehvz`.`hvzsmallevents` (`evntId` ,`evntType` ,`evtDate` ,`usrSubjctId` ,`relevantId`) VALUES (NULL , 0, '$date' , $NewUserID, NULL);";
			mysqli_query($DBCon, $NewUserRegistratonQuery);
			
			// Close the connection.
			mysqli_close($DBCon);
			
			// Setting session data.
			session_start();
			session_unset();
			
			// Set user ID.
			$_SESSION["userId"] = $NewUserID;
			$_SESSION["userName"] = $NewLogin;
			
			// Go to Profile page to define it.
			header("Location: editplayerstats.php");
			die();
			}
		// Someone is using the email or name, panic and run away:
		else
			{
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"index.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"error\" value=\"2\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		}
	}
// Someone is trying to log in. Good on them:
else if (isset($_POST['login']))
	{
	// Get connection up, pronto.
	$DBCon = mysqli_connect("localhost","DataHandler","IHandleAllTheData","keenehvz");

	// Equally quickly, escape the strings.
	$Login = mysqli_real_escape_string($DBCon, $_POST['usrlogin']);
	$Pass = mysqli_real_escape_string($DBCon, $_POST['usrpass']);
	
	// Get the Salt from account with the same name.
	$SaltQuery = "SELECT `Salt` FROM `hvzusers` WHERE `usrLogin` like '$Login'";
	$Salt =  mysqli_fetch_array(mysqli_query($DBCon, $SaltQuery))[0];
	
	// If we found the account, go on.
	if ($Salt != "")
		{
		// Set Salt
		$SaltingOptions =
			[
			'salt' => $Salt,
			'cost' => 10,
			];
			
		// Get Hash.
		$CheckHash = password_hash($Pass, PASSWORD_BCRYPT, $SaltingOptions);
		
		// Run a query to see if the Hash is matching.
		$CheckQuery = "SELECT `hvzusers`.`usrID`, `hvzusrinfo`.`usrname` FROM `hvzusers` INNER JOIN `hvzusrinfo` ON `hvzusers`.`usrID`=`hvzusrinfo`.`usrID` WHERE `usrSaltyPass` like '$CheckHash' AND `usrLogin` like '$Login'";
		$CheckResult = mysqli_query($DBCon, $CheckQuery);

		// Results found. Great. You're real.
		if ($CheckResult->num_rows>0)
			{
			$UserInfo = mysqli_fetch_row($CheckResult);
			
			$UserID = $UserInfo[0];
			$UserName = $UserInfo[1];
			
			// Setting session data.
			session_start();
			session_unset();
			// Set user ID.
			$_SESSION["userId"] = $UserID;
			$_SESSION["userName"] = $UserName;
			
			// Close the connection.
			mysqli_close($DBCon);
			// Go to stat page.
			header("Location: gamestats.php");
			die();
			}
		// Lying criminal scum. Back to main page with you.
		else
			{
			// Close the connection.
			mysqli_close($DBCon);
			// Run away!
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"index.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"error\" value=\"3\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		}
	else
		{
		echo "<html>";
			echo "<body onload=\"document.frm1.submit()\">";
				echo "<form action=\"index.php\" method=\"post\" name=\"frm1\">";
					echo "<input type=\"hidden\" name=\"error\" value=\"3\"/>";
				echo "</form>";
			echo "</body>";
		echo "</html>";
		die();
		}
	}
// User trying to log out.
else if (isset($_POST['logout']))
	{
	// Kill the session.
	session_start();
	session_unset();
	session_destroy();
	// Throw us back to the login page.
	header("Location: index.php");
	die();
	}
else if (isset($_POST['sendpass']))
	{
	// Connect to the database.
	$DBCon = mysqli_connect("localhost","DataHandler","IHandleAllTheData","keenehvz");
	// Get the provided email.
	$ForgetfulEmail = mysqli_real_escape_string($DBCon, $_POST['frgtemail']);
	
	// Get the User ID from account with the given email.
	$ForgottenEmailQuery = "SELECT `usrID` FROM `hvzusers` WHERE `usrEmail` like '$ForgetfulEmail'";
	$ForgottenResults = mysqli_query($DBCon, $ForgottenEmailQuery);
	$ForgottenID = mysqli_fetch_array($ForgottenResults)[0];
	
	// If such user found.
	if ($ForgottenID != "")
		{
		date_default_timezone_set("America/New_York");
		$CurDate = date('Y-m-d H:i:s');
		$RandConfirm = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
		$SetUserForgettenQuery = "UPDATE `hvzusers` SET `usrForgotPass`=1,`usrForgotSetDate`='$CurDate',`usrForgotConfirm`='$RandConfirm' WHERE `usrID` = $ForgottenID";
		mysqli_query($DBCon, $SetUserForgettenQuery);
		
		// Send Email with proper link
		// Set up the email
		$to = $ForgetfulEmail;
		$subject = 'KSC HVZ - Password Recovery Confirmation';
		$message = "It appears that you have requested a new password for your account.\r\n\r\nIf you wish to continue, please access the following link: " . $_SERVER['HTTP_HOST'] . "/forgotpass.php?USRID=$ForgottenID&USREMAIL=$ForgetfulEmail&CONF=$RandConfirm \r\n\r\nIf you have not requested the change in password, simply do not click the link, and simply ignore this email.\r\n\r\n\r\n(Please do not reply to this automated message.)";
		$headers = 'From: survivorradio@kschvz.com' . "\r\n" .
		'Reply-To: survivorradio@kschvz.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		// Send the email
		mail($to, $subject, $message, $headers);
		}
	// Use the Error output for the information
	echo "<html>";
		echo "<body onload=\"document.frm1.submit()\">";
			echo "<form action=\"index.php\" method=\"post\" name=\"frm1\">";
				echo "<input type=\"hidden\" name=\"error\" value=\"4\"/>";
			echo "</form>";
		echo "</body>";
	echo "</html>";
	die();
	}
// How did you get here? Get out!
else
	{
	header("Location: index.php");
	die();
	}
?>