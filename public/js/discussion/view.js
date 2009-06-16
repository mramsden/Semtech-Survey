var lastCommentId = 0;
var commentCounter = 0;

function addComment()
{
	if ($("textarea#comment").val() == "")
	{
		alert("You must enter some text for the comment.");
		return;
	}
	
	$("input[type=submit]").attr('disabled', true);
	$("textarea#comment").attr('disabled', true);
	$.ajax({
		type: "POST",
		url: "/discussion/add",
		data: "userid="+$("input#userid").val()+"&techid="+$("input#techid").val()+"&comment="+$("textarea#comment").val()+"&replyto="+$("input#replyto").val(),
		dataType: "json",
		success: function(data) {
			if (data.result != false)
			{
				if (data.result.replyto == 0)
				{
					displayComment(data.result);
				}
				else
				{
					displayCommentReply(data.result.replyto, data.result);
					window.location = "#comment"+data.result.id;
				}	
			}
			$("p#replytomessage").remove();
			$("input#replyto").val(0);
			$("textarea#comment").val("");
			$("input[type=submit]").attr('disabled', false);
			$("textarea#comment").attr('disabled', false);
		}
	});
}

function fetchComments()
{
	$.ajax({
		type: "GET",
		url: "/discussion/list?techid="+$("input#techid").val()+"&lastcomment="+lastCommentId,
		dataType: "json",
		success: function(data) {
			if (data.result != false)
			{
				$.each(data.result, function() {
					commentCounter++;
					if (this.replyto == 0)
					{
						displayComment(this);
					}
					else
					{
						displayCommentReply(this.replyto, this);
					}
				});
			}
		}
	});
}

function replyToReset()
{
	$("p#replytomessage").remove();
	$("input#replyto").val(0);
}

function replyto(commentid)
{
	$("p#replytomessage").remove();
	$("<p id=\"replytomessage\">You are replying to a comment. <a href=\"javascript: replyToReset();\">Click here</a> to clear this reply.</p>").appendTo("form#commentform");
	$("input#replyto").val(commentid);
	window.location = "#commentform";
}

function displayComment(comment)
{
	$("<div class=\"comment\" id=\"comment"+comment.id+"\" style=\"display: none;\"><p><span class=\"commentcontrols\"><a href=\"javascript: replyto("+comment.id+")\">Reply</a></span><span class=\"commentheader\">"+commentCounter+". "+comment.user+" - "+comment.postedon+"</span><br/>"+comment.message+"</p></div>").prependTo("div#comments");
	$("div#comment"+comment.id).show("slow");
	lastCommentId = comment.id;
}

function displayCommentReply(parentcommentid, comment)
{
	$("<div class=\"commentreply\" id=\"comment"+comment.id+"\" style=\"display: none;\"><p><span class=\"commentheader\">"+commentCounter+". "+comment.user+" - "+comment.postedon+"</span><br/>"+comment.message+"</p></div>").appendTo("div#comment"+parentcommentid);
	$("div#comment"+comment.id).show("slow");
	lastCommentId = comment.id;
}

$(function() {
	fetchComments();
	setInterval("fetchComments()", 10000);
});