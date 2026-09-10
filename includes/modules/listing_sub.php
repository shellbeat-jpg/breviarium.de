<?php
	if($where_str==''){
	//where-string
	$where_str = " WHERE p.products_status = '1' ".$subcat_where.$fsk_lock.$manu_check.$group_check.$tax_where.$pfrom_check.$pto_check.$date_added_check;

	//go for keywords... this is the main search process
	if (isset ($_GET['keywords']) && xtc_not_null($_GET['keywords'])) {
	$where_str .= " AND ";
	$HEADING_TITLE = "Suchergebnis für: \"".$_GET['keywords']."\"";
		if (xtc_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
			$where_str .= "   ( ";
			for ($i = 0, $n = sizeof($search_keywords); $i < $n; $i ++) {
				switch ($search_keywords[$i]) {
					case '(' :
					case ')' :
					case 'and' :
					case 'or' :
						$where_str .= " ".$search_keywords[$i]." ";
						break;
					default :


					// Wurde nach Umlauten gesucht?
          $ent_keyword = htmlentities($search_keywords[$i]);
          $ent_keyword = ($ent_keyword != $search_keywords[$i]) ? addslashes($ent_keyword) : false;

          // addslashes langt einmal ...
          $keyword = addslashes($search_keywords[$i]);

          $where_str .= "   ( ";
          $where_str .= "  p.Title LIKE ('%".$keyword."%') ";
          $where_str .= "OR p.Author LIKE ('%".$keyword."%') ";
          $where_str .= "OR p.EditorialReview LIKE ('%".$keyword."%') ";
          $where_str .= "OR p.Publisher LIKE ('%".$keyword."%') ";
          if(strlen($keyword)>=10)
          	$where_str .= "OR p.ISBN LIKE ('%".trim($keyword)."%') ";


					// EOF - Dokuman - 2009-05-27 - search for umlaut letters

						$where_str .= " ) ";
						break;
				}
			}
			$where_str .= " )   ";
		}
	}
	}

?>