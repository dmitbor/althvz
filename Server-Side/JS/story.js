var unseen = 0;

function ShowStory(form)
	{
	if (unseen == 0)
		{
		document.getElementById('StoryTitle').innerHTML = form.children[0].value;
		document.getElementById('StoryText').innerHTML = form.children[1].value;
		
		$("#InfoDiv").css('visibility','visible').hide().fadeIn('slow');
		unseen = 1;
		}
	else
		{
		$("#InfoDiv").fadeOut('slow', function()
			{
			document.getElementById('StoryTitle').innerHTML = form.children[0].value;
			document.getElementById('StoryText').innerHTML = form.children[1].value;
			$("#InfoDiv").fadeIn('slow');
			});
		}
	}