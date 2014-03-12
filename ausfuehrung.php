<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="map.css"></link>
		<title>OSM Export</title>		
		<link rel="shortcut icon" type="image/x-icon" href="favicon_osm.ico">
	</head>
	
	<body>

		<h2>OSM-Daten exportieren</h2>
		<h3>Hauptmen&uuml;</h3>
		
	<?php 
	
	$lon1 = $_GET["lon1"];
	$lat1 = $_GET["lat1"];
	$lon2 = $_GET["lon2"];
	$lat2 = $_GET["lat2"];
	
	if ($lat1 > $lat2) {
		$top = $lat1;
		$bot = $lat2;}
	elseif ($lat1 == $lat2) {
		echo "Fehler: Latitude 1 == Latitude 2 <br/>"; }
	else {
		$top = $lat2;
		$bot = $lat1; }
		
	if ($lon1 > $lon2) {
		$left = $lon2;
		$right = $lon1; }
	elseif ($lon1 == $lon2) {
		echo "Fehler: Longitude 1 == Longitude 2 <br/>"; }
	else {
		$left = $lon1;
		$right = $lon2; }
	if (isset($left, $right, $top, $bot)) { // wenn Bounding Box (also keine gleichen Koordinaten)
		?>
		<table border="0">
			<tr>
				<th colspan="4">Bounding Box:</th>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td align="center" width="100">West:</td><td align="left" width="100"><?php echo $left; ?> &deg;</td><td align="center" width="100">Ost:</td><td align="left" width="100"><?php echo $right; ?> &deg;</td>
			</tr>
			<tr>
				<td align="center">Nord:</td><td><?php echo $top; ?> &deg;</td><td align="center">S&uuml;d:</td><td><?php echo $bot; ?> &deg;</td>
			</tr>
		</table>
		<?php
		
		//echo "<b>Bounding Box:<b> <br/> West: ".$left."\tOst: ".$right."<br/>Nord: ".$top."\tSüd: ".$bot; }
		set_time_limit(0); // ansonsten nach 30 Sekunden abbruch
		//$rueck = " 2>&1"; // Rückgabewert der Tools umleiten
		$dir = 'D:/Bakk/osmosis-latest/bin';
		//$befehl = '"'.$dir.'\osmosis" --read-pbf file="'.$dir.'\australia-oceania-latest.osm.pbf" --bounding-polygon file='.$dir.'"\country.poly" --write-xml file='.$dir.'"\australia.osm"'.$rueck;
		$befehl = sprintf(
			'%s --read-pbf file=%s --bounding-box top='.$top.' bottom='.$bot.' right='.$right.' left='.$left.' --write-xml file=%s 2>&1', // "2>&1" Rückgabewert der Tools anzeigen
			escapeshellarg($dir . '/osmosis'),
			escapeshellarg($dir . '/australia-oceania-latest.osm.pbf'),
			//escapeshellarg($dir . '/country.poly'),
			escapeshellarg($dir . '/australia_php.osm')
			);
		
		//echo "$befehl";
		//$output = shell_exec('"D:\Bakk\osmosis-latest\bin\osmosis" --read-pbf file="D:\Bakk\osmosis-latest\bin\australia-oceania-latest.osm.pbf" --bounding-polygon file="D:\Bakk\osmosis-latest\bin\country.poly" --write-xml file="D:\Bakk\osmosis-latest\bin\australia.osm" 2>&1');  
		$output = shell_exec($befehl);  
		echo "$output";
	}
	?>		
	</body>	
</html>