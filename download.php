<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="map.css"></link>
		<title>OSM Export</title>		
		<link rel="shortcut icon" type="image/x-icon" href="favicon_osm.ico">
	</head>
	
	<body>
<?php
$loeschen = $_GET["loeschen"];
$dir_daten = 'D:/Bakk/Daten/';
$zip = "output.zip";
$pfad = "$dir_daten"."$zip";


if (file_exists($pfad)) {
	header("Content-Disposition: attachment; filename=\"". urlencode($zip) ."\"");
	readfile($pfad);
	
	if ($loeschen == "loesch_ja") {
		if(is_dir($dir_daten) == true){
			$pfad = $dir_daten;
				function rrmdir($dir) {
					if (is_dir($dir)) {
						$objects = scandir($dir);
							foreach ($objects as $object) {
								if ($object != "." && $object != "..") {
									if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
								}
							}
						reset($objects);
						rmdir($dir);
					}
				}
				rrmdir($pfad);                                    
		}	
	}
} 
else {
	echo "Fehler!<br/>";
}
		?>
	<form action = "karte.html" method = "GET">
	Zur&uuml;ck zur Auswahl?
	<input type="submit" value="OK"\>
	</form>	
	</body>	
</html>