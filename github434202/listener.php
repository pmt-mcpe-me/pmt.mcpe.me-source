<?php
function curlGet($url){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: pmt.mcpe.me/1.1"]);
  $r = curl_exec($ch);
  curl_close($ch);
  return $r;
}
header("Content-Type: text/plain");
$inputStream = fopen("php://input", "rt");
$input = stream_get_contents($inputStream);
fclose($inputStream);
$headers = getallheaders();
$data = json_decode($input);
$event = $headers["X-GitHub-Event"];
if($event === "ping") {
  echo "PONG";
  exit;
}
if($event === "push") goto onPush;
if($event === "pull_request") goto onPr;
http_response_code(400);
echo "Unsupported event";
onPush:
// var_dump($data);
$zipball = curlGet(str_replace(["{archive_format}", "{/ref}"], ["zipball", $data->after], $data->repository->archive_url));
var_dump($zipball);
exit;
onPr:
