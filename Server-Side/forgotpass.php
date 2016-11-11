<?php
// This allows us to use the Hash functions that is otherwise not available from GoDaddy, because they are rude bloody people like that.
require "Hasher/lib/password.php";

$UserID = $_GET["USRID"];
$UserEmail = $_GET["USREMAIL"];
$Confirmation = $_GET["CONF"];

// If not all of the settings are set, go to front page.
if ($UserID == null || $UserEmail == null || $Confirmation == null)
	{
	header("Location: index.php");
	die();
	}
	
// Array to Generate new Password String
$CharAray = array(
	1 => "1",
	2 => "2",
	3 => "3",
	4 => "4",
	5 => "5",
	6 => "6",
	7 => "7",
	8 => "8",
	9 => "9",
	10 => "0",
	11 => "A",
	12 => "B",
	13 => "C",
	14 => "D",
	15 => "E",
	16 => "F",
	17 => "H",
	18 => "I",
	19 => "J",
	20 => "K",
	21 => "L",
	22 => "M",
	23 => "N",
	24 => "P",
	25 => "R",
	26 => "T",
	27 => "U",
	28 => "V",
	29 => "W",
	30 => "X",
	31 => "Y",
);
// Generate a new 10-character pass.
$NewPass = $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)] . $CharAray[rand (1, 31)];

// Salt the newly created password!
$Salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
			
// Set the new salt and the cost.
$SaltingOptions =
[
'salt' => $Salt,
'cost' => 10,
];
			
// Create the new Hash.
$HashedPass = password_hash($NewPass, PASSWORD_BCRYPT, $SaltingOptions);

// Now actually connect to the database!
// Set Database Connection
include 'dbconnector.php';

// Set the new Salt AND Hashed Password for the user
$SetNewPassQuery = "UPDATE `hvzusers` SET `usrSaltyPass`='$HashedPass',`Salt`='$Salt',`usrForgotPass`=0,`usrForgotSetDate`=null,`usrForgotConfirm`=null WHERE `usrID` = $UserID";

// Run the password update Query
mysqli_query($DBCon, $SetNewPassQuery);

// Send Email to the user with the new Password.
// Set up the mailing info.
$to = $UserEmail;
$subject = 'KSC HVZ - Your New Password';
$message = "It appears that you have requested a new password for your account, and have accessed the link sent to you for confirmation.\r\nIn addition, we are providing you with a copy of your new password to your provided email.\r\n\r\nYour new Password: " . $NewPass . "\r\n\r\n\r\n(Please do not reply to this automated message.)";
$headers = 'From: survivorradio@kschvz.com' . "\r\n" .
'Reply-To: survivorradio@kschvz.com' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);

// Now that we set a new password, log the user in.
// Get User Login
$GetLoginQuery = "SELECT `usrLogin` FROM `hvzusers` WHERE `usrID`=$UserID";
$UsrLogin = mysqli_fetch_array(mysqli_query($DBCon, $GetLoginQuery))[0];

// Close the connection.
mysqli_close($DBCon);
			
// Setting session data.
session_start();
session_unset();
			
// Set user ID.
$_SESSION["userId"] = $UserID;
$_SESSION["userName"] = $UsrLogin;

// Go to Profile page, in case they want to set up a new password.
echo "<html>";
	echo "<body onload=\"document.frm1.submit()\">";
		echo "<form action=\"editplayerstats.php\" method=\"post\" name=\"frm1\">";
		echo "<input type=\"hidden\" name=\"NewPass\" value=\"$NewPass\"/>";
		echo "</form>";
	echo "</body>";
echo "</html>";
die();
?>