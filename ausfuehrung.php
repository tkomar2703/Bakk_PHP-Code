<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="map.css"></link>
		<title>OSM Export</title>		
		<link rel="shortcut icon" type="image/x-icon" href="favicon_osm.ico">
	</head>
	
	<body>

		<h2>OSM-Daten exportieren</h2>
		
	<?php 
	// Funktion zum rekursiven Zippen von Ordnern
function zippen($sourcePath, $zip) {
	$archiv = new ZipArchive();
	$dirIter = new RecursiveDirectoryIterator($sourcePath);
	$iter = new RecursiveIteratorIterator($dirIter);
	foreach($iter as $element) {
		$dir = str_replace($sourcePath, '', $element->getPath()) . '/';
		if ($element->isDir()) {
        // Ordner erstellen (damit werden auch leere Ordner hinzugefügt
			$zip->addEmptyDir($dir);
		} elseif ($element->isFile()) {
			$file = $element->getPath() . '/' . $element->getFilename();
			$fileInArchiv = $dir . $element->getFilename();
        // Datei dem Archiv hinzufügen
			$zip->addFile($file, $fileInArchiv);
		}
	}
}


// Pfade auf Variablen schreiben
$dir_osmosis = 'D:/Bakk/osmosis/bin';
$dir_daten = 'D:/Bakk/Daten';
$dir_wget = 'D:/Bakk/GnuWin32/bin';
$dir_convert_filter = 'D:/Bakk';
$dir_ogr2ogr = 'D:\Bakk\ogr2ogr';
$dir_shape = 'D:/Bakk/Daten/output_shape';
		
$loeschen = $_GET["loeschen"];
$lon1 = $_GET["lon1"];
$lat1 = $_GET["lat1"];
$lon2 = $_GET["lon2"];
$lat2 = $_GET["lat2"];


if (isset($_GET['ausw_bounding'])) {// Bounding Box oder Polygon
	$bounding_art = $_GET['ausw_bounding'];
}

// Überprüfen ob alle notwendigen Variablen übergeben wurden
if (empty($_GET["lon1"]) || empty($_GET["lon2"]) || empty($_GET["lat1"]) || empty($_GET["lat2"])) {		
	echo "Keine oder nicht alle Eckpunkte f&uuml;r die Bounding Box gew&#xE4;hlt";
	?>
	<form action = "karte.html" method = "GET">
	Zur&uuml;ck zur Auswahl?
	<input type="submit" value="OK"\>
	</form>
	<?php
	exit;		
}
$tag_ausw = $_GET['tag']; // welcher Tag wurde ausgewählt	
/*if (isset($_GET['tag'])) {// welche Tags wurden ausgewählt
	$tag = $_GET["tag"];
	$tag_ausw = implode(' ',$tag);
//	$anz = count($tag);
} */
if (!empty($_GET['keep_eig'])) {// Benutzerdefinierter Tag hinzugefügt
	$keep_eig = $_GET['keep_eig'];
	if (isset($_GET['tag'])) {
		$tag_ausw = $tag_ausw." ".$keep_eig;
	}
	else {
		$tag_ausw = $keep_eig;
	}
}

if (!empty($_GET['befehl_eig'])) {// Benutzerdefinierte Tags löschen
	$befehl_eig = $_GET['befehl_eig'];
}

	
if (isset($_GET['typ'])) {// Export als Shape und/oder OSM
	$typ = $_GET["typ"];
	$typ_ausw = implode(' ',$typ);
	$anz_typ = count($typ);
}
else {
	echo "Keine Downloadoption gew&#xE4;hlt";
	?>
	<form action = "karte.html" method = "GET">
	Zur&uuml;ck zur Auswahl?
	<input type="submit" value="OK"\>
	</form>
	<?php
	exit;
}

$server = $_GET['server'];
$shapes = $_GET['shapes'];

mkdir($dir_daten); // Ordner für alle Daten
mkdir($dir_shape); // Ordner erstellen, wo die Shapes gespeichert werden

// Bounding Box Koordinaten "sortieren"
if ($lat1 > $lat2) {
	$top = $lat1;
	$bot = $lat2;}
else {
	$top = $lat2;
	$bot = $lat1; 
	}
		
if ($lon1 > $lon2) {
	$left = $lon2;
	$right = $lon1; 
	}
else {
	$left = $lon1;
	$right = $lon2; 
	}
	
$datei_handle=fopen($dir_daten."/overpass_api.xml","w+"); // .xml für Overpass-API
fwrite($datei_handle,"<osm-script>\r\n<union>\r\n<bbox-query e=\"".$right."\" n=\"".$top."\" s=\"".$bot."\" w=\"".$left."\"/>\r\n<recurse type=\"up\"/><recurse type=\"down\"/>\r\n</union>\r\n<print mode=\"meta\" order=\"quadtile\"/>\r\n</osm-script>");
fclose($datei_handle);
		
set_time_limit(0); // ansonsten nach 30 Sekunden abbruch
$rueck = " 2>&1"; // Rückgabewert der Tools umleiten

$befehl_overpass = sprintf(
	'%s '.$server.'interpreter --ignore-length --post-file=%s -O %s',
	escapeshellarg($dir_wget . '/wget'),
	escapeshellarg($dir_daten . '/overpass_api.xml'),
	escapeshellarg($dir_daten . '/output_overpass.osm')
	);

$output_overpass = shell_exec($befehl_overpass);  // Export mittels wget/Overpass-AP	

