<?php
$d = <<<d
<!doctype HTML>
<html>
<head>
	<title>PL webmap</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		body{max-width:50em;margin:auto;font-family:Tahoma,sans-serif;font-size:100%;}
		input{width:100%;}
		a,a:visited{color:#00f}
	</style>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
</head>
<body>
	<h1><a href="index.html">PL webmap</a></h1>
	<hr/>
	<h2>What</h2>
	<p>This is a map made for <a href="http://thepilotslife.com" target="_blank">The Pilot's Life</a> (aka PL) <a href="http://sa-mp.com" target="_blank">SA-MP</a> server which shows the map with overlays and data for runways and roads.</p>
	<p>Inspired by <a href="http://plmap.guth3d.com/" target="_blank">the interactive webmap by GeriBoss</a></p>
	<hr/>
	<h2>Requirements</h2>
	<p>A browser with svg support and basic javascript support if you want to interact with it.</p>
	<hr/>
	<h2>Credits & thanks</h2>
	<p>
		Map images were rendered using <a href="http://www.steve-m.com/downloads/tools/mapviewer/" target="_blank">steve-m's map viewer</a><br/>
		Thanks to P\$G for teleporting my infernus to seychelles island to map the roads (^^,)<br/>
		Thanks to Haydz and Aaron, past and current owners/developers of PL<br/>
		Thanks to whoever made the PL favicon, cuz I stole it and used it here :)<br/>
		<br/>
		This site, tools and data by me, robin_be<br/>
		<br/>
		Greetings to all my PL buddies<br/>
	</p>
	<hr/>
	<h2>Source</h2>
	<p><a href="https://github.com/thepilotslife/plmap">https://github.com/thepilotslife/plmap</a></p>
</body>
</html>
d;

$d = str_replace("\n", '', $d);
$d = str_replace("\r", '', $d);
$d = str_replace("\t", '', $d);
file_put_contents('about.html', $d);
