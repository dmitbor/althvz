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
		<script src="JS/adminarsenal.js"></script>
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	echo "<div class=\"ArsenalAdminTable\">";
	$GetAllWeaponData = "SELECT `wpnid`,`wpnname`,`wpnpic`,`wpnnum`,`wpncost` FROM `hvzarsenalitems`";
	$WeaponData = mysqli_query($DBCon, $GetAllWeaponData);
	
	// Get all currently uploaded gun pics
	$PicDir = 'Images/ArsenalPics';
	$PicFiles = scandir($PicDir);
	$PicList = "<option value=\"\">Current</option>";
	$JustImages = "<option value=\"\">N/A</option>";
	
	for ($Counter = 2; $Counter < count($PicFiles);$Counter++)
		{
		$PicList = $PicList . "<option value=\"" . $PicFiles[$Counter] . "\">" . $PicFiles[$Counter] . "</option>";
		if ($PicFiles[$Counter] != "defaultpic.png")
			{
			$JustImages = $JustImages . "<option value=\"" . $PicFiles[$Counter] . "\">" . $PicFiles[$Counter] . "</option>";
			}
		}
	
	while($WeaponRow = mysqli_fetch_array($WeaponData))
		{
		$WeaponID = $WeaponRow[0];
		echo "<form class=\"ArsenalTableEntry\" action=\"arsenalhandler.php\" method=\"post\">";
		echo "<input type=\"hidden\" id=\"WpnID\" name=\"WpnID\" value=\"" . $WeaponRow[0] . "\">";
		if ($WeaponRow[2] == "")
			{
			echo "<img class=\"BlasterIMG\" name=\"WPNPic\" src=\"Images/ArsenalPics/defaultpic.png\">";
			}
		else
			{
			echo "<img class=\"BlasterIMG\" name=\"WPNPic\" src=\"Images/ArsenalPics//" . $WeaponRow[2] . "\">";
			}
		echo "<select class=\"ImageList\" id=\"ImageList\" name=\"ImageList\" onchange=\"ChangePic(this.form)\">";
		echo $PicList;
		echo "</select>";
		
		$GetClaimsNumQuery = "SELECT count(*) FROM `hvzarsenalclaims` WHERE `wpnid` = $WeaponID AND `claimstate` > 0";
		$NumTaken = mysqli_fetch_row(mysqli_query($DBCon, $GetClaimsNumQuery))[0];
		echo "<input type=\"submit\" id=\"SaveWPN\" name=\"SaveWPN\" value=\"Save\" class=\"SaveWPN\">";
		echo "<p class=\"WPNNameTXT\">Name:</p>";
		echo "<input type=\"text\" id=\"WpnName\" name=\"WpnName\" class=\"WpnName\" value=\"" . $WeaponRow[1] . "\" maxlength=\"30\" autocomplete=\"off\">";
		echo "<p class=\"WPNNumTXT\">Number of Items In Stock:</p>";
		echo "<input type=\"number\" id=\"WpnNumber\" name=\"WpnNumber\" class=\"WpnNumber\" value=\"" . $WeaponRow[3] . "\" autocomplete=\"off\">";
		echo "<p class=\"WPNPriceTXT\">Weapon Rent Price:</p>";
		echo "<input type=\"text\" id=\"WpnCost\" name=\"WpnCost\" class=\"WpnCost\" value=\"" . $WeaponRow[4] . "\" autocomplete=\"off\">";
		echo "<p class=\"WPNRemaining\">Remaining<br>In Stock:</p>";
		echo "<p class=\"WPNRemNum\">" . ($WeaponRow[3] - $NumTaken) . "</p>";
		echo "<input type=\"submit\" id=\"DeleteWPN\" name=\"DeleteWPN\" value=\"Delete\" class=\"DeleteWPN\">";
		echo "</form>";
		$GetAllWeaponClaims = "SELECT `claimid`,`claimstate`,`claimdate`,`claimerid`,`usrname`,`usravy` FROM `hvzarsenalclaims` LEFT JOIN `hvzusrinfo` ON `claimerid`=`usrid` WHERE `wpnid` = $WeaponID AND `claimstate` < 3 ORDER BY `claimstate`";
		
		$Claims = mysqli_query($DBCon, $GetAllWeaponClaims);
		if ($Claims->num_rows>0)
			{
			while($ClaimRow = mysqli_fetch_array($Claims))
				{
				echo "<form class=\"ArsenalClaimEntry\"";
				// It's a request
				if ($ClaimRow[1] == 0)
					{
					echo " style=\"background-color: #E0AA0F;\" ";
					}
				// Approved Request
				else if ($ClaimRow[1] == 1)
					{
					echo " style=\"background-color: #4B721D;\" ";
					}
				// Person is Dead, get them to give you stuff back
				else if ($ClaimRow[1] == 2)
					{
					echo " style=\"background-color: #CE1126;\" ";
					}	
				echo " action=\"arsenalhandler.php\" method=\"post\">";
				// Hidden ID for the Claim
				echo "<input type=\"hidden\" id=\"ClaimID\" name=\"ClaimID\" value=\"" . $ClaimRow[0] . "\">";
				echo "<a href=\"playerstats.php?profId=" . $ClaimRow[3] . "\">";
				if ($ClaimRow[5] != "")
					{
					echo "<img class=\"ClaimerAvy\" name=\"ClaimerAvy\" src=\"Images/Avatars//" . $ClaimRow[5] . "\">";
					}
				else if ($ClaimRow[1] == 0 || $ClaimRow[1] == 1)
					{
					echo "<img class=\"ClaimerAvy\" name=\"ClaimerAvy\" src=\"Images/DefaultAvatars/DefaultHumanAv.png\">";
					}
				else if ($ClaimRow[1] == 2)
					{
					echo "<img class=\"ClaimerAvy\" name=\"ClaimerAvy\" src=\"Images/DefaultAvatars/DefaultZombieAv.png\">";
					}
				echo "<p class=\"ClaimerName\">" . $ClaimRow[4] . "</p>";
				echo "</a>";
				echo "<p class=\"ClaimChangeDate\">" . date("j/m/y - g:ia", strtotime($ClaimRow[2])) . "</p>";
				// If this is a request for a rent, we can deny it or approve it.
				if ($ClaimRow[1] == 0)
					{
					echo "<input type=\"submit\" id=\"ApproveClaim\" name=\"ApproveClaim\" value=\"Approve\" class=\"ApproveClaim\">";
					echo "<input type=\"submit\" id=\"DenyClaim\" name=\"DenyClaim\" value=\"Deny\" class=\"DenyClaim\">";
					}
				// If the claim was approved, we can mark it as returned/
				else if ($ClaimRow[1] == 1 || $ClaimRow[1] == 2)
					{
					echo "<input type=\"submit\" id=\"ReturnClaim\" name=\"ReturnClaim\" value=\"Confirm Return\" class=\"ReturnClaim\">";
					}
				echo "</form>";
				}
			}
		}
	echo "</div>";
	echo "<div class=\"ArsenalAdminNewEntry\">";
	echo "<form class=\"NewItemForm\" action=\"arsenalhandler.php\" method=\"post\">";
	echo "<p class=\"NewItmHeader\">New Item:</p>";
	echo "<p class=\"NewItmName\">Item Name:</p>";
	echo "<input type=\"text\" id=\"NewWpnNm\" name=\"NewWpnNm\" class=\"NewWpnNm\" value=\"\" maxlength=\"30\" autocomplete=\"off\">";
	echo "<p class=\"NewItmPrice\">Item Rent:</p>";
	echo "<input type=\"text\" id=\"NewWpnCost\" name=\"NewWpnCost\" class=\"NewWpnCost\" value=\"\" autocomplete=\"off\">";
	echo "<p class=\"NewItmStock\">Item Stock:</p>";
	echo "<input type=\"number\" id=\"NewWpnStock\" name=\"NewWpnStock\" class=\"NewWpnStock\" value=\"\" autocomplete=\"off\">";
	echo "<input type=\"submit\" id=\"NewWeapon\" name=\"NewWeapon\" value=\"New Item\" class=\"NewWeapon\">";
	echo "</form>";
	
	echo "<form class=\"UploadPicForm\" action=\"arsenalhandler.php\" method=\"post\" enctype=\"multipart/form-data\">";
	echo "<p class=\"UploadHeader\">Upload Image:</p>";
	echo "<input type=\"file\" name=\"ItemPicUp\" id=\"ItemPicUp\" class=\"ItemPicUp\">";
	echo "<input type=\"submit\" id=\"ArsenalUpPic\" name=\"ArsenalUpPic\" value=\"Upload\" class=\"ArsenalUpPic\">";
	echo "</form>";
	
	echo "<form class=\"DelPicForm\" action=\"arsenalhandler.php\" method=\"post\">";
	echo "<p class=\"DelHeader\">Remove Image:</p>";
	echo "<select class=\"JustImgSel\" id=\"JustImgSel\" name=\"JustImgSel\" onchange=\"ChangeDelPic(this.form)\">";
	echo $JustImages;
	echo "</select>";
	echo "<img class=\"DelIMG\" name=\"DelIMG\" src=\"Images/ArsenalPics/defaultpic.png\">";
	echo "<input type=\"submit\" id=\"DelIMGBtn\" name=\"DelIMGBtn\" value=\"Delete\" class=\"DelIMGBtn\">";
	echo "</form>";
	echo "</div>";
	
	if(isset($_POST["LoadError"]))
		{
		echo "<div class=\"ArsenalErrorDiv\">";
		if ($_POST["LoadError"] == 1)
			{
			echo "File with such name already exists.<br>Please rename the file if you wish to use it.";
			}
		else if ($_POST["LoadError"] == 2)
			{
			echo "Provide file is too large.<br>Please only upload files up to 1MB in size.";
			}
		else if ($_POST["LoadError"] == 3)
			{
			echo "Provide file is not an image.<br><br>Please only upload images.";
			}
		echo "</div>";
		}
	?>
	</body>
</html>