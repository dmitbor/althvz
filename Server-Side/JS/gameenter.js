function LoadPage()
{
	$("#MercsBox").fadeOut(0);
	$("#OZBox").fadeOut(0);
}

function ShowMercInfo()
{
	$("#MercsBox").fadeIn();
}

function HideMercInfo()
{
if (document.getElementById("MercCheckBox").checked == false)
	{
	$("#MercsBox").fadeOut();
	}
}

function ShowOZInfo()
{
	$("#OZBox").fadeIn();
}

function HideOZInfo()
{
if (document.getElementById("OZCheckBox").checked == false)
	{
	$("#OZBox").fadeOut();
	}
}

function CheckChecked()
{
	if ($('#RuleCheck').is(':checked'))
		{
		document.getElementById("enter").disabled = false;
		}
	else
		{
		document.getElementById("enter").disabled = true;
		}
}

function NoMercOZ()
{
	if ($('#MercCheckBox').is(':checked'))
		{
		document.getElementById("OZCheckBox").disabled = true;
		}
	else if ($('#OZCheckBox').is(':checked'))
		{
		document.getElementById("MercCheckBox").disabled = true;
		}
	else
		{
		document.getElementById("MercCheckBox").disabled = false;
		document.getElementById("OZCheckBox").disabled = false;
		}
}