<html>
<head>
	<title>Crash dump parser</title>
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script>
		$(document).ready(function(){

		});
	</script>
</head>
<body>
<p>Please choose a method to send the crash dump.</p>
<hr>
<form action="result.php" method="post" enctype="multipart/form-data">
	<input type="radio" name="method" value="buffer" checked> Paste crash dump
	<br>
	<pre>----------------------REPORT THE DATA BELOW THIS LINE-----------------------
===BEGIN CRASH DUMP===
<textarea name="buffer" cols="100" rows="30"></textarea>
===END CRASH DUMP===
	</pre>
	<hr>
	<input type="radio" name="method" value="file"> {EXPERIMENTAL} Upload crash dump file<br>
	<input type="file" name="file"><br>
	<hr>
	<input type="radio" name="method" value="github"> {EXPERIMENTAL} GitHub link <br>
	<hr>
	<input type="submit">
	<input type="checkbox" name="api"> output as JSON
</form>
</body>
</html>

