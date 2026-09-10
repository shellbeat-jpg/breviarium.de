<?php
/* -----------------------------------------------------------------------------------------
   $Id: fuzzy_search.php

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------
   
   3rd-party contribution: 'fuzzy search' by Thorsten Reineke (c) 2006 www.get-attention.de
   
   ---------------------------------------------------------------------------------------*/

  // wenn keywords aus Suche übergeben -> fuzzy search
if (($_GET['keywords']) && (SEARCH_ACTIVATE_SUGGEST == 'true')){

  require_once (DIR_FS_INC.'xtc_get_products_image.inc.php');
  require_once (DIR_WS_CLASSES.'fuzzy_search.php');

  $keywords = strtolower($_GET['keywords']);
	isset($_GET['spt'])?$SPT=$_GET['spt']:$SPT=SEARCH_PROXIMITY_TRIGGER;
  $Suggest = new FuzzySearch();
  $Suggest->getSuggest($keywords, $SPT);
  if($Suggest->reTry){
	?>
  <script>
	 document.location.href="<?php echo xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'spt='.(SEARCH_PROXIMITY_TRIGGER-10).'&keywords='.htmlspecialchars(xtc_db_input($_GET['keywords'])));?>";
	</script>
	</body>
	</html>
	<?php
	}
		# header("location: " . xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'spt='.(SEARCH_PROXIMITY_TRIGGER-1).'keywords='.htmlspecialchars(xtc_db_input($_GET['keywords']))));
  $module_content_keywords = $Suggest->resultKeywords;
  $module_content_products = $Suggest->resultProducts;
  $parse_time = $Suggest->parse_time;
  
  
  if ($module_content_keywords){
    $module_smarty->assign('keyword_data', $module_content_keywords);
  } 

  if ($module_content_products){
    $info_smarty= new Smarty;
    $info_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
    $info_smarty->assign('language', $_SESSION['language']);
    $info_smarty->assign('module_content', $module_content_products);
    $info_smarty->caching = 0;
    $module_smarty->assign('suggest_products', $info_smarty->fetch(CURRENT_TEMPLATE.'/module/suggest_products.html'));      
  } 

  if (SEARCH_SHOW_PARSETIME == 'true'){
    $module_smarty->assign('PARSE_TIME', '<small>'.$parse_time.' s</small>');    
  }
}

?>
