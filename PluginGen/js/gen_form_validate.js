var hasWarnedAuthors = false;
$(document).ready(function(){
	$("#_name").focusout(function(){
		var name = $("#_name").val().trim();
		if(name.split(" ").length != 1){
			alert("Name of plugin must not contain spaces!");
		}
	});
	$("#_author").focusout(function(){
		var author = $("#_author").val().trim();
		if(hasWarnedAuthors){
			return;
		}
	});
});