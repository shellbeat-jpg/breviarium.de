<?php
# http://alt.breviarium.de/hidden/copy.php?&LIM=171450
$ID = $_GET['ID'];
$LIM= $_GET['LIM'];
if($LIM<1)$LIM=0;
if($ID<1)$ID=0;
include("includes/db.php");
include("includes/functions.php");
define('AWS_KEY', $arrAwsKeys[8]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[8]['AWS_SECRET_KEY']);

header("Content-Type: text/html; charset=utf-8");
$result = mysql_query("SELECT * FROM  buchfreund Limit 900, 2000");
while($result_download = mysql_fetch_array($result, MYSQL_ASSOC)){
	$title = utf8_encode($result_download['title']);
	#echo "<br>".$title."<br>";
	$title = preg_replace("/[^0-9a-zA-ZäÄüÜöÖ]/",' ',$title);
  $newTitle ="";
	$arrTemp   = explode(' ', $title);
	for($i=0; $i < count($arrTemp); $i++){
			if(strlen($arrTemp[$i])>3)
         $newTitle.=$arrTemp[$i]. " ";
	}
  $title=utf8_decode($newTitle);
  $SQL="UPDATE   buchfreund set titleNew = '$title' where id = " . $result_download['id'];
  mysql_query($SQL) or die($SQL);
	echo $title."<br>";
	
}
exit;


$Category = getCategory('3883759120');
var_dump($Category);
exit;

checkBuchfreundItems($LIM) ;

echo "<script>\n";
echo "document.location.href=\"http://buch.breviarium.de/hidden/copy.php?&LIM=".($LIM+10)."\"\n";
echo "</script>\n";
exit;

deleteDuplicatesAmazon($LIM);
echo "<script>\n";
echo "document.location.href=\"http://buch.breviarium.de/hidden/copy.php?&LIM=".($LIM+200)."\"\n";
echo "</script>\n";
exit;




$ID = removeEmpty($ID) ;

echo "<script>\n";
echo "document.location.href=\"http://buch.breviarium.de/hidden/copy.php?&ID=".$ID."\"\n";
echo "</script>\n";

exit;


addEurobuchListings($LIM);
echo "<script>\n";
echo "document.location.href=\"http://buch.breviarium.de/hidden/copy.php?&LIM=".($LIM+25)."\"\n";
echo "</script>\n";

exit;




exit;
updateProductsPrice() ;
exit;

deleteDuplicates($LIM);
echo "<script>\n";
echo "document.location.href=\"http://buch.breviarium.de/hidden/copy.php?&LIM=".($LIM+200)."\"\n";
echo "</script>\n";
exit;
$result = mysql_query("Select * from categories where parent_id = 0");

while($result_download = mysql_fetch_array($result, MYSQL_ASSOC)){
	echo "\$arrCategories[] = ".$result_download['categories_id'].";\n";
}

  exit;
	$i=0;
	$categories_products_query = mysql_query("Select * from amazonOffers");
	while($cateqories_products = mysql_fetch_array($categories_products_query, MYSQL_ASSOC)){
    $i++;
		mysql_query("Update amazonOffers set ConditionNote = '". str_replace("'", "\'", utf8_decode($cateqories_products['ConditionNote']))."'    where  id = " . $cateqories_products['id']) or die("$i - ".mysql_error());
	}
	exit;
	$i=0;
	$categories_products_query = mysql_query("Select * from amazonCreator");
	while($cateqories_products = mysql_fetch_array($categories_products_query, MYSQL_ASSOC)){
    $i++;
		mysql_query("Update amazonCreator set creator = '". str_replace("'", "\'", utf8_encode($cateqories_products['creator']))."'    where  id = " . $cateqories_products['id']) or die( "$i - ".mysql_error());
	}

  $i=0;
	$categories_products_query = mysql_query("Select * from products");
	while($cateqories_products = mysql_fetch_array($categories_products_query, MYSQL_ASSOC)){
    $i++;
		mysql_query("Update `products` set Author = '". str_replace("'", "\'", utf8_encode($cateqories_products['Author']))."', Title = '". str_replace("'", "\'", utf8_encode($cateqories_products['Title']))."', Publisher = '". str_replace("'", "\'", utf8_encode($cateqories_products['Publisher']))."',  Edition = '". str_replace("'", "\'", utf8_encode($cateqories_products['Edition']))."', Binding = '". str_replace("'", "\'", utf8_encode($cateqories_products['Binding']))."' where    products_id = " . $cateqories_products['products_id']) or die("$i - ".mysql_error());
	}

  $i=0;
  $categories_products_query = "select * from products_description";
	$categories_products_query = mysql_query($categories_products_query);
	while($cateqories_products = mysql_fetch_array($categories_products_query, MYSQL_ASSOC)){
     $i++;
		mysql_query("Update products_description set products_name = '". str_replace("'", "\'", utf8_encode($cateqories_products['products_name']))."' where products_id = '".$cateqories_products['products_id']."'") or die( "$i - ".mysql_error());
	}

  $i=0;
  $categories_products_query = "select * from categories_description";
	$categories_products_query = mysql_query($categories_products_query);
	while($cateqories_products = mysql_fetch_array($categories_products_query, MYSQL_ASSOC)){
    $i++;
		mysql_query("Update categories_description set categories_name = '".str_replace("'", "\'", utf8_encode($cateqories_products['categories_name']))."' where categories_id = '".$cateqories_products['categories_id']."'") or die( "$i - ".mysql_error());
	}

  exit;




$result = mysql_query("Select * from amazon where id < 20072 and id > $ID and SmallImage != '' order by id asc LIMIT 0, 25", $db_link);
while($result_download = mysql_fetch_array($result, MYSQL_ASSOC)){
  copy($result_download['SmallImage'], 'thumbnail_images/' . $result_download['id']."_0.jpg");
  copy($result_download['LargeImage'], 'popup_images/' . $result_download['id']."_0.jpg");
  $ID=  $result_download['id'];
  echo $ID." ";
}

echo "<script>\n";
echo "document.location.href=\"http://buch.breviarium.de/hidden/copy.php?&id=$ID\"\n";
echo "</script>\n";
?>

