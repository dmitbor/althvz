<?php
if (!isset($_POST["Delete"]) && !isset($_POST["NewNews"]) && !isset($_POST["SaveNews"]) && !isset($_POST["EmailNews"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
$date = date('Y-m-d H:i:s');
	
// Removing news from the list. Handy that.
if (isset($_POST["Delete"]))
	{
	$EntryID = $_POST["NewsIDHidden"];
	
	$RemoveEntryQuery = "DELETE FROM `hvzglobalnews` WHERE `newsId` = $EntryID";
	mysqli_query($DBCon, $RemoveEntryQuery);
	}
// Creating a new News Entry
else if (isset($_POST["NewNews"]))
	{
	$NewsTitle = mysqli_real_escape_string($DBCon, $_POST["NewsTitleText"]);
	$NewsDesc = mysqli_real_escape_string($DBCon, $_POST["NewsDescText"]);
	
	$InsertEntryQuery = "INSERT INTO `hvzglobalnews`(`newsTitle`, `newsText`) VALUES ('$NewsTitle','$NewsDesc')";
	mysqli_query($DBCon, $InsertEntryQuery);
	
	// Update everyone ever to check the news
	$GetEveryonetoCheckNewsQuery = "UPDATE `hvzuserstate` SET `checknews` = 1";
	// Run Query
	mysqli_query($DBCon, $GetEveryonetoCheckNewsQuery);
	}
// Editing an existing Entry
else if (isset($_POST["SaveNews"]))
	{
	$NewsID = mysqli_real_escape_string($DBCon, $_POST["NewsSaveIDHidden"]);
	$NewsTitle = mysqli_real_escape_string($DBCon, $_POST["NewsTitleText"]);
	$NewsDesc = mysqli_real_escape_string($DBCon, $_POST["NewsDescText"]);
	
	$EditEntryQuery = "UPDATE `hvzglobalnews` SET `newsTitle`='$NewsTitle',`newsText`='$NewsDesc'";
	if (isset($_POST["ResetNewsTimer"]))
		{
		$EditEntryQuery = $EditEntryQuery . ",`newsTime`='$date'";
		}
	$EditEntryQuery = $EditEntryQuery .	" WHERE `newsId` = $NewsID";
	mysqli_query($DBCon, $EditEntryQuery);
	
	// Update everyone ever to check the news
	$GetEveryonetoCheckNewsQuery = "UPDATE `hvzuserstate` SET `checknews` = 1";
	// Run Query
	mysqli_query($DBCon, $GetEveryonetoCheckNewsQuery);
	}
// Emailing the news to everyone registered on the site. How wonderful!
else if (isset($_POST["EmailNews"]))
	{
	// Get posting's ID
	$NewsID = $_POST["NewsSaveIDHidden"];
	
	// Set up the mailing info.
	$subject = 'KSC HVZ News Update (' . $_POST["NewsTitleText"] . ')';
	$message = $_POST["NewsDescText"] . "\r\n\r\n\r\n(Please do not reply to this automated message.)";
	$headers = 'From: survivorradio@kschvz.com' . "\r\n" .
    'Reply-To: survivorradio@kschvz.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

	// Get all the emails.
	$GetAllEmailsEverQuery = "SELECT `usrEmail` FROM `hvzusers` WHERE `usrEmailState` = 1 OR `usrEmailState` = 2";
	$EmailsResult = mysqli_query($DBCon, $GetAllEmailsEverQuery);
	
	// Go through every email, and send the message to all.
	while($EmailRow = mysqli_fetch_array($EmailsResult))
		{
		$to = $EmailRow[0];
		mail($to, $subject, $message, $headers);
		}
		
	// Update the posting's status to email sent
	$SetEmailSentQuery = "UPDATE `hvzglobalnews` SET `newsEmailSent`= 1 WHERE `newsId` = $NewsID";
	mysqli_query($DBCon, $SetEmailSentQuery);
	}
	
// Go back to the news:
header("Location: adminnews.php");
die();
?>