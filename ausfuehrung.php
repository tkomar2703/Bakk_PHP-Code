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
$dir_osm = 'D:/Bakk/osmosis-latest/bin';
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

if (empty($_GET["poly_koord"])) { echo "idiot";}
else {
$poly_koord = $_GET["poly_koord"];
echo $poly_koord;
$datei_handle_poly=fopen($dir_convert_filter."/poly_koord.poly","w+"); // .xml für Overpass-API
fwrite($datei_handle_poly,"polygon\r\n1\r\n".$poly_koord."END\r\nEND");
fclose($datei_handle_poly);
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
	//echo $tag_ausw;
//	$tag = $_GET["tag"];
//	$tag_ausw = implode(' ',$tag);
//	$anz = count($tag);
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
		
		
		//echo "<b>Bounding Box:<b> <br/> West: ".$left."\tOst: ".$right."<br/>Nord: ".$top."\tSüd: ".$bot; }
set_time_limit(0); // ansonsten nach 30 Sekunden abbruch
$rueck = " 2>&1"; // Rückgabewert der Tools umleiten

$befehl_overpass = sprintf(
	'%s '.$server.'interpreter --ignore-length --post-file=%s -O %s',
	escapeshellarg($dir_wget . '/wget'),
	escapeshellarg($dir_daten . '/overpass_api.xml'),
	escapeshellarg($dir_daten . '/output_overpass.osm.pbf')
	);

$output_overpass = shell_exec($befehl_overpass);  // Export mittels wget/Overpass-AP	
//echo "$output_overpass";	

		//$befehl = '"'.$dir.'\osmosis" --read-pbf file="'.$dir.'\australia-oceania-latest.osm.pbf" --bounding-polygon file='.$dir.'"\country.poly" --write-xml file='.$dir.'"\australia.osm"'.$rueck;
		//$befehl = sprintf(
		//	'%s --read-pbf file=%s --bounding-box top='.$top.' bottom='.$bot.' right='.$right.' left='.$left.' --write-xml file=%s 2>&1', // "2>&1" Rückgabewert der Tools anzeigen
		//	escapeshellarg($dir_osm . '/osmosis'),
		//	escapeshellarg($dir_osm . '/my.osm.pbf'),
			//escapeshellarg($dir_osm . '/country.poly'),
		//	escapeshellarg($dir_osm . '/australia_php.osm')
		//	);
		
		//echo "$befehl";
		//$output = shell_exec('"D:\Bakk\osmosis-latest\bin\osmosis" --read-pbf file="D:\Bakk\osmosis-latest\bin\australia-oceania-latest.osm.pbf" --bounding-polygon file="D:\Bakk\osmosis-latest\bin\country.poly" --write-xml file="D:\Bakk\osmosis-latest\bin\australia.osm" 2>&1');  
	
		//echo "$befehl_overpass";
		
		
$befehl_convert_to_o5m = sprintf(
	'%s %s -o=%s',
	escapeshellarg($dir_convert_filter . '/osmconvert'),
	escapeshellarg($dir_daten . '/output_overpass.osm.pbf'),
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

$output_convert = shell_exec("$befehl_filter $rueck");
		//$output = shell_exec($befehl);  
		//echo $befehl_filter;
		//echo "$output_convert";
	//}
	
$befehl1_ogr2ogr = sprintf(
	"%s par",
	escapeshellarg($dir_ogr2ogr . '/SDKShell.bat')
);
			
$befehl2_ogr2ogr = sprintf(
	"SET par=ogr2ogr -f \"ESRI Shapefile\" %s %s ".$shapes." --config OSM_USE_CUSTOM_INDEXING NO",
	escapeshellarg($dir_shape . "/shape"),
	escapeshellarg($dir_daten . '/output_filter.osm')
);
		
		//echo $befehl_ogr2ogr;
		//echo $output_ogr2ogr;


//$zip_shape = new ZipArchive();
//$filename = "$dir_daten/outputshape.zip";

//if ($zip_shape->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
//    exit("cannot open <$filename>\n");
//}

//$zip_osm = new ZipArchive();
//$filename = "$dir_daten/output.zip";

//if ($zip_osm->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
//    exit("cannot open <$filename>\n");
//}
$filename = "$dir_daten/output.zip";
$filename_s = "$dir_daten/output_shape.zip";
$zip = new ZipArchive();

switch ($typ_ausw) { // welche Downloadoption wurde gewählt
	
	case "shp":	
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$output_ogr2ogr = shell_exec("$befehl2_ogr2ogr && $befehl1_ogr2ogr");
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
		$output_ogr2ogr = shell_exec("$befehl2_ogr2ogr && $befehl1_ogr2ogr");
		zippen($dir_shape, $zip);
		$zip->close();
		header ("Location: download.php?loeschen=".$loeschen);			
		break;
}
?>
	
	
	<!--	
	<form action = "karte.html" method = "GET">
	Noch etwas exportieren?
	<input type="submit" value="OK"\>
	</form>
	<?php
//	if (file_exists("$dir_daten/output_osm.zip")) {
//	$zip_osm->open($filename_o);
//	$anzahl = $zip_osm->numFiles;
//	$filesize_o = filesize($filename_o);
//	echo "Anzahl .osm Daten: " . $anzahl . "\n";
//	echo "</br>Dateigr&#xF6;&#xDF;e: $filesize_o Byte"; 
//	$zip_osm->close();
	?>
	<form action = "download_osm.php" method = "GET">
	.osm Datei downloaden?
	<input type="submit" value="OK"\> <?php //} ?>
	</form>
	<?php
//	if (file_exists("$dir_daten/output_shape.zip")) {
//	$filesize_s = filesize($filename_s);
//	$anz_shapes = anz_ordner($dir_shape) - 2;
//	echo "Anzahl Shapes: " . $anz_shapes . "\n";
//	echo "</br>Dateigr&#xF6;&#xDF;e: $filesize_s Byte"; 
	
	?>
	<form action = "download_shape.php" method = "GET">
	.shp Datei downloaden?
	<input type="submit" value="OK"\> !-->
	<?php //}
	 // geschwungene Klammer für if empty (Koordinaten)?> 
	<!--</form> -->
	
	</body>	
</html>