<?php
$ID = $_GET['ID'];
if($ID<1)$ID=0;

include("includes/db.php");
include("includes/functions.php");

$ID = removeEmpty($ID) ;

echo "<script>\n";
echo "document.location.href=\"http://buch.breviarium.de/hidden/removeEmpty.php?&ID=".$ID."\"\n";
echo "</script>\n";



?>

