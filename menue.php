<!DOCTYPE HTML>
<html>
	<head>
		<title>OSM Export</title>		
		<link rel="shortcut icon" type="image/x-icon" href="favicon_osm.ico">
	</head>
	
	<body>

		<h2>OSM-Daten exportieren</h2>
		<h3>Hauptmen&uuml;</h3>
		
	<?php 
		set_time_limit(0); // ansonsten nach 30 Sekunden abbruch
		$rueck = " 2>&1"; // Rückgabewert der Tools umleiten
		$dir = "D:\Bakk\osmosis-latest\bin"; 
		$befehl = '"'.$dir.'\osmosis" --read-pbf file="'.$dir.'\australia-oceania-latest.osm.pbf" --bounding-polygon file='.$dir.'"\country.poly" --write-xml file='.$dir.'"\australia.osm"'.$rueck;
		//echo "$befehl";
		//$output = shell_exec('"D:\Bakk\osmosis-latest\bin\osmosis" --read-pbf file="D:\Bakk\osmosis-latest\bin\australia-oceania-latest.osm.pbf" --bounding-polygon file="D:\Bakk\osmosis-latest\bin\country.poly" --write-xml file="D:\Bakk\osmosis-latest\bin\australia.osm" 2>&1');  
		$output = shell_exec($befehl);  
		echo "$output";
	?>		
	</body>	
</html>