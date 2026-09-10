<?php

/* -----------------------------------------------------------------------------------------
  $Id: default.php 1292 2005-10-07 16:10:55Z mz $

  XT-Commerce - community made shopping
  http://www.xt-commerce.com

  Copyright © 2003 XT-Commerce
  -----------------------------------------------------------------------------------------
  based on:
  © 2000-2001 The Exchange Project  (earlier name of osCommerce)
  © 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
  © 2003  nextcommerce (default.php,v 1.11 2003/08/22); www.nextcommerce.org

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        Autor: Mikel Williams | mikel@ladykatcostumes.com
  Customers Status v3.x  © 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs...by=date#dirlist

  Released under the GNU General Public License
  ---------------------------------------------------------------------------------------*/

$default_smarty = new smarty;
$default_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
$default_smarty->assign('session', session_id());
$main_content = '';
// include needed functions
require_once (DIR_FS_INC.'xtc_customer_greeting.inc.php');
require_once (DIR_FS_INC.'xtc_get_path.inc.php');
require_once (DIR_FS_INC.'xtc_check_categories_status.inc.php');

//BOF - Dokuman - 2009-10-02 - removed feature, due to wrong links in category on "last viewed"  
//$_SESSION['lastpath'] = $_GET['cPath'];
//EOF - Dokuman - 2009-10-02 - removed feature, due to wrong links in category on "last viewed"  

#if (xtc_check_categories_status($current_category_id) >= 1) {
if (1 > 1) {
$error = CATEGORIE_NOT_FOUND;
include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);

} else {

if ($category_depth == 'nested') {
  if (GROUP_CHECK == 'true') {
  $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
  }
  $category_query = "select cd.categories_description,
                            cd.categories_name,
                            cd.categories_heading_title,       
                            c.categories_template,
                            c.categories_image from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                            where c.categories_id = '".$current_category_id."'
                            and cd.categories_id = '".$current_category_id."'
                            ".$group_check."
                            and cd.language_id = '".(int) $_SESSION['languages_id']."'";


	$category_query = xtDBquery($category_query);

  $category = xtc_db_fetch_array($category_query, true);

  if (isset ($cPath) && preg_match('/_/', $cPath)) { // Hetfield - 2009-08-19 - replaced deprecated function ereg with preg_match to be ready for PHP >= 5.3
  // check to see if there are deeper categories within the current category
  $category_links = array_reverse($cPath_array);
  for ($i = 0, $n = sizeof($category_links); $i < $n; $i ++) {
    if (GROUP_CHECK == 'true') {
    $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }
    $categories_query = "select cd.categories_description,
                                c.categories_id,
                                cd.categories_name,
                                cd.categories_heading_title,
                                c.categories_image,
                                c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                                where c.categories_status = '1'
                                and c.parent_id = '".$category_links[$i]."'
                                and c.categories_id = cd.categories_id
                                ".$group_check."
                                and cd.language_id = '".(int) $_SESSION['languages_id']."'
                                order by sort_order, cd.categories_name";

		$categories_query = xtDBquery($categories_query);

// BOF - Dokuman - 22.07.2009 - avoid else-condition

    if ( xtc_db_num_rows($categories_query, true) >= 1 ) {
      break; // we've found the deepest category the customer is in
    }
// EOF - Dokuman - 22.07.2009 - avoid else-condition
    
  }
  } else {
  if (GROUP_CHECK == 'true') {
    $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
  }
  $categories_query = "select cd.categories_description,
                              c.categories_id,
                              cd.categories_name,
                              cd.categories_heading_title,
                              c.categories_image,
                              c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                              where c.categories_status = '1'
                              and c.parent_id = '".$current_category_id."'
                              and c.categories_id = cd.categories_id
                              ".$group_check."
                              and cd.language_id = '".(int) $_SESSION['languages_id']."'
                              order by sort_order, cd.categories_name";

	$categories_query = xtDBquery($categories_query);
  }

  $rows = 0;
  while ($categories = xtc_db_fetch_array($categories_query, true)) {
  $rows ++;
 
  $cPath_new = xtc_category_link($categories['categories_id'],$categories['categories_name']);
 
  $width = (int) (100 / MAX_DISPLAY_CATEGORIES_PER_ROW).'%';
  $image = '';
  if ($categories['categories_image'] != '') {
    $image = DIR_WS_IMAGES.'categories/'.$categories['categories_image'];
// BOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
    if(!file_exists($image)) $image = DIR_WS_IMAGES.'categories/noimage.gif';
// EOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
  }

  $categories_content[] = array ('CATEGORIES_NAME' => $categories['categories_name'], 
                                 'CATEGORIES_HEADING_TITLE' => $categories['categories_heading_title'],
                                 'CATEGORIES_IMAGE' => $image,
                                 'CATEGORIES_LINK' => xtc_href_link(FILENAME_DEFAULT, $cPath_new), 
                                 'CATEGORIES_DESCRIPTION' => $categories['categories_description']);
  }
  $new_products_category_id = $current_category_id;
  #include (DIR_WS_MODULES.FILENAME_NEW_PRODUCTS);

  $image = '';
  if ($category['categories_image'] != '') {
  $image = DIR_WS_IMAGES.'categories/'.$category['categories_image'];
  }
  $default_smarty->assign('CATEGORIES_NAME', $category['categories_name']);
  $default_smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);

  $default_smarty->assign('CATEGORIES_IMAGE', $image);
  $default_smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);

  $default_smarty->assign('language', $_SESSION['language']);
  $default_smarty->assign('module_content', $categories_content);

  // get default template
  if ($category['categories_template'] == '' || $category['categories_template'] == 'default') {
	$files = array ();
	if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/')) {
		while (($file = readdir($dir)) !== false) {
// BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
			//if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
			if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
// EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
// BOF - web28 - 2010-07-12 - sort templates array
				//$files[] = array ('id' => $file, 'text' => $file);
				$files[] = $file;
			} //if
		} // while
		closedir($dir);
	}		
	sort($files);      
	//$category['categories_template'] = $files[0]['id'];
	$category['categories_template'] = $files[0];
// EOF - web28 - 2010-07-12 - sort templates array
  }

  $default_smarty->caching = 0;
  $main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/categorie_listing/'.$category['categories_template']);
  $smarty->assign('main_content', $main_content);

}
//elseif ($category_depth == 'products' || $_GET['manufacturers_id']) {
elseif ($category_depth == 'products' || isset($_GET['list']) || (isset($_GET['illustrator']) && $_GET['illustrator'] > 0) || (isset($_GET['foreword']) && $_GET['foreword'] > 0) || (isset($_GET['photographer']) && $_GET['photographer'] > 0)) { //DokuMan - 2010-02-26 - Undefined index: manufacturers_id
		switch ((int)$_GET['sorting_id']) {
		case 1:
		$sorting=' ORDER BY p.title ASC';
		break;
		case 2:
		$sorting=' ORDER BY p.title DESC';
		break;
		case 3:
		$sorting=' ORDER BY p.products_price ASC';
		break;
		case 4:
		$sorting=' ORDER BY p.products_price DESC';
		break;
		case 5:
		$sorting=' ORDER BY p.products_id DESC';
		break;
		}
		if(!isset($sorting) && !isset ($_GET['list']))
       $sorting = ' ORDER BY p.products_id ASC';
  // show the products of a specified manufacturer


  	if (isset ($_GET['list'])) {


			if (isset ($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {

		 	if (!$sorting_data['products_sorting'])
		    $sorting_data['products_sorting'] = 'p.products_last_modified desc';
		    #$sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
		    // We are asked to show only a specific category
		    if (GROUP_CHECK == 'true') {
		    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		    }

		    $listing_sql = "select DISTINCT p.*, p.title   as products_name
		                                  from    ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p
		                                  where p.products_status = '1'
																			and p.products_id = p2c.products_id
		                                  ".$group_check."
		                                  ".$fsk_lock."
		                                  and p2c.categories_id = '".(int) $_GET['filter_id']."'".$sorting;
		//EOF - DokuMan - remove unneeded "left join ".TABLE_SPECIALS." from SELECT

		  } else {

		    // We show them all
		    if (GROUP_CHECK == 'true') {
		    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		    }


				if(isset($_GET['author'])){
				$_GET['author']     = str_replace("'", "\'",$_GET['author']);

		    $listing_sql = "select DISTINCT p.*
		                             from ".TABLE_PRODUCTS." p
		                            where p.products_status = '1'
		                            and Author  LIKE '% ".$_GET['author']."'
																order by Title
																".$group_check."
		                            ".$fsk_lock;
		                            
                $HEADING_TITLE = "Autor: ".$_GET['author'];
                $HEADING_TITLE = stripslashes($HEADING_TITLE);


				}else{
				
				if(!isset($sorting))
				 $sorting  = "order by TRIM(SUBSTRING(Author, INSTR(Author, ' ')))";
		    $listing_sql = "select DISTINCT p.*  from  ".TABLE_PRODUCTS." p
		                            where p.products_status = '1'
		                            and TRIM(SUBSTRING(Author, INSTR(Author, ' '))) LIKE '".$_GET['list']."%'
                                $sorting
																".$group_check."
		                            ".$fsk_lock;


           $HEADING_TITLE = "Autoren: ".strtoupper($_GET['list']);
				}
		                            

		  }
      $authorlist_sql = "SELECT DISTINCT TRIM( SUBSTRING( Author, INSTR( Author, ' ' ) ) ) AS name
													FROM `products`
													WHERE TRIM( SUBSTRING( author, INSTR( Author, ' ' ) ) ) LIKE '".$_GET['list']."%'
													AND products_status > 0
													ORDER BY name";
													
			$authorlist_query = xtDBquery($authorlist_sql);
			if (xtc_db_num_rows($authorlist_query, true) > 1) {
			    $author_dropdown = xtc_draw_form('list', FILENAME_DEFAULT, 'get');


				    $authors[] = array ('text' => TEXT_ALL_MANUFACTURERS .": ".$_GET['list']);
			    
			    
					while ($authorlist = xtc_db_fetch_array($authorlist_query, true)) {
			    	$authors[] = array ('id' => str_replace(" ", "%20", $authorlist['name']), 'text' => $authorlist['name']);
			    }
			    $author_dropdown .= xtc_draw_pull_down_menu('author', $authors, '', ' style="width:382px" onchange="document.location.href=\'/'.$_GET['list'].'/Autor:_:\'+this.value+\'.html\'"');
          $author_dropdown .= xtc_draw_hidden_field('list', $_GET['list']);


          
          
					$author_dropdown .= '</form>'."\n";
			}






		}elseif (isset ($_GET['illustrator']) || isset ($_GET['photographer']) || isset ($_GET['foreword'])) {

		  if (isset ($_GET['illustrator'])) {
				$idRole= $_GET['illustrator']  ;
				$ROLE  = 'Illustrator: '    ;
			}elseif (isset ($_GET['photographer'])) {
			  $idRole= $_GET['photographer']  ;
			  $ROLE  = 'Fotograf: '    ;
		  }elseif (isset ($_GET['foreword'])) {
		    $idRole= $_GET['foreword']  ;
		    $ROLE  = 'Einleitung, Vorwort oder Nachwort: '    ;
			}

			if (isset ($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {

								 if (!$sorting_data['products_sorting'])
		    $sorting_data['products_sorting'] = 'p.products_last_modified desc';
		     // We are asked to show only a specific category
		    if (GROUP_CHECK == 'true') {
		    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		    }

		    $listing_sql = "select DISTINCT p.*, p.title   as products_name

		                                  from    ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p
		                                  where p.products_status = '1'


		                                  and p.products_id = p2c.products_id

		                                  ".$group_check."
		                                  ".$fsk_lock."

		                                  and p2c.categories_id = '".(int) $_GET['filter_id']."'".$sorting;
		//EOF - DokuMan - remove unneeded "left join ".TABLE_SPECIALS." from SELECT

		  } else {
		    // We show them all
		    if (GROUP_CHECK == 'true') {
		    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		    }
				$creator = getRoleCreator((int) $idRole)    ;
		    $listing_sql = "select DISTINCT p.*, c.*
		                             from amazonCreator c, ".TABLE_PRODUCTS." p
		                            where p.products_status = '1'
		                            and c.id_amazon = p.products_id
		                            and c.creator = '". xtc_db_input($creator) ."'
		                            ".$group_check."
		                            ".$fsk_lock.$sorting;
		                            

   			$HEADING_TITLE = $ROLE.$creator;
		//EOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT

		  }
  } else {
  // show the products in a given categorie
  if (isset ($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {


		 if (!$sorting_data['products_sorting'])
    $sorting_data['products_sorting'] = 'p.products_last_modified desc';

    // We are asked to show only specific catgeory
    if (GROUP_CHECK == 'true') {
    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }

    $listing_sql = "select p.*, p.title as   products_name

                               ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p
                                  where p.products_status = '1'


                                  and p.products_id = p2c.products_id
                                  and pd.products_id = p2c.products_id
                                  ".$group_check."
                                  ".$fsk_lock."

                                  and p2c.categories_id = '".$current_category_id."'".$sorting;
                                  
          
  } else {
    require_once (DIR_FS_INC.'xtc_parse_search_string.inc.php');
    include (DIR_WS_MODULES.'listing_sub.php');
		if (!$sorting_data['products_sorting'])
    $sorting_data['products_sorting'] = 'p.products_last_modified desc';
    #$sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
    // We show them all
    if (GROUP_CHECK == 'true') {
    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }

    $listing_sql = "select  p.*, p.title   as products_name
                                  from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c
                                  where p.products_status = '1'
                                  and p.products_id = p2c.products_id
                                  ".$group_check."
                                  ".$fsk_lock."
                                  and p2c.categories_id = '".$current_category_id."'".$sorting;
                                  
    if($where_str!='')
      $listing_sql = str_replace("where", $where_str." AND ", $listing_sql);



  }
  }


  if (PRODUCT_LIST_FILTER == 'true') {
	if ( !isset ($_GET['author']) &&  !isset ($_GET['illustrator']) && !isset ($_GET['photographer']) && !isset ($_GET['foreword'])) {

		 $sorting_dropdown = xtc_draw_form('sorting', FILENAME_DEFAULT, 'GET')  ;
			 if (isset($cPath))
			 	$sorting_dropdown.= xtc_draw_hidden_field('cPath', $cPath);
				 if (isset($cPath))
	     $sorting_dropdown.= "Suche nach: " . xtc_draw_input_field('keywords', '', 'style="width: 300px"')."&nbsp;&nbsp;";

			 	
			 if (isset($_GET['list']))
			 	$sorting_dropdown.= xtc_draw_hidden_field('list', $cPath);

			$options_sort = array(array('text' => 'Sortierung w&auml;hlen'));
				$options_sort[] = array('id' => '1', 'text' => 'Titel aufsteigend');
				$options_sort[] = array('id' => '2', 'text' => 'Titel absteigend');
				$options_sort[] = array('id' => '3', 'text' => 'Preis - aufsteigend');
				$options_sort[] = array('id' => '4', 'text' => 'Preis - absteigend');
		  	$options_sort[] = array('id' => '5', 'text' => 'Erfassung - absteigend');

				$sorting_dropdown.= xtc_draw_pull_down_menu('sorting_id', $options_sort, $_GET['sorting_id'], 'onchange="this.form.submit()" style="width:140px"');
		 	$sorting_dropdown.= '</form>';

      }
  }

  // Get the right image for the top-right
  $image = DIR_WS_IMAGES.'table_background_list.gif';
     #   if($_SESSION['customer_id']=='1')
  # $listing_sql;

  include (DIR_WS_MODULES.FILENAME_PRODUCT_LISTING);

} else {

  if (GROUP_CHECK == 'true') {
  $group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
  }

  $shop_content_query = xtDBquery("SELECT content_title,
                                          content_heading,
                                          content_text,
                                          content_file
                                          FROM ".TABLE_CONTENT_MANAGER."
                                          WHERE content_group='5'
                                          ".$group_check."
                                          AND languages_id='".$_SESSION['languages_id']."'");
  $shop_content_data = xtc_db_fetch_array($shop_content_query,true);

// BOF - Dokuman - 22.07.2009 - added htmlspecialchars
//  $default_smarty->assign('title', $shop_content_data['content_heading']);
  $default_smarty -> assign('title', htmlspecialchars($shop_content_data['content_heading']));
// EOF - Dokuman - 22.07.2009 - added htmlspecialchars
  # echo '$shop_content_data[\'content_file\']: '. $shop_content_data['content_file'] ;
  include (DIR_WS_INCLUDES.FILENAME_CENTER_MODULES);
 #  echo '$shop_content_data[\'content_file\']: '. $shop_content_data['content_file'] ;
  if ($shop_content_data['content_file'] != '') {
  ob_start();
  if (strpos($shop_content_data['content_file'], '.txt')) {
    echo '<pre>';
  }
  include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
  if (strpos($shop_content_data['content_file'], '.txt')){
    echo '</pre>';
  }    
  $shop_content_data['content_text'] = ob_get_contents();
  ob_end_clean();
  }
  

  $DATUM = date("d.m.Y");
	$ALLE = $product_query = xtc_db_query("Select count(*) as total from products where products_status > 0");
  $ALLE = xtc_db_fetch_array($ALLE);
  $ALLE = $ALLE['total'];
  
if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0') {
	$date_new_products = date("Y.m.d", mktime(1, 1, 1, date("m"), date("d") - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date("Y")));
	$days = "   products_date_added > '".$date_new_products."' ";
}
	$HEUTE = $product_query = xtc_db_query("Select count(*) as total from products where $days and  products_status = 1");
  $HEUTE = xtc_db_fetch_array($HEUTE);
  $HEUTE = $HEUTE['total'];
	#
	#echo $HEUTE ;
	  $shop_content_data['content_text'] =  '<table   cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <tr>

            <td colspan="2">
            <p>Sie finden hier aktuelle Angebote vergriffener B&uuml;cher mit Abbildung aus dem  Amazon-Katalog. Alternativ sind neben der Detailansicht eines Buches  vergleichbare oder g&uuml;nstigere Anbieter von folgenden Buchplattformen  gelistet: Abebooks, Alibris, Antbo, antikbuch24, Antiquario, AuM.at,  Auxion, biblio, Booklooker, buch.de, buch24.de, buchfreund,  English-Book-Service, guthschrift, hitmeister, jokers,libri, prolibri,  SFBasar, zeilenreich, Zvab.</p>
            <p>Wenn Sie in der Detailansicht eines Titels Ihre Email-Adresse  hinterlassen werden Sie benachrichtigt sobald ein g&uuml;nstigeres Angebot  f&uuml;r diesen Titel vorliegt.</p>

            </td>
        </tr>
        <tr>
            <td>
            Gesamtbestand am %s: %s</td>
            <td>&nbsp;</td>
        </tr>

    </tbody>
</table>';
# &nbsp;&nbsp; Heute erstmals aufgenommen: %s
#  error_reporting(E_ALL);
	 #$shop_content_data['content_text'] =  str_replace('%s', "XX", $shop_content_data['content_text']) ;
     $shop_content_data['content_text'] = sprintf($shop_content_data['content_text'],   $DATUM, $ALLE, $HEUTE);
	  #echo  printf($shop_content_data['content_text'], "X", "Y", "Z", "Z");

	# echo $DATUM, $ALLE, $HEUTE ;
	$default_smarty->assign('text', str_replace('{$greeting}', xtc_customer_greeting(), $shop_content_data['content_text']));
  $default_smarty->assign('language', $_SESSION['language']);

  $default_smarty->caching = 0;
  $main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html');

 
  	/*
  // set cache ID   isset($DATUM) ||
  if (!CacheCheck()) {
  $default_smarty->caching = 0;
  $main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html');

  } else {

  $default_smarty->caching = 1;
  $default_smarty->cache_lifetime = CACHE_LIFETIME;
  $default_smarty->cache_modified_check = CACHE_CHECK;
  #$cache_id = $_SESSION['language'].$_SESSION['currency'].$_SESSION['customer_id'];
  $cache_id = $cPath.'_'. $_GET['author'].'_'.$_GET['list'].'_'.$_GET['illustrator'].'_'.$_GET['foreword'].'_'.$_GET['photographer'].'_'.$_GET['filter_id'].'_'.$_GET['page'].'_'.$_GET['keywords'].'_'.$_GET['categories_id'].'_'.$_GET['pfrom'].'_'.$_GET['pto'].'_'.$_GET['x'].'_'.$_GET['y'].'_'.$_GET['sorting_id'];
  $main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html', $cache_id);
  }
   */
  $smarty->assign('main_content', $main_content);
}
}
 
?>
