<?php
include("includes/db.php");
include("includes/functions.php");
$LIM= $_GET['LIM'];
if($LIM<1){
	$LIM=0;
	mysql_query("Delete from products_xsell");
}

		/*
$SQL_AUTHORS = mysql_query("SELECT DISTINCT id, count( id_amazon ) AS total, role, creator
FROM `amazonCreator`
GROUP BY role, creator
ORDER BY total desc") or die(mysql_error());

while ($AUTHORS = mysql_fetch_array($SQL_AUTHORS, MYSQL_ASSOC)) {
	if($AUTHORS['total']>1) {
     $SQL = "UPDATE amazonCreator set total = '".$AUTHORS['total']."' where creator= '".addslashes($AUTHORS['creator'])."' and role = '".$AUTHORS['role']."'";
     $SQL_PRODUCTS = mysql_query($SQL)or die($SQL."<br>".mysql_error());
	}

}
exit;
	*/

$SQL_AUTHORS = mysql_query("SELECT DISTINCT `Author` , count( products_id ) AS total
FROM `products`
WHERE Author != '' and Author != 'unbekannt' and Author != 'Zeitschriften.' and Author != '(o.A.)'
and CHAR_LENGTH(`Author`) > 4
GROUP BY Author
ORDER BY `Author` ASC LIMIT $LIM, 1000") or die(mysql_error());

if(! mysql_num_rows($SQL_AUTHORS))
	exit;
	
while ($AUTHORS = mysql_fetch_array($SQL_AUTHORS, MYSQL_ASSOC)) {
	 if($AUTHORS['total']>1){
     $AUTHOR=str_replace("'", "\'", $AUTHORS['Author']);
		 if(strlen($AUTHOR)>6){
		  $SQL_PRODUCTS = mysql_query("SELECT products_id FROM `products` WHERE Author = '$AUTHOR'")or die(mysql_error());
			$arrTemp=array();
			while ($PRODUCTS = mysql_fetch_array($SQL_PRODUCTS, MYSQL_ASSOC)) {
         $products_id = $PRODUCTS['products_id'];
         $arrTemp[]=$products_id;
				 # echo $AUTHORS['Author'] . "<br>";
      }
      for ($i = 0; $i < count($arrTemp); $i ++) {
	      for ($j = 0; $j < count($arrTemp); $j ++) {
				 if($arrTemp[$i] != $arrTemp[$j]){
				    mysql_query("Insert into products_xsell (products_id, products_xsell_grp_name_id, xsell_id) values(".$arrTemp[$i].", 1, ".$arrTemp[$j].")");
            #echo "$i-$j " . $arrTemp[$i] . " : ".$arrTemp[$j]."<br>";
				 }
	      }
      }
		 }
	 }
}
header("Location:http://buch.breviarium.de/hidden/makeXselling.php?&LIM=".($LIM+1000));
#header("refresh:0,url=http://buch.breviarium.de/hidden/makeXselling.php?&LIM=".($LIM+1000));
?>
