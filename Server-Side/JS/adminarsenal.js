function ChangePic(form)
	{
	if (form.children[2].value != "")
		{
		form.children[1].src = "Images/ArsenalPics/" + form.children[2].value
		}
	}
	
function ChangeDelPic(form)
	{
	if (form.children[1].value != "")
		{
		form.children[2].src = "Images/ArsenalPics/" + form.children[1].value;
		document.getElementById('DelIMGBtn').style.visibility = "visible"
		}
	else if (form.children[1].value == "")
		{
		form.children[2].src = "Images/ArsenalPics/defaultpic.png";
		document.getElementById('DelIMGBtn').style.visibility = "hidden"
		}
	}