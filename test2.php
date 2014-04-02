<html><head><title>Test</title>
<script type="text/javascript">
var Start = new Date();
var Startzeit = Start.getTime();

function Aufenthalt () {
  var Ende = new Date();
  var Endzeit = Ende.getTime();
  var Aufenthalt = Math.floor((Endzeit - Startzeit) / 1000);
  alert("Sie waren " + Aufenthalt + " Sekunden auf dieser Seite");
}
function test () {
window.location.href = "test.php";
};

//window.addEventListener("beforeunload", function( event ) {
//window.location.href = "test.php";
//event.returnValue = "Test";
//});

//window.onbeforeunload = function() {
//	window.location.href = "test.php";
//    return 'You have unsaved changes!';	
//}
</script>
</head>
<body>

<?php
// Ignoriere Abbruch durch den Benutzer und erlaube dem Skript weiterzulaufen
ignore_user_abort(true);
set_time_limit(0);

echo 'Teste Connectionhandling in PHP';

// Lasse eine sinnfreie Schleife laufen, die uns irgendwann
// hoffentlich von der Seite wegklicken oder den "Stop"-Button
// betätigen lässt


// Wird dieser Punkt erreicht, wurde das 'break'
// von einem Punkt innerhalb der while-Schleife getriggert

// Somit können wir hier ein Log schreiben oder andere Aufgaben
// ausführen, die nicht davon abhängig sind, ob der Browser des
// Benutzers noch eine stehende Verbindung zum Server hat

while(1)
{
  // Schlug die Verbindung fehl?
  if(connection_status() != CONNECTION_NORMAL)
  {
  $dir_daten = 'D:/Bakk/Daten';
$zip_osm = "output_osm.zip";
$zip_shape = "output_shape.zip";
$pfad_osm = "$dir_daten/$zip_osm";
$verz = "$dir_daten/output_shape/shape6/";

$handle = opendir($verz);
if($handle)
{
    while ( false !== ($file = readdir($handle)) )
    {
        if ( $file != "." and $file != ".." )
        {
            unlink($verz.$file);
        }
    }
}
rmdir($verz);
    break;
  }

  // 10 Sekunden warten
  sleep(10);
}

?>

<a href="javascript:test()">Box</a><br>
<?
ignore_user_abort(true);
set_time_limit(0);
while(1)
{
  // Schlug die Verbindung fehl?
  if(connection_status() == CONNECTION_NORMAL)
  {
  $dir_daten = 'D:/Bakk/Daten';
$zip_osm = "output_osm.zip";
$zip_shape = "output_shape.zip";
$pfad_osm = "$dir_daten/$zip_osm";
$verz = "$dir_daten/output_shape/shape5/";

$handle = opendir($verz);
if($handle)
{
    while ( false !== ($file = readdir($handle)) )
    {
        if ( $file != "." and $file != ".." )
        {
            unlink($verz.$file);
        }
    }
}
rmdir($verz);
    break;
  }

  // 10 Sekunden warten
  //sleep(10);
}
?>
</body>
</html>