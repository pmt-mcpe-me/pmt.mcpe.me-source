<?php
if(strpos(getallheaders()["Accept"], "json") !== false){
	echo json_encode(yaml_parse_file("perm.yml"), JSON_PRETTY_PRINT);
}

