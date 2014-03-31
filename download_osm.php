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
$zip_osm = "output_osm.zip";
$zip_shape = "output_shape.zip";
$pfad_osm = "$dir_daten/$zip_osm";
$pfad_shape = "$dir_daten/$zip_shape";
if (file_exists($pfad_osm)) {
    header("Content-Disposition: attachment; filename=\"". urlencode($zip_osm) ."\"");
	readfile($pfad_osm);
	unlink(realpath($pfad_osm));
} 
else {
	echo "Fehler!<br/>";
    echo ".osm Datei entweder bereits runtergeladen oder noch keins erstellt.";
}
		?>
	<form action = "karte.html" method = "GET">
	Zur&uuml;ck zur Auswahl?
	<input type="submit" value="OK"\>
	</form>
	</body>	
</html>