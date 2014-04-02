<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="content-language" content="de" />
Test
</head>
<body onbeforeunload="javascript:alert (Die Positionsbestimmung ist Fehlgeschlagen!);"> 

<?php

if (isset($_GET["loeschen"]))
{echo $_GET["loeschen"];}
else
{echo "Du Idiot";}


?>

</body>
</html>