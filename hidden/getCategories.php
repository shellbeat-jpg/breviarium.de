<?php
include("includes/db.php");
include("includes/functions.php");
$key=0;

define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);

$params = array("Operation"=>"BrowseNodeLookup",  "BrowseNodeId"=>"541686",  "ResponseGroup"=>"BrowseNodeInfo","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
$Nodes=$pxml->BrowseNodes->BrowseNode->Children->BrowseNode;
$parent=0;
$g=0;
for($i=0; $i < count($Nodes); $i++){
	 $g++;
	 echo $g." ".$Nodes[$i]->BrowseNodeId   .":".$Nodes[$i]->Name    ."<br>";
	 $categories_id=$Nodes[$i]->BrowseNodeId;
	 $categories_name=utf8_decode($Nodes[$i]->Name);

			 $check = mysql_query("select * from categories where categories_id = '$categories_id' and parent_id = '0'");
			 if(mysql_num_rows($check)<1){
          echo "select * from categories where categories_id = '$categories_id' and parent_id = '0';<br>";
          echo "$g - $categories_id: $categories_name<br>";
			 }

	 $params = array("Operation"=>"BrowseNodeLookup",  "BrowseNodeId"=>$categories_id,  "ResponseGroup"=>"BrowseNodeInfo","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
	 $pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
	 $Nodes2=$pxml->BrowseNodes->BrowseNode->Children->BrowseNode;
	 
			 if(189528 == $categories_id)
			    var_dump($pxml->BrowseNodes->BrowseNode);
	 
	 for($j=0; $j < count($Nodes2); $j++){
       $g++;
       #echo $g." ".$Nodes2[$i]->BrowseNodeId   .":".$Nodes2[$i]->Name    ."<br>";

			 $categories_id2=$Nodes2[$j]->BrowseNodeId;
			 $categories_name2=utf8_decode($Nodes2[$j]->Name);
			 
			 $check = mysql_query("select * from categories where categories_id = '$categories_id2' and parent_id = '$categories_id'");
			 if(mysql_num_rows($check)<1){
          echo "$g - $categories_id2: $categories_name2<br>";
			 }
			 #mysql_query("Insert into categories (categories_id, parent_id, sort_order 	) values($categories_id2, $categories_id, $j)", $db_link);
			 #mysql_query("Insert into categories_description (categories_id, 	language_id, 	categories_name) values($categories_id2, 1, '$categories_name2')", $db_link);
		}
}
?>