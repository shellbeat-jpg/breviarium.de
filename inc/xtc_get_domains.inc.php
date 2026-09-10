<?php  # MODULE DOMAINS


 function admin_access_language($customers_id, $languages_id=0){
    $query_raw = "SELECT *  FROM " . TABLE_ADMIN_ACCESS_LANGUAGES . " WHERE customers_id ='$customers_id'";
		if($languages_id>0){
       $query_raw .= " and languages_id = '$languages_id'";
	     $query = xtc_db_query($query_raw);
       return (xtc_db_num_rows($query)>0);
		}else{
       $arr_return=array();
	     $query = xtc_db_query($query_raw);
	     while($languages = xtc_db_fetch_array($query)){
	       $arr_return[] = $languages['languages_id'];
	     }
	     return $arr_return ;
		}
 }

 function admin_access_domains($customers_id, $domain_id=0){
    $query_raw = "SELECT *  FROM " . TABLE_ADMIN_ACCESS_DOMAINS . " WHERE customers_id ='$customers_id'";
		if($domain_id>0){
       $query_raw .= " and domain_id = '$domain_id'";
	     $query = xtc_db_query($query_raw);
       return (xtc_db_num_rows($query)>0);
		}else{
       $arr_return=array();
	     $query = xtc_db_query($query_raw);
	     while($domains = xtc_db_fetch_array($query)){
	       $arr_return[] = $domains['domain_id'];
	     }
	     return $arr_return ;
		}
 }
 
 function xtc_get_host() {
  $http_host = strtolower($_SERVER['HTTP_HOST']);
  $http_host2 = strtolower(getenv("HTTP_HOST"));
  $http_host = ($http_host == $http_host2) ? $http_host : $http_host.';'.$http_host2;
  if($http_host) return $http_host;
 }
 
 function xtc_get_sqlField() {
  (getenv('HTTPS')=='1'||getenv('HTTPS')=='on')?$http_field='domain_https':$http_field='domain_http';
  return $http_field;
 }
 
 function xtc_check_domain($categories_domain) {
   $domain = xtc_get_domains($categories_domain);
   return ($domain==xtc_get_host());
 } 
 
 function xtc_get_domains($domain_id = 0, $arr_return = Null) {
  $domain_query_raw = "SELECT *  FROM " . TABLE_DOMAINS . " WHERE domain_status ='1'"; 
  if($domain_id > 0){
    $domain_query_raw .= " and domain_id = '$domain_id'";
    $domain_query = xtc_db_query($domain_query_raw);
    $domain = xtc_db_fetch_array($domain_query);
    return $domain['domain_http']; 
  }else{ 
    $domain_query = xtc_db_query($domain_query_raw);
		if(!isset($arr_return))
		   $arr_return = array();
    while($domain = xtc_db_fetch_array($domain_query)){
       $arr_return[] = array('id' => $domain['domain_id'], 'text' => $domain['domain_http'], 'template' => $domain['template']);                
    }  
    return $arr_return;    
  }      
 }
 
 function xtc_get_template_by_domain($domain_id, $domain_array) { 
  for($i=0; $i < count($domain_array); $i++){
    if($domain_array[$i]['id'] == $domain_id)
      return $domain_array[$i]['template'];      
  }
  return false;
 }
 
 function xtc_validate_domain($domain_id = 0, $language_id) {
    $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_LANGUAGES_TO_DOMAINS . " WHERE languages_id = '$language_id' and	domain_id  = '$domain_id'");
		if(xtc_db_num_rows($domain_query))
		 return true;
 }

 function xtc_js_languages4domains($domain_id, $arrayValues, $arrayLanguageCodes) {
    $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_LANGUAGES_TO_DOMAINS . " where domain_id  = '$domain_id' order by languages_id");
		$arrData=array();
		for($i=0; $i < count($arrayLanguageCodes); $i++){
		    $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."'] = new Array();\n";
		    $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."']['id'] = ".$arrayLanguageCodes[$i]['idLang'].";\n";
		    $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."']['code'] = '".$arrayLanguageCodes[$i]['id']."';\n";
        $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."']['text'] = '".$arrayLanguageCodes[$i]['text']."';\n";
        #$str_return .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."'] = ".$arrayLanguageCodes[$i]['idLang'].";\n";
				$arrCode[$arrayLanguageCodes[$i]['idLang']]=$arrayLanguageCodes[$i]['id'];
		}
		$str_return  .= "\n\n";
		if(xtc_db_num_rows($domain_query)){
			$str_return .= "arrDomain[$domain_id] = new Array();\n";
			$t=0;
			while($domain = xtc_db_fetch_array($domain_query)){
			  $str_return  .= "arrDomain[$domain_id][$t] = new Array();\n";
				$str_return  .= "arrDomain[$domain_id][$t]['id'] = ".$domain['languages_id'].";\n";
				$str_return  .= "arrDomain[$domain_id][$t]['code'] = '".$arrCode[$domain['languages_id']]."';\n";
				$str_return  .= "arrDomain[$domain_id][$t]['language'] = '".$arrayValues[$domain['languages_id']]."';\n";
        #$str_return  .= "arrDomain[$domain_id][$t] = new Array('id' = ".$domain['languages_id'].", 'language' = '".$arrayValues[$domain['languages_id']]."');\n";
        $t++;
			}
		}
		$str_return  .= "\n\n";
		return $str_return;
 }
 function xtc_js_domains4languages($languages_id, $array) {
    $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_LANGUAGES_TO_DOMAINS . " where languages_id  = '$languages_id' order by domain_id");
		$arrData=array();
		if(xtc_db_num_rows($domain_query)){
			$str_return .= "arrLanguage[$languages_id] = new Array();\n";
			$t=0;
	    while($domain = xtc_db_fetch_array($domain_query)){
        $str_return  .= "arrLanguage[$languages_id][$t] = new Array();\n";
				$str_return  .= "arrLanguage[$languages_id][$t]['id'] = ".$domain['domain_id'].";\n";
				$str_return  .= "arrLanguage[$languages_id][$t]['domain'] = '".$array[$domain['domain_id']]."';\n";
        $t++;
			}
		}
		$str_return  .= "\n\n";
		return $str_return;
 }
?>
