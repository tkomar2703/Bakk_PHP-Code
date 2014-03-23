<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="map.css"></link>
		<title>OSM Export</title>		
		<link rel="shortcut icon" type="image/x-icon" href="favicon_osm.ico">
	</head>
	
	<body>
<?php
$dir_daten = 'D:/Bakk/Daten';
$datei = "output.zip";
$pfad = "$dir_daten/$datei";

header("Content-Disposition: attachment; filename=\"". urlencode($datei) ."\"");
readfile($pfad);
unlink(realpath($dir_daten."/".$datei));
		?>
		
	</body>	
</html>