if (strcmp($bounding_art,'ausw_bpoly') == 0) // wenn Bounding Polygon -> Polygon File erstellen und mittels Osmosis ausschneiden
{
	$poly_koord = $_GET["poly_koord"];
	$datei_handle_poly=fopen($dir_daten."/poly_koord.poly","w+"); // .xml für Overpass-API
	fwrite($datei_handle_poly,"polygon\r\n1\r\n".$poly_koord."END\r\nEND");
	fclose($datei_handle_poly);
	
	$befehl_osmosis = sprintf(
	'%s --read-xml file=%s --bounding-polygon file=%s completeWays=yes --write-xml file=%s',
	escapeshellarg($dir_osmosis . '/osmosis'),
	escapeshellarg($dir_daten . '/output_overpass.osm'),
	escapeshellarg($dir_daten . '/poly_koord.poly'),
	escapeshellarg($dir_daten . '/output_osmosis.osm')
	);

$output_osmosis = shell_exec($befehl_osmosis);
$file_convert = '/output_osmosis.osm';
}
		
else {$file_convert = '/output_overpass.osm';}

$befehl_convert_to_o5m = sprintf(
	'%s %s -o=%s',
	escapeshellarg($dir_convert_filter . '/osmconvert'),
	escapeshellarg($dir_daten . $file_convert),
	escapeshellarg($dir_daten . '/output_convert.o5m')
	//escapeshellarg($dir_daten . '/output_convert.osm')
);
		
$output_convert = shell_exec("$befehl_convert_to_o5m");

// Befehl selbst formuliert
if (isset($befehl_eig)) {
	$befehl_filter = sprintf(
	"%s %s $befehl_eig -o=%s",
	escapeshellarg($dir_convert_filter . '/osmfilter'),
	escapeshellarg($dir_daten . '/output_convert.o5m'),
	escapeshellarg($dir_daten . '/output_filter.osm')
	);
}
// Tags ausgewählt?	
elseif (isset($tag_ausw)) { // ja
	$befehl_filter = sprintf(
	"%s %s --keep=\"$tag_ausw\" -o=%s",
	escapeshellarg($dir_convert_filter . '/osmfilter'),
	escapeshellarg($dir_daten . '/output_convert.o5m'),
	escapeshellarg($dir_daten . '/output_filter.osm')
	);
}
else { // nein
	$befehl_filter = sprintf(
	"%s %s -o=%s",
	escapeshellarg($dir_convert_filter . '/osmfilter'),
	escapeshellarg($dir_daten . '/output_convert.o5m'),
	escapeshellarg($dir_daten . '/output_filter.osm')
	);
}
$output_convert = shell_exec("$befehl_filter");

	
$befehl_ogr2ogr = sprintf(
	"%s par",
	escapeshellarg($dir_ogr2ogr . '/SDKShell.bat')
);
			
$befehl_ogr2ogr_shape = sprintf(
	"SET par=ogr2ogr -f \"ESRI Shapefile\" %s %s ".$shapes." --config OSM_USE_CUSTOM_INDEXING NO",
	escapeshellarg($dir_shape . "/shape"),
	escapeshellarg($dir_daten . '/output_filter.osm')
);

$befehl_ogr2ogr_json = sprintf(
	"SET par=ogr2ogr -f \"GeoJSON\" %s %s ".$shapes." --config OSM_USE_CUSTOM_INDEXING NO",
	escapeshellarg($dir_daten . "/output_geojson.geojson"),
	escapeshellarg($dir_daten . '/output_filter.osm')
);
	
$filename = "$dir_daten/output.zip";
$filename_s = "$dir_daten/output_shape.zip";
$zip = new ZipArchive();

switch ($typ_ausw) { // welche Downloadoption wurde gewählt
	
	case "shp":	
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$output_ogr2ogr_shape = shell_exec("$befehl_ogr2ogr_shape && $befehl_ogr2ogr");
		zippen($dir_shape, $zip);
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen); 
		break;
		
	case "osm":
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$zip->addFile($dir_daten . "/output_filter.osm","output_filter.osm");
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen);
		break;
		
	case "shp osm";
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$zip->addFile($dir_daten . "/output_filter.osm","output_filter.osm");
		$output_ogr2ogr_shape = shell_exec("$befehl_ogr2ogr_shape && $befehl_ogr2ogr");
		zippen($dir_shape, $zip);
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen);			
		break;
		
	case "json":	
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$output_ogr2ogr_geojson = shell_exec("$befehl_ogr2ogr_json && $befehl_ogr2ogr");
		$zip->addFile($dir_daten . "/output_geojson.geojson","output_geojson.geojson");
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen); 
		break;
		
	case "osm json":	
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$output_ogr2ogr_geojson = shell_exec("$befehl_ogr2ogr_json && $befehl_ogr2ogr");
		$zip->addFile($dir_daten . "/output_filter.osm","output_filter.osm");
		$zip->addFile($dir_daten . "/output_geojson.geojson","output_geojson.geojson");
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen); 
		break;
		
	case "shp json":	
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$output_ogr2ogr_geojson = shell_exec("$befehl_ogr2ogr_json && $befehl_ogr2ogr");
		$zip->addFile($dir_daten . "/output_geojson.geojson","output_geojson.geojson");
		$output_ogr2ogr_shape = shell_exec("$befehl_ogr2ogr_shape && $befehl_ogr2ogr");
		zippen($dir_shape, $zip);
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen); 
		break;
		
	case "shp osm json":	
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$output_ogr2ogr_geojson = shell_exec("$befehl_ogr2ogr_json && $befehl_ogr2ogr");
		$zip->addFile($dir_daten . "/output_filter.osm","output_filter.osm");
		$zip->addFile($dir_daten . "/output_geojson.geojson","output_geojson.geojson");
		$output_ogr2ogr_shape = shell_exec("$befehl_ogr2ogr_shape && $befehl_ogr2ogr");
		zippen($dir_shape, $zip);
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen); 
		break;	
		
}
?>	
	
</body>	

</html>