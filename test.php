<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
Test
</head>
<body>

<?php
$dir_daten = 'D:/Bakk/Daten';
$zip_osm = "output_osm.zip";
$zip_shape = "output_shape.zip";
$pfad_osm = "$dir_daten/$zip_osm";
$pfad_shape = "$dir_daten/$zip_shape";
if (file_exists($pfad_osm) && file_exists($pfad_shape)) {
        header("Content-Disposition: attachment; filename=\"". urlencode($zip_osm) ."\"");
	readfile($pfad_osm);
	unlink(realpath($pfad_osm));
	header("Content-Disposition: attachment; filename=\"". urlencode($zip_shape) ."\"");
	readfile($pfad_shape);
	unlink(realpath($pfad_shape));
} else {
    echo "Die Datei existiert nicht";
}

?>

</body>
</html>