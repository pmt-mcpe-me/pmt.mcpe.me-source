function changeCmdName(oldName){
	var name = " ";
	var first = true;
	while(name.indexOf(" ") > -1 || name.indexOf(":") > -1){
		var promptMsg = "";
		if(!first){
			promptMsg = "Command names must not contain \" \" or \":\"!\n";
		}
		name = prompt(promptMsg + "Change command name to:", first ? oldName:name);
		if(name == null){
			return;
		}
		first = false;
	}
	$.post("ajax/changeCmdNameRequest.php", {
		oldName: oldName,
		newName: name
	},function(data, status){
		data = JSON.parse(data);
		if(data.status == "OK"){
			eval(data.evalCode);
		}
		else{
			alert("Error: " + data.status);
		}
	});
}
function changeCmdProperty(cmd, property, oldValue){
	var newValue = prompt("Change command description to:", oldValue);
	if(newValue == null){
		return;
	}
	$.post("ajax/changeCmdPropertyRequest.php", {
		cmd: cmd,
		name: property,
		value: newValue
	}, function(data, status){
		data = JSON.parse(data);
		if(data.status == "OK"){
			eval(data.evalCode);
		}
		else{
			alert("Error: " + data.status);
		}
	});
}
