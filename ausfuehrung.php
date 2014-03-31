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

function anz_ordner($source) {
$dirIter = new RecursiveDirectoryIterator($source);
$recursiveIterator = new RecursiveIteratorIterator($dirIter, 
                                //    RecursiveIteratorIterator::SELF_FIRST,
                                    RecursiveIteratorIterator::CATCH_GET_CHILD);

$counts = 0;
foreach($recursiveIterator as $element)
{
    /* @var $element SplFileInfo */
    switch($element->getType())
    {
        case 'dir':
            $counts++;
        break;
    }
}
return($counts);
}


	$lon1 = $_GET["lon1"];
	$lat1 = $_GET["lat1"];
	$lon2 = $_GET["lon2"];
	$lat2 = $_GET["lat2"];
	
	if (isset($_GET['tag'])) // welche Tags wurden ausgewählt
	{
		$tag = $_GET["tag"];
		$tag_ausw = implode(' ',$tag);
		$anz = count($tag);
	}
	
	if (isset($_GET['typ'])) // Export als Shape und/oder OSM
	{
		$typ = $_GET["typ"];
		$typ_ausw = implode(' ',$typ);
		$anz_typ = count($typ);
	}
	
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
				<th colspan="5">Bounding Box:</th>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td align="left" width="80">West:</td><td align="center" width="150"><?php echo $left; ?> &deg;</td><td width="80"></td><td align="left" width="80">Ost:</td><td align="center" width="150"><?php echo $right; ?> &deg;</td>
			</tr>
			<tr>
				<td align="left">Nord:</td><td align="center"><?php echo $top; ?> &deg;</td></td><td width="80"></td><td align="left">S&uuml;d:</td><td align="center"><?php echo $bot; ?> &deg;</td>
			</tr>
		</table>
		<?php
		
		$dir_osm = 'D:/Bakk/osmosis-latest/bin';
		$dir_daten = 'D:/Bakk/Daten';
		$dir_wget = 'D:/Bakk/GnuWin32/bin';
		$dir_convert_filter = 'D:/Bakk';
		$dir_ogr2ogr = 'D:\Bakk\ogr2ogr';
		$dir_shape = 'D:/Bakk/Daten/output_shape';

		$datei_handle=fopen($dir_daten."/overpass_api.xml","w+");
		fwrite($datei_handle,"<osm-script>\r\n<union>\r\n<bbox-query e=\"".$right."\" n=\"".$top."\" s=\"".$bot."\" w=\"".$left."\"/>\r\n<recurse type=\"up\"/><recurse type=\"down\"/>\r\n</union>\r\n<print mode=\"meta\" order=\"quadtile\"/>\r\n</osm-script>");
		fclose($datei_handle);
		
		
		//echo "<b>Bounding Box:<b> <br/> West: ".$left."\tOst: ".$right."<br/>Nord: ".$top."\tSüd: ".$bot; }
		set_time_limit(0); // ansonsten nach 30 Sekunden abbruch
		$rueck = " 2>&1"; // Rückgabewert der Tools umleiten

		$befehl_overpass = sprintf(
			'%s www.overpass-api.de/api/interpreter --ignore-length --post-file=%s -O %s',
			escapeshellarg($dir_wget . '/wget'),
			escapeshellarg($dir_daten . '/overpass_api.xml'),
			escapeshellarg($dir_daten . '/output_overpass.osm.pbf')
			);
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
		$output_overpass = shell_exec($befehl_overpass);  // Export mittels wget/Overpass-AP		
		//echo "$befehl_overpass";
		echo "$output_overpass";
		
		$befehl_convert_to_o5m = sprintf(
			'%s %s -o=%s',
			escapeshellarg($dir_convert_filter . '/osmconvert'),
			escapeshellarg($dir_daten . '/output_overpass.osm.pbf'),
			escapeshellarg($dir_daten . '/output_convert.o5m')
			//escapeshellarg($dir_daten . '/output_convert.osm')
			);
		$output_convert = shell_exec("$befehl_convert_to_o5m");
		echo "$output_convert";
	if (isset($tag))
		{
			$befehl_filter = sprintf(
			"%s %s --keep=\"$tag_ausw\" -o=%s",
			escapeshellarg($dir_convert_filter . '/osmfilter'),
			escapeshellarg($dir_daten . '/output_convert.o5m'),
			escapeshellarg($dir_daten . '/output_filter.osm')
			);
		}
		else
		{
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
	}
	
			$befehl1_ogr2ogr = sprintf(
			"%s par",
			escapeshellarg($dir_ogr2ogr . '/SDKShell.bat')
			);
			
			$anz_shapes = anz_ordner($dir_shape) - 1; // wie viele Shapes-Ordner wurden bereits erstellt
			
			$befehl2_ogr2ogr = sprintf(
			"SET par=ogr2ogr -f \"ESRI Shapefile\" %s %s multipolygons lines points --config OSM_USE_CUSTOM_INDEXING NO",
			escapeshellarg($dir_shape . "/shape".$anz_shapes),
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
$filename_o = "$dir_daten/output_osm.zip";
$filename_s = "$dir_daten/output_shape.zip";
$zip_osm = new ZipArchive();
$zip_shape = new ZipArchive();

	if (isset($typ_ausw)) {
		switch ($typ_ausw) {
		case "shp":	

			if ($zip_shape->open($filename_s, ZIPARCHIVE::CREATE)!==TRUE) {
				exit("cannot open <$filename_s>\n");
			}
			$output_ogr2ogr = shell_exec("$befehl2_ogr2ogr && $befehl1_ogr2ogr");
			zippen($dir_shape, $zip_shape);
			$zip_shape->close();
		break;
		
		case "osm":
			

			if ($zip_osm->open($filename_o, ZIPARCHIVE::CREATE)!==TRUE) {
				exit("cannot open <$filename_o>\n");
			}
		
			$anzahl = $zip_osm->numFiles + 1;
			$zip_osm->addFile($dir_daten . "/output_filter.osm","output_filter".$anzahl.".osm");
			$zip_osm->close();
		break;
		
		case "shp osm";

			if ($zip_osm->open($filename_o, ZIPARCHIVE::CREATE)!==TRUE) {
				exit("cannot open <$filename_o>\n");
			}

			if ($zip_shape->open($filename_s, ZIPARCHIVE::CREATE)!==TRUE) {
				exit("cannot open <$filename_s>\n");
			}
			$anzahl = $zip_osm->numFiles  + 1;
			$zip_osm->addFile($dir_daten . "/output_filter.osm","output_filter".$anzahl.".osm");
			$output_ogr2ogr = shell_exec("$befehl2_ogr2ogr && $befehl1_ogr2ogr");
			zippen($dir_shape, $zip_shape);
			$zip_osm->close();
			$zip_shape->close();
		break;
		}
	}
 	else {echo "keine Downloadoption gew&#xE4;hlt";}



//$dateiname = "/output.zip"; 

	
	
	?>		
	<form action = "karte.html" method = "GET">
	Noch etwas exportieren?
	<input type="submit" value="OK"\>
	</form>
	<?php
	if (file_exists("$dir_daten/output_osm.zip")) {
	$zip_osm->open($filename_o);
	$anzahl = $zip_osm->numFiles;
	$filesize_o = filesize($filename_o);
	echo "Anzahl .osm Daten: " . $anzahl . "\n";
	echo "</br>Dateigr&#xF6;&#xDF;e: $filesize_o Byte"; 
	$zip_osm->close();
	?>
	<form action = "download_osm.php" method = "GET">
	.osm Datei downloaden?
	<input type="submit" value="OK"\> <?php } ?>
	</form>
	<?php
	if (file_exists("$dir_daten/output_shape.zip")) {
	$filesize_s = filesize($filename_s);
	$anz_shapes = anz_ordner($dir_shape) - 2;
	echo "Anzahl Shapes: " . $anz_shapes . "\n";
	echo "</br>Dateigr&#xF6;&#xDF;e: $filesize_s Byte"; 
	
	?>
	<form action = "download_shape.php" method = "GET">
	.shp Datei downloaden?
	<input type="submit" value="OK"\> <?php } ?>
	</form>
	
	</body>	
</html>