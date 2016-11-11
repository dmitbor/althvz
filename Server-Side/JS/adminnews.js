function GetNews(form)
	{
	document.getElementById('NewsSaveIDHidden').value = form.children[2].value;
	document.getElementById('NewsTitleText').value = form.children[3].value;
	document.getElementById('NewsDescText').value = form.children[4].value;
	document.getElementById('SaveNews').style.visibility = "visible";
	document.getElementById('EmailNews').style.visibility = "visible";
	}