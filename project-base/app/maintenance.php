<?php
	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-after: 300');
	header('Expires: Thu, 01 Dec 1994 16:00:00 GMT');
	header('Last modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
	header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Probíhá údržba</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>

	<body>
		<div>
			<h1>Probíhá údržba</h1>
			<p>
				Zkuste to prosím znovu za pár minut.
			</p>
		</div>
	</body>
</html>
