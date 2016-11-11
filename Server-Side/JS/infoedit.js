function changeAvyPrev()
	{
	if (document.getElementById("AvySelect").value == "Remove")
		{
		document.getElementById("AvyPrev").src = "";
		document.getElementById("AvyWarnPar").style.visibility = "visible";
		}
	else if (document.getElementById("AvySelect").value == "")
		{
		document.getElementById("AvyPrev").src = "";
		document.getElementById("AvyWarnPar").style.visibility = "hidden";
		}
	else
		{
		document.getElementById("AvyPrev").src = "Images/Avatars/" + document.getElementById("AvySelect").value;
		document.getElementById("AvyWarnPar").style.visibility = "hidden";
		}
	}
	
function CheckRequirements()
	{
	var PassOne = document.getElementById("NewPassText1").value;
	var PassTwo = document.getElementById("NewPassText2").value;
	var NewEmail = document.getElementById("NewEmailText").value;
	
	var PassUsed = 0;
	var PassConfirmed = 0;
	
	var EmailUsed = 0;
	var EmailConfirmed = 0;
	
	if (PassOne != "" && PassTwo != "")
		{
		PassUsed = 1;
		if (PassOne == PassTwo && PassOne.length > 6 && PassTwo.length > 6)
			{
			PassConfirmed = 1;
			}
		}
		
	if (NewEmail != "" )
		{
		EmailUsed = 1;
		if (NewEmail.indexOf("@") != -1 && NewEmail.indexOf(".") != -1)
			{
			EmailConfirmed = 1;
			}
		}
		
	if ((PassUsed == 1 && PassConfirmed == 1 && EmailUsed == 1 && EmailConfirmed == 1) || (PassUsed == 1 && PassConfirmed == 1 && EmailUsed == 0) || (PassUsed == 0 && EmailUsed == 1 && EmailConfirmed == 1))
		{
		MoveChangesUp();
		}
	else
		{
		MoveChangesDown();
		}
	}
	
function MoveChangesUp()
	{
	$("#MovedForm").animate({bottom: '-12.5%'});
	}
	
function MoveChangesDown()
	{
	$("#MovedForm").animate({bottom: '-30%'});
	}