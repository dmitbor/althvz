function areYouSure()
	{
	var DoIt = confirm("Are you sure you want to end this game entirely?");
	if (DoIt == true)
		{
		$('#delConfirm').val('DoIt');
		document.forms["GameMNGR"].submit();
		}
	}
	
function changeIconPrev()
	{
	if (document.getElementById("IconList").value != "")
		{
		document.getElementById("GameIcon").src = "Images\\GameIcons\\" + document.getElementById("IconList").value;
		}
	}