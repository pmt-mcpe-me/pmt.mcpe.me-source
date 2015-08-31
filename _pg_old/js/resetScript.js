function confirmReset(name){
	var check = prompt("Are you sure to reset the plugin? All your changes to this plugin will be lost!\n\n" +
	"Type \"" + name + "\" to confirm resetting the plugin");
	if(check == null){
		return;
	}
	if(check == name){
		location = "/pg/resetSession.php";
	}
	else{
		alert("Name does not match!");
	}
}
