<?php
if (!isset($_POST["DeleteWPN"]) && !isset($_POST["SaveWPN"]) && !isset($_POST["ApproveClaim"]) && !isset($_POST["DenyClaim"]) && !isset($_POST["ReturnClaim"]) && !isset($_POST["NewWeapon"]) && !isset($_POST["ArsenalUpPic"]) && !isset($_POST["DelIMGBtn"]) && !isset($_POST["MakeClaim"]) && !isset($_POST["UnClaim"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
$date = date('Y-m-d H:i:s');
	
// Saving info for an Arsenal Item
if (isset($_POST["SaveWPN"]))
	{
	$WeaponID = $_POST["WpnID"];
	$WeaponName = mysqli_real_escape_string($DBCon, $_POST["WpnName"]);
	$WeaponPrice = $_POST["WpnCost"];
	$WeaponStock = $_POST["WpnNumber"];
	$WeaponPicture = $_POST["ImageList"];
	
	// If the picture value is not Current, switch to picture.
	if ($WeaponPicture != "")
		{
		$UpdateWeaponQuery = "UPDATE `hvzarsenalitems` SET `wpnname`='$WeaponName',`wpnpic`='$WeaponPicture',`wpnnum`=$WeaponStock,`wpncost`=$WeaponPrice WHERE `wpnid` = $WeaponID";
		}
	// Otherwise, update only the other info.
	else
		{
		$UpdateWeaponQuery = "UPDATE `hvzarsenalitems` SET `wpnname`='$WeaponName',`wpnnum`=$WeaponStock,`wpncost`=$WeaponPrice WHERE `wpnid` = $WeaponID";
		}
	// Run the update.
	mysqli_query($DBCon, $UpdateWeaponQuery);
	}
// Deleting a Weapon Entry.
else if (isset($_POST["DeleteWPN"]))
	{
	$WeaponID = $_POST["WpnID"];
	
	// Delete all of the Claims related to the weapon
	$DeleteWeaponClaims = "DELETE FROM `hvzarsenalclaims` WHERE `wpnid` = $WeaponID";
	mysqli_query($DBCon, $DeleteWeaponClaims);
	
	// Delete the actual weapon entry
	$DeleteWeaponEntry = "DELETE FROM `hvzarsenalitems` WHERE `wpnid` = $WeaponID";
	mysqli_query($DBCon, $DeleteWeaponEntry);
	}
// Approving a claim
else if (isset($_POST["ApproveClaim"]))
	{
	$ClaimId = $_POST["ClaimID"];
	
	$SetClaimToApproved = "UPDATE `hvzarsenalclaims` SET `claimstate` = 1,`claimdate` = '$date' WHERE `claimid` = $ClaimId";
	// Run the update.
	mysqli_query($DBCon, $SetClaimToApproved);
	}
// Shooting down a claim
else if (isset($_POST["DenyClaim"]))
	{
	$ClaimId = $_POST["ClaimID"];
	
	$SetClaimToDie = "UPDATE `hvzarsenalclaims` SET `claimstate` = 3,`claimdate` = '$date' WHERE `claimid` = $ClaimId";
	// Run the update.
	mysqli_query($DBCon, $SetClaimToDie);
	}
// Returned Item Check-In
else if (isset($_POST["ReturnClaim"]))
	{
	$ClaimId = $_POST["ClaimID"];
	$RemoveClaim = "DELETE FROM `hvzarsenalclaims` WHERE `claimid` = $ClaimId";
	// Remove the Entry.
	mysqli_query($DBCon, $RemoveClaim);
	}
// Create a new Item entry.
else if (isset($_POST["NewWeapon"]))
	{
	$ItemName = mysqli_real_escape_string($DBCon, $_POST["NewWpnNm"]);
	$ItemPrice = mysqli_real_escape_string($DBCon, $_POST["NewWpnCost"]);
	$ItemStock = mysqli_real_escape_string($DBCon, $_POST["NewWpnStock"]);
	
	// Insert the item
	$CreateNewItem = "INSERT INTO `hvzarsenalitems`(`wpnname`, `wpnnum`, `wpncost`) VALUES ('$ItemName',$ItemStock,$ItemPrice)";
	mysqli_query($DBCon, $CreateNewItem);
	}
// Upload a new Weapon Picture
else if (isset($_POST["ArsenalUpPic"]))
	{
	if (isset($_FILES["ItemPicUp"]) && $_FILES["ItemPicUp"]["name"] != "")
		{
		$Failure = 0;
		$target_dir = "Images/ArsenalPics//";
		$target_file = $target_dir . basename($_FILES["ItemPicUp"]["name"]);
		$FileName = basename($_FILES["ItemPicUp"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			
		// File already on the server with such name?
		if (file_exists($target_file))
			{
			$Failure = 1;
			}
				
		// Check file size against 1MB max
		if ($_FILES["ItemPicUp"]["size"] > 1048576)
			{
			$Failure = 2;
			}
				
		// Check if file is an image. Check against caps, because some servers are whiny about it.
		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG" && $imageFileType != "GIF")
			{
			$Failure = 3;
			}
			
		// Fall back and whine to the user if errors are caught.
		if ($Failure != 0)
			{
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"adminarsenal.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"LoadError\" value=\"". $Failure . "\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		// Success, upload the image.
		else
			{
			move_uploaded_file($_FILES["ItemPicUp"]["tmp_name"], $target_file);
			}
		}
	}
// Delete selected Image
else if (isset($_POST["DelIMGBtn"]))
	{
	$SelectedImage = $_POST["JustImgSel"];
	unlink('Images/ArsenalPics//' . $SelectedImage);
	}
// Make a claim on an item
else if (isset($_POST["MakeClaim"]))
	{
	session_start();
	$MyId = $_SESSION["userId"];
	$WeaponID = $_POST["WeaponID"];
	
	// Insert Claim
	$CreateClaimQuery = "INSERT INTO `hvzarsenalclaims`(`wpnid`, `claimerid`, `claimstate`, `claimdate`) VALUES ($WeaponID,$MyId,0,'$date')";
	mysqli_query($DBCon, $CreateClaimQuery);
	
	header("Location: arsenal.php");
	die();
	}
else if (isset($_POST["UnClaim"]))
	{
	$ClaimID = $_POST["ClaimID"];
	
	// Remove Claim
	$RemoveClaimQuery = "DELETE FROM `hvzarsenalclaims` WHERE `claimid` = $ClaimID";
	mysqli_query($DBCon, $RemoveClaimQuery);
	
	header("Location: arsenal.php");
	die();
	}
	
// Finish everything and get out.
header("Location: adminarsenal.php");
die();
?>