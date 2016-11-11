function GetStory(form)
	{
	document.getElementById('GivenStoryID').value = form.children[4].value;
	document.getElementById('GivenStoryAccess').value = form.children[8].value;
	document.getElementById('StoryNameText').value = form.children[5].value;
	document.getElementById('StoryDescText').value = form.children[6].value;
	document.getElementById('StoryStateSelect').selectedIndex = form.children[7].value;
	document.getElementById('StoryAccessCode').innerHTML = form.children[8].value;
	document.getElementById('StoryAccessCode').style.visibility = "visible";
	document.getElementById('SaveStoryBtn').style.visibility = "visible";
	}
	
function SetPassUsed(form)
	{
	var SelectionState = form.children[0].checked;
	var SelectedKey = form.children[2].value;
	if (SelectionState == true)
		{
		form.style.backgroundColor = "#FFCCCC";
		$.post("tagusagehandler.php", {ExpandableKey: SelectedKey, ValueGiven: 1});
		}
	else
		{
		form.style.backgroundColor = "#FFFFFF";
		$.post("tagusagehandler.php", {ExpandableKey: SelectedKey, ValueGiven: 0});
		}
	}