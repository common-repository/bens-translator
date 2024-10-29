<?php
/**
 * Loads Translations
 * @since 0.9
 */
  $bentr_url_translate_file = plugin_basename(__FILE__); 
  $bentr_url_translate_file = str_replace( '/core/functions.php' , '' , $bentr_url_translate_file);
  load_plugin_textdomain( 'bens-translator', '/wp-content/plugins/' . $bentr_url_translate_file . '/translations' );

/**
 * Array of User Agents to Spoof Browser Request
 * @since 0.9
 */
$bentr_ua = array(
  "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.4; en-US; rv:1.9b5)",
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.0.1)",
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.0.1)",
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US)",
  "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us)",
  "Mozilla/5.0 (Macintosh; U; PPC; en-US; rv:0.9.2)",
  "Mozilla/5.0 (Windows; U; Win98; en-US; rv:0.9.2)",
  "Mozilla/5.0 (Windows; U; Win98; en-US; rv:x.xx)",
  "Mozilla/5.0 (Windows; U; Win9x; en; Stable)",
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.5)",
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.x.x)",
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.xx)",
  "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.xxx)",
  "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9b5)",
  "Mozilla/5.0 (Windows; U;XMPP Tiscali Communicator v.10.0.1; Windows NT 5.1; it; rv:1.8.1.3)",
  "Mozilla/5.0 (X11; Linux i686; U;rv: 1.7.13)",
  "Mozilla/5.0 (X11; U; Linux 2.4.2-2 i586; en-US; m18)",
  "Mozilla/5.0 (X11; U; Linux i686; en-GB; rv:1.7.6)",
  "Mozilla/5.0 (X11; U; Linux i686; en-US; Nautilus/1.0Final)",
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:0.9.3)",
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2b)",
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6)",
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.7)",
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1)",
  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.1)",
  "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9a8)",
);

/**
 * 
 * @since 0.9
 */
if (!function_exists('file_get_contents')) {
	function file_get_contents($filename, $incpath = false, $resource_context = null) {
		if (false === $handle = fopen($filename, 'rb', $incpath)) {
			return false;
		}
		if ($fsize = @filesize($filename)) {
			$buf = fread($handle, $fsize);
		} else {
			$buf = '';
			while (!feof($handle)) {
				$buf .= fread($handle, 8192);
			}
		}
		fclose($handle);
		return $buf;
	}	
}

/**
 * 
 * @since 0.9
 */
if(!interface_exists("bentr_translation_status")) {
	class bentr_translation_status {
		
		var $_status;
		var $_last_connection_time;
		
		function bentr_translation_status() {
			$exists = get_option("bentr_translation_status");
			if($exists === false){ 
				add_option("bentr_translation_status","");
			}
			$this->save();
		}
		
		function save() {
			update_option("bentr_translation_status",$this);		
		}		

		function load() {
			$status = @get_option("bentr_translation_status");
			if(is_a($status,"bentr_translation_status")) return $status;
			else return null;	
		}
			
    function save_status($status){
    	$this->_status = $status;
    	$this->save();
    }

    function get_status(){
    	return $this->_status;
    }
		
    function save_last_connection_time($last_connection_time){
    	$this->_last_connection_time = $last_connection_time;
    	$this->save();
    }

    function get_last_connection_time(){
    	return $this->_last_connection_time;
    }
		

	}

}

/**
 * 
 * @since 0.9
 */
if(!interface_exists("bentr_translation_engine")) {
	class bentr_translation_engine {
		var $_name;

		var	$_base_url;

		var $_links_pattern;

		var $_links_replacement;

		var $_languages_matrix;

		var $_available_languages;

		function bentr_translation_engine(
			$name,
			$base_url,
			$links_pattern,
			$links_replacement,
			$languages_matrix,
			$available_languages) {
	      $this->set_name($name);
        $this->set_base_url($base_url);
        $this->set_links_pattern($links_pattern);
        $this->set_links_replacement($links_replacement);
        $this->set_languages_matrix($languages_matrix);
        $this->set_available_languages($available_languages);
		}

    function set_name($name){
    	$this->_name = (string)$name;
    }

		function set_base_url($base_url){
    	$this->_base_url = (string)$base_url;
    }

		function set_links_pattern($links_pattern){
    	$this->_links_pattern = (array)$links_pattern;
    }

		function set_links_replacement($links_replacement){
    	$this->_links_replacement = (string)$links_replacement;
    }

		function set_languages_matrix($languages_matrix){
    	$this->_languages_matrix = (array)$languages_matrix;
    }

		function set_available_languages($available_languages){
    	$this->_available_languages = (array)$available_languages;
    }

    function get_name(){
    	return $this->_name;
    }

		function get_base_url(){
    	return $this->_base_url;
    }

		function get_links_pattern(){
    	return $this->_links_pattern;
    }

		function get_links_replacement(){
    	return $this->_links_replacement;
    }

		function get_languages_matrix(){
    	return $this->_languages_matrix;
    }

		function get_available_languages(){
    	return $this->_available_languages;
    }

    function decode_lang_code($res)
    {
        //if ($res == 'es') $res = 's';
        //else if ($res == 'de') $res = 'g';
        //else $res = substr($res, 0, 1);
      return $res;
    }
    
    function build_clean_link($matches){
    	$res = "href=";
    	foreach($this->_match_id as $key=>$val){
    		
    	}
			return urldecode();
		}

	}
}

//Inlcude google settings file
require_once (dirname(__file__).'/google.php');

$well_known_extensions =  
    array('swf','gif','jpg','jpeg','bmp','gz','zip','rar','tar','png','xls',
    'doc','ppt','tiff','avi','mpeg','mp3','mov','mp4','c','sh','bat');
$bentr_available_engines = array();
$bentr_available_engines['google'] = $googleEngine;

//Just incase options are missing
add_option('bentr_base_lang', 'en');
add_option('bentr_col_num', '7');
add_option('bentr_html_bar_tag', 'DIV');
add_option('bentr_my_translation_engine', 'google');
add_option('bentr_preferred_languages', array());
add_option('bentr_ban_prevention', true);
add_option('bentr_enable_debug', false);
add_option('bentr_conn_interval',580);
add_option('bentr_sitemap_integration',false);
add_option("bentr_last_connection_time",0);
add_option("bentr_translation_status","unknown");
add_option("bentr_cache_expire_time",30);
add_option('bentr_translate_template', true);
add_option("bentr_compress_cache",false);

/**
 * 
 * @since 0.9
 */
if (!function_exists('str_ireplace')){
  function str_ireplace($search,$replace,$subject){
    $token = chr(1);
    $haystack = strtolower($subject);
    $needle = strtolower($search);
    while (($pos=strpos($haystack,$needle))!==FALSE){
      $subject = substr_replace($subject,$token,$pos,strlen($search));
      $haystack = substr_replace($haystack,$token,$pos,strlen($search));
    }
    $subject = str_replace($token,$replace,$subject);
    return $subject;
  }
} 

/**
 * Settings to be moved TODO
 * @since 0.9
 */
if( !defined('WP_CONTENT_DIR') ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
$bentr_cache_dir = WP_CONTENT_DIR . "/ben-translate-cache/current";
$bentr_stale_dir = WP_CONTENT_DIR . "/ben-translate-cache/stale";
$bentr_merged_image=dirname(__file__) . '/bentr_image_map.png';
$bentr_uri_index = array();


/**
 * Sets location of Flag icons
 * @since 0.9
 */
function bentr_get_flag_image_path($language) {
  return dirname(plugin_basename(__FILE__)) . '/images/flags/flag_' . $language . '.png';
}

/**
 * 
 * @since 0.9
 */
function bentr_get_flag_image_path_copy() {
  $path = strstr(realpath(dirname(__file__)), 'wp-content');
  $path = str_replace('\\', '/', $path);
  $path = str_replace('/core', '', $path);
  return get_settings('siteurl') . '/' . $path . '/images/ben.png';
}

/**
 * Gets flag URL
 * @since 0.9
 */
function bentr_get_flag_image($language) {
  $path = strstr(realpath(dirname(__file__)), 'wp-content');
  $path = str_replace('\\', '/', $path);
  $path = str_replace('/core', '', $path);
  return get_settings('siteurl') . '/' . $path . '/images/flags/flag_' . $language . '.png';
}

/**
 * Checks whether Google XML sitemap plugin is active
 * @since 0.9
 */
function bentr_sitemap_plugin_detected(){
	if (function_exists('get_plugins')){
		$all_plugins = get_plugins();
		foreach( (array)$all_plugins as $plugin_file => $plugin_data) {
			if ($plugin_file == 'google-sitemap-generator/sitemap.php'||$plugin_file == 'sitemap.php') return true;
		}
		return false;
	} else
		return true;
}

/**
 * Checks whether Global Translator is active
 * @since 0.9
 */
function bentr_bentr_plugin_detected(){
	if (function_exists('get_plugins')){
		$all_plugins = get_plugins();
		foreach( (array)$all_plugins as $plugin_file => $plugin_data) {
			if ($plugin_file == 'global-translator/translator.php'||$plugin_file == 'translator.php') return true;
		}
		return false;
	} else
		return true;
}

/**
 * Creates Cache File
 * @since 0.9
 */
function bentr_create_file($datafile){
	$success = true;
	if (!file_exists($datafile)){
      if (($handle = @fopen($datafile, "wb")) === false) return false;
	    if ((@fwrite($handle, '')) === false) return false;
      @fclose($handle);
	} 
	return true;
}

/**
 * Creates redirect settings for .htaccess
 * @since 0.9
 */
function bentr_translator_init() {
  global $wp_rewrite;
  bentr_debug("Bens-Translator Initialized");
  if (isset($wp_rewrite) && $wp_rewrite->using_permalinks()) {
    bentr_debug("Permalinks on");
    define('REWRITEON', true);
    define('LINKBASE', $wp_rewrite->root);
  } else {
    bentr_debug("Permalinks off");
    define('REWRITEON', false);
    define('KEYWORDS_REWRITEON', '0');
    define('LINKBASE', '');
  }
  if (REWRITEON) {
    bentr_debug("Generating Rules");
    add_filter('generate_rewrite_rules', 'bentr_translations_rewrite');
  }
  
	if (isset($_GET['bentr_redir'])){
		$resource = urldecode($_GET['bentr_redir']);
		bentr_debug("Redirect Requested :: $resource");
		bentr_make_server_redirect_page($resource);
	}
}

/**
 * Adds Translated pages to Google XML Plugin
 * @since 0.9
 */
function bentr_add_translated_pages_to_sitemap() {
	global $bentr_uri_index;
	$start= round(microtime(true),4);
	@set_time_limit(120);
  global $wpdb;
  if (SITEMAP_INTEGRATION == TRUE){
	 if (bentr_sitemap_plugin_detected()){
	 	 $generatorObject = &GoogleSitemapGenerator::GetInstance();
	   $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_password='' ORDER BY post_modified DESC");
      $chosen_langs = get_option('bentr_preferred_languages');
  	
  	//homepages
		foreach($chosen_langs as $lang){
			$trans_link = "";
			if (REWRITEON){
				$trans_link = preg_replace("/".BLOG_HOME_ESCAPED."/", BLOG_HOME . "/$lang/" , BLOG_HOME );
			} else {
				$trans_link = BLOG_HOME . "?lang=$lang";
			}
			if (bentr_is_cached($trans_link,$lang))
				$generatorObject->AddUrl($trans_link,time(),"daily",1);
		}
		
		//posts
    foreach($chosen_langs as $lang){
		foreach ($posts as $post) {
			$permalink = get_permalink($post->ID);			
				$trans_link = "";
				if (REWRITEON){
					$trans_link = preg_replace("/".BLOG_HOME_ESCAPED."/", BLOG_HOME."/" . $lang, $permalink );
				} else {
					$trans_link = $permalink . "&lang=$lang";
				}
				if (bentr_is_cached($trans_link,$lang))
					$generatorObject->AddUrl($trans_link,time(),"weekly",0.2);
			}
			$bentr_uri_index[$lang] = array();//unset
		}
	}
  $end = round(microtime(true),4);
 	bentr_debug("Translated pages sitemap addition process total time:". ($end - $start) . " seconds");
	}
}

/**
 * 
 * @since 0.9
 * @updated 1.3
 */
function bentr_patch_translation_url($res) {	
  $maincont = bentr_http_get_content($res);
  $matches = array();
  preg_match( '/(\/translate_p[^"]*)"/',$maincont,$matches);
  $res = "http://translate.google.com" . $matches[1];
  $res = str_replace('&amp;','&', $res);    
  bentr_debug("bentr_patch_translation_url :: Google Patched: $res");
	return $res;
}

/**
 * 
 * @since 0.9
 */
function bentr_build_translation_url($srcLang, $destLang, $urlToTransl) {
  global $bentr_engine;
  //Encodes to HTML
  $urlToTransl = urlencode($urlToTransl); 
  //Creates array
  $tokens = array('${URL}', '${SRCLANG}', '${DESTLANG}'); 
  $srcLang = $bentr_engine->decode_lang_code($srcLang);  
  bentr_debug("Patching URL From Lang :: $srcLang");
  $destLang = $bentr_engine->decode_lang_code($destLang);
  bentr_debug("Patching URL To Lang :: $destLang");
  $values = array($urlToTransl, $srcLang, $destLang); 
  $res = str_replace($tokens, $values, $bentr_engine->get_base_url());
  return $res;
} 

/**
 * 
 * @since 0.9
 */
function bentr_clean_url_to_translate(){
  $url = bentr_get_self_url();
  bentr_debug("bentr_clean_url_to_translate :: original url:$url");
  $url = preg_replace('(\?.+$)','',$url);
  bentr_debug("bentr_clean_url_to_translate :: Cleaned url:$url");
  $url_to_translate = "";

  $blog_home_esc = BLOG_HOME_ESCAPED;

  if (REWRITEON) {
    $contains_index = (strpos($url, 'index.php')!==false);
    if ($contains_index){
      $blog_home_esc .= '\\/index.php';
    }
    $pattern1 = '/(' . $blog_home_esc . ')(\\/(' . LANGS_PATTERN . ')\\/)(.+)/';
    $pattern2 = '/(' . $blog_home_esc . ')\\/(' . LANGS_PATTERN . ')[\\/]{0,1}$/';

    if (preg_match($pattern1, $url)) {
      $url_to_translate = preg_replace($pattern1, '\\1/\\4', $url);
    } elseif (preg_match($pattern2, $url)) {
      $url_to_translate = preg_replace($pattern2, '\\1', $url);
    }
    bentr_debug("bentr_clean_url_to_translate :: [REWRITEON] self url:$url | url_to_translate:$url_to_translate");

  } else {
  
    $url_to_translate = preg_replace('/[\\?&]{0,1}lang\\=(' . LANGS_PATTERN . ')/i', '', $url);
    bentr_debug("bentr_clean_url_to_translate :: [REWRITEOFF] self url:$url | url_to_translate:$url_to_translate");
  }
  return $url_to_translate;
}

/**
 * Redirects page
 * redirects to google translate  
 * @since 0.9
 * @updated 1.6.1
 */
function bentr_make_server_redirect_page($resource){
    bentr_debug("bentr_make_server_redirect_page :: Showing redirect to google manual :: $resource");
    $dir = WP_CONTENT_DIR . "/ben-translate-cache/";
    $dir_2 = WP_CONTENT_DIR . "/plugins/bens-translator/";
    if (file_exists($dir.'redirect_template_user.php')){
      $bentr_template_file = $dir . 'redirect_template_user.php';
    }
    else {
      $bentr_template_file = $dir_2 . 'core/redirect_template.php';
    }
    ob_start(); # start buffer
      require_once ("$bentr_template_file");
      $hash = ob_get_contents();
    ob_end_clean(); # end buffer
    
      $home = get_bloginfo('url');

      $message = str_replace('{RESOURCE}', $resource , $hash);
      $message = str_replace('{HOME}', $home , $message);
      echo "$message";
      die();
}

/**
 * 
 * @since 0.9
 */
function bentr_add_get_param($url,$param, $value){
  if (strpos($url,'?')===false)
    $url .= "?$param=$value";
  else
    $url .= "&$param=$value";
  return $url;
}

/**
 * Main function, patches url, connects to google 
 * and checks if the data is correct
 * @since 0.9
 */
function bentr_translate($lang) {
  global $bentr_engine;
  
  $page = "";
  $url_to_translate = bentr_clean_url_to_translate();
  $resource = bentr_build_translation_url(BASE_LANG, $lang, $url_to_translate);
  bentr_debug("Build URL :: $url_to_translate :: Built :: $resource");
  
  if (!bentr_is_connection_allowed()){
    global $bentr_stale_dir;
    $hash = bentr_hashReqUri($_SERVER['REQUEST_URI']);
    $staledir = $bentr_stale_dir;		
    $stale_filename = $staledir . '/' . $lang . '/' . $hash;
     if (file_exists($stale_filename) && filesize($stale_filename) > 0) {
	      bentr_debug("bentr_get_page_content :: returning stale version (101) ($hash) for url:" . $url_to_translate);
	      $page = bentr_load_cached_page($stale_filename);
		    $page = bentr_insert_flag_bar($page); //could be skipped 
	      $page .= "<!--STALE VERSION: ($hash)-->";
	 			$from_cache = true;
	    } else {
	        bentr_debug("bentr_get_page_content :: No Stale Version (101) ($hash) for url:" . $url_to_translate);
			    $resource = bentr_build_translation_url(BASE_LANG, $lang, $url_to_translate);
          //no cache, no translated,no stale
	      	$page = bentr_make_server_redirect_page($resource);
      }
  } else {
    bentr_debug("Connection Allowed, Trying Google");
    update_option("bentr_last_connection_time", time());
    $translation_url = bentr_patch_translation_url($resource);
	  $buf = bentr_http_get_content($translation_url);
		if (bentr_is_valid_translated_content($buf)){
		  bentr_debug("Google Returned good Translation");
	  	bentr_store_translation_engine_status('working');
			$page = bentr_clean_translated_page($buf, $lang);
		} else {
		  bentr_debug("Google Returned BAD Translation: $url_to_translate \n$buf");
	  	bentr_store_translation_engine_status('banned');
	  	$page = bentr_make_server_redirect_page($resource);
	 	}
  }
  return $page;
}

/**
 * Connects to Google for Page
 * @since 0.9
 */
function bentr_http_get_content($resource) {
  bentr_debug("Connecting to Google");
  $isredirect = true;
  $redirect = null;
	
	while ($isredirect) {
    $isredirect = false;
    if (isset($redirect_url)) {
      $resource = $redirect_url;
    }

    $url_parsed = parse_url($resource);
    $host = $url_parsed["host"];
    $port = $url_parsed["port"];
    if ($port == 0)
      $port = 80;
    $path = $url_parsed["path"];
    if (empty($path))
      $path = "/";
    $query = $url_parsed["query"];
    $http_q = $path . '?' . $query;

    $req = bentr_build_request($host, $http_q);
				
    $fp = @fsockopen($host, $port, $errno, $errstr);

    if (!$fp) {
      return "$errstr ($errno)<br />\n";
    } else {
      fputs($fp, $req, strlen($req)); // send request
      $buf = '';
      $isFlagBar = false;
      $flagBarWritten = false;
      $beginFound = false;
      $endFound = false;
      $inHeaders = true;
			$prevline='';
      while (!feof($fp)) {
        $line = fgets($fp);
        if ($inHeaders) {
        	
          if (trim($line) == '') {
            $inHeaders = false;
            continue;
          }

          $prevline = $line;
          if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
            // Skip to the next header
            continue;
          } 
          $key = strtolower(trim($m[1]));
          $val = trim($m[2]);
					if ($key == 'location') {
            $redirect_url = $val;
          	$isredirect = true;
          	break;
          }
          continue;
        }
				
        $buf .= $line;
      } //end while
    }
    fclose($fp);
  } //while($isredirect) 
  return $buf; 
}

/**
 * DO NOT EDIT, USED TO VERIFY GOOGLE HAS SENT CORRECT DATA
 * New pages will not be cached without it 
 * @since 0.9
 */
function bentr_is_valid_translated_content($content){
	return (strpos($content, "<!-- Bens-Translator -->") > 0);
}

/**
 * Adds Comment To Header
 * DO NOT EDIT, USED TO VERIFY GOOGLE HAS SENT CORRECT DATA
 * New pages will not be cached without it  
 * @since 1.3
 */
function bentr_add_header() {
echo"<!-- Bens-Translator -->";
echo"<!-- $bentr_version_number -->";
}



/**
 * 
 * @since 0.9
 */
function bentr_store_translation_engine_status($status){
	$exists = get_option("bentr_translation_status");
	if($exists === false){ 
		add_option("bentr_translation_status","unknown");
	}
	update_option("bentr_translation_status",$status);	
}

/**
 * Check if Google connection is allowed or banned
 * @since 0.9
 */
function bentr_is_connection_allowed(){
	$last_connection_time = get_option("bentr_last_connection_time");
	$btr_interval_time = get_option("bentr_conn_interval");
	if($last_connection_time === false){ 
		update_option("bentr_last_connection_time",0);
		$last_connection_time = 0;
	} 
	
	if ($last_connection_time > 0){
		$now = time();
		$delta = $now - $last_connection_time;
		if (!is_numeric($btr_interval_time)){
		bentr_debug("interval time is not numeric :: $btr_interval_time ::");
    }
    else {
    $btr_connection_interval = '580';
    }
		if ($delta < CONN_INTERVAL){
			bentr_debug("bentr_is_connection_allowed :: Blocking connection request: delta=$delta secs");
			$res = false;
		} else {
			bentr_debug("bentr_is_connection_allowed :: Allowing connection request: delta=$delta secs");
			update_option("bentr_last_connection_time", $now);
	    $res = true;
	  }
	} else {
		bentr_debug("bentr_is_connection_allowed :: Warning: 'last_connection_time' is undefined: allowing translation");
		$res = true;
	}
	return $res;
}

/**
 * 
 * @since 0.9
 */
function bentr_clean_link($matches){
  if (TRANSLATION_ENGINE == 'google'){
    $res = "=\"" . urldecode($matches[1]) . $matches[3] . "\"";
    if ($matches[4] == '>') $res .= ">";
  } else {
    $res = "=\"" . urldecode($matches[1]) . "\"";
  }
  return $res;
}

/**
 * Cleans Translated Page for output
 * @since 0.9
 */
function bentr_clean_translated_page($buf, $lang) {
  global $bentr_engine;
	global $well_known_extensions;  
	$is_IIS = (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) ? true : false;

	$patterns = $bentr_engine->get_links_pattern();
	foreach( $patterns as $id => $pattern){
  	$buf = preg_replace_callback($pattern, "bentr_clean_link", $buf);
  }
	
  $buf = preg_replace("/<meta name=\"description\"([ ]*)content=\"([^>]*)\"([ ]*)\/>/i", "", $buf);
  $buf = preg_replace("/<meta name='description'([ ]*)content='([^>]*)'([ ]*)\/>/i", "", $buf);


  $blog_home_esc = BLOG_HOME_ESCAPED;
  $blog_home = BLOG_HOME;

  if (REWRITEON) {
    if ($is_IIS){
      $blog_home_esc .= '\\/index.php';
      $blog_home .= '/index.php';
      $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "(((?![\"])(?!\/trackback)(?!\/feed)" . bentr_get_extensions_skip_pattern() . ".)*)\"([^>]*)>/i";
      $repl = "<a\\1href=\"" . $blog_home . '/' . $lang . "\\2\" \\4>";
      //bentr_debug("IS-IIS".$repl."|".$pattern);
      $buf = preg_replace($pattern, $repl, $buf);
    } else {
      $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "(((?![\"])(?!\/trackback)(?!\/feed)" . bentr_get_extensions_skip_pattern() . ".)*)\"([^>]*)>/i";
      $repl = "<a\\1href=\"" . $blog_home . '/' . $lang . "\\2\" \\4>";
      //bentr_debug($repl."|".$pattern);
      $buf = preg_replace($pattern, $repl, $buf);
    }
  } else {
    $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "\/\?(((?![\"])(?!\/trackback)(?!\/feed)" . bentr_get_extensions_skip_pattern() . ".)*)\"([^>]*)>/i";
    $repl = "<a\\1href=\"" . $blog_home . "?\\2&lang=$lang\" \\4>";
    $buf = preg_replace($pattern, $repl, $buf);
    
    $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "[\/]{0,1}\"([^>]*)>/i";
    $repl = "<a\\1href=\"" . $blog_home . "?lang=$lang\" \\2>";
    $buf = preg_replace($pattern, $repl, $buf);
  }

  //let's remove custom tags
  $buf = preg_replace("/<iframe src=\"[^>]*><\/iframe>/i", "",$buf);
  $buf = preg_replace("/<script>[^<]*<\/script>[^<]*<script src=\"[^\"]*translate_c.js\"><\/script>[^<]*<script>[^<]*_intlStrings[^<]*<\/script>[^<]*<style type=[\"]{0,1}text\/css[\"]{0,1}>\.google-src-text[^<]*<\/style>/i", "",$buf);
  $buf = preg_replace("/_setupIW\(\);_csi\([^\)]*\);/","",$buf);
  $buf = preg_replace("/onmouseout=[\"]{0,1}_tipoff\(\)[\"]{0,1}/i", "",$buf);
  $buf = preg_replace("/onmouseover=[\"]{0,1}_tipon\(this\)[\"]{0,1}/i", "",$buf);
  $buf = preg_replace("/<span class=[\"]{0,1}google-src-text[\"]{0,1}[^>]*>/i", "<span style=\"display:none;\">",$buf);
  $buf = preg_replace("/<span style=\"[^\"]*\" class=[\"]{0,1}google-src-text[\"]{0,1}[^>]*>/i", "<span style=\"display:none;\">",$buf);
  
	if (HARD_CLEAN){
		$out = array();
		$currPos=0;
		$result = "";
		$tagOpenPos = 0;
		$tagClosePos = 0;
		
		while (!($tagOpenPos === false)){
			$beginIdx = $tagClosePos;
      $tagOpenPos = stripos($buf,"<span style=\"display:none;\">",$currPos);
      $tagClosePos = stripos($buf,"</span>",$tagOpenPos);
			if ($tagOpenPos == 0 && ($tagOpenPos === false) && strlen($result) == 0){
				bentr_debug("===>break all!");
				$result = $buf;
				break;
			}
			$offset = substr($buf,$tagOpenPos,$tagClosePos - $tagOpenPos + 7);
			preg_match_all('/<span[^>]*>/U',$offset,$out2,PREG_PATTERN_ORDER);
			$nestedCount = count($out2[0]);
			
			for($i = 1; $i < $nestedCount; $i++){
        $tagClosePos = stripos($buf,"</span>",$tagClosePos + 7);
			}
			if ($beginIdx > 0)$beginIdx += 7;
			
			$result .= substr($buf,$beginIdx,$tagOpenPos - $beginIdx);
			$currPos = $tagClosePos;
		}
		//bentr_debug($result);
		$buf = $result . substr($buf,$beginIdx);//Fixed by adding the last part of the translation: thanks Nick Georgakis!
	}
  
  $buf = bentr_insert_flag_bar($buf);
  
  return $buf;
}

/**
 * 
 * @since 0.9
 */
function bentr_insert_flag_bar($buf){
	$bar = bentr_get_flags_bar();

	$startpos = strpos($buf, FLAG_BAR_BEGIN);
	$endpos = strpos($buf, FLAG_BAR_END);
	if ($startpos > 0 && $endpos > 0){
    $buf = substr($buf, 0, $startpos) . $bar . substr($buf, $endpos + strlen(FLAG_BAR_END));
  } else {
    bentr_debug("Flags bar tokens not found: translation failed or denied");
  }
  
  return $buf;
}

/**
 * 
 * @since 0.9
 */
function bentr_get_extensions_skip_pattern() {
	global $well_known_extensions;
	
	$res = "";
	foreach ($well_known_extensions as $key => $value) {
		$res .= "(?!\.$value)";
	}
	return $res;
}

/**
 * Gets random User Agent to Spoof Browser
 * @since 0.9
 */
function bentr_get_random_UA(){
	global $bentr_ua;
	$tot = count($bentr_ua);
	$id = rand( 0, count($bentr_ua)-1 );
	$ua = $bentr_ua[$id];
	//bentr_debug("Random UA nr $id: $ua");
	return $ua;
}

/**
 * Builds HTTP headers for connection to google with random UA
 * @since 0.9
 */
function bentr_build_request($host, $http_req) {
  $res = "GET $http_req HTTP/1.0\r\n";
  $res .= "Host: $host\r\n";
  $res .= "User-Agent: " . bentr_get_random_UA() . " \r\n";
  $res .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n";
  $res .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
  $res .= "Connection: close\r\n";
  $res .= "\r\n";
  return $res;
}

/**
 * 
 * @since 0.9
 */
function bentr_build_flags_bar() {
  echo (bentr_get_flags_bar());
}

/**
 * for backward compatibility (REMOVE?)
 * @since 0.9
 */
function build_flags_bar() {
  echo (bentr_get_flags_bar());
}

/**
 * 
 * @since 0.9
 */
function bentr_get_translated_url($language, $url) {
  if (REWRITEON) {
    $contains_index = (strpos($url, 'index.php')!==false);
    $blog_home_esc = BLOG_HOME_ESCAPED;
    if ($contains_index){
      $blog_home_esc .= '\\/index.php';
    }
		$pattern = '/' . $blog_home_esc . '\\/((' . LANGS_PATTERN . ')[\\/])*(.*)/';

    if (preg_match($pattern, $url)) {
      $uri = preg_replace($pattern, '\\3', $url);
    } else {
      $uri = '';
    }

    $blog_home = BLOG_HOME;
    if ($contains_index){
      $blog_home .= '/index.php';
    }
    if ($language == BASE_LANG)
      $url = $blog_home . '/' . $uri;
    else
      $url = $blog_home . '/' . $language . '/' . $uri;
  } else {
    //REWRITEOFF
    $pattern1 = '/(.*)([&|\?]{1})lang=(' . LANGS_PATTERN . ')(.*)/';
    $pattern2 = '/(.*[&|\?]{1})lang=(' . LANGS_PATTERN . ')(.*)/';

    if ($language == BASE_LANG) {
      $url = preg_replace($pattern1, '\\1\\4', $url);
    } else
      if (preg_match($pattern2, $url)) {
        $url = preg_replace($pattern2, '\\1lang=' . $language . '\\3', $url);
      } else {
        if (strpos($url,'?')===false)
          $url .= '?lang=' . $language;
        else
          $url .= '&lang=' . $language;
      }

  }

  return $url;
}

/**
 * 
 * @since 0.9
 */
function bentr_get_self_url() {
  $full_url = 'http';
  $script_name = '';
  if (isset($_SERVER['REQUEST_URI'])) {
    $script_name = $_SERVER['REQUEST_URI'];
  } else {
    $script_name = $_SERVER['PHP_SELF'];
    if ($_SERVER['QUERY_STRING'] > ' ') {
      $script_name .= '?' . $_SERVER['QUERY_STRING'];
    }
  }
  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $full_url .= 's';
  }
  $full_url .= '://';
  if ($_SERVER['SERVER_PORT'] != '80') {
    $full_url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $script_name;
  } else {
    $full_url .= $_SERVER['HTTP_HOST'] . $script_name;
  }
  return $full_url;
}

/**
 * 
 * @since 0.9
 */
//rewrite rules definitions
function bentr_translations_rewrite($wp_rewrite) {
  //$wp_rewrite->flush_rules();
  $translations_rules = array('^(' . LANGS_PATTERN . ')$' =>
    'index.php?lang=$matches[1]', '^(' . LANGS_PATTERN . ')/(.+?)$' =>
    'index.php?lang=$matches[1]&url=$matches[2]');
    bentr_debug("Translation Rules:$translations_rules");
  $wp_rewrite->rules = $translations_rules + $wp_rewrite->rules;
}

/**
 * 
 * @since 0.9
 */
function bentr_get_cookies() {
  $string = '';
  while ($key = key($_COOKIE)) {
    if (preg_match("/^wordpress|^comment_author_email_/", $key)) {
      $string .= $_COOKIE[$key] . ",";
    }
    next($_COOKIE);
  }
  reset($_COOKIE);
  return $string;
}

/**
 * Checks to see if there is a cached file
 * @since 0.9
 */
function bentr_is_cached($url,$lang){
	global $bentr_cache_dir, $bentr_stale_dir;

  $url_parts = parse_url($url);
  $host = 'http://' . $url_parts['host'];
  $host_escaped = str_replace('/', '\\/', $host);

  $cachedir = $bentr_cache_dir."/$lang";
  $staledir = $bentr_stale_dir."/$lang";
  $uri = preg_replace("/$host_escaped/", '', $url);
  $hash = bentr_hashReqUri($uri);
  $filename = $cachedir . '/' . $hash;
  $stale_filename = $staledir . '/' . $hash;
  return (is_file($filename)||is_file($stale_filename));
  
}

/**
 * Creates Cache Folder
 * @since 0.9
 */
function bentr_mkdir($dirtomake){
  if (!is_dir($dirtomake)) { 
    if (!@mkdir($dirtomake, 0755)){
     	die("<b>Bens-Translator has detected a problem with your filesystem permissions:<br />The cache dir <em>$dirtomake</em> cannot be created. <br />Please make readable and writeable the following directory (755): <br /><em>".WP_CONTENT_DIR."/ben-translate-cache</em>. and the plugin directory.</b>");}
    if(!file_exists($dirtomake) || !is_readable($dirtomake) || !is_writeable($dirtomake)){
    	die("<b>Bens-Translator has detected a problem with your filesystem permissions:<br />The cache dir <em>$dirtomake</em> cannot be created. <br />Please make readable and writeable the following directory (755): <br /><em>".WP_CONTENT_DIR."/ben-translate-cache</em>. and the plugin directory.</b>");}
  }
	
}

/**
 * Gets Translation from cache
 * @since 0.9
 */
function bentr_get_page_content($lang, $url) {
 	global $bentr_cache_dir;
	global $bentr_stale_dir;

  $page = '';
  $hash = bentr_hashReqUri($_SERVER['REQUEST_URI']);
  //bentr_debug("Hashing uri: ".$_SERVER['REQUEST_URI']." to: $hash");

  $cachedir = $bentr_cache_dir;
  $staledir = $bentr_stale_dir;

  //bentr_debug("==>$cachedir");
  bentr_mkdir($cachedir);
  bentr_mkdir($staledir);

	bentr_move_to_new_cache_loc($hash,$lang);				
	bentr_move_to_new_stale_loc($hash,$lang);				
	
  $filename = $cachedir . '/' . $lang . '/' . $hash;
  $stale_filename = $staledir . '/' . $lang . '/' . $hash;
  
  if(file_exists($filename) && (!is_readable($filename) || !is_writeable($filename))){
  	return "<b>Bens Translator has detected a problem with your filesystem permissions:<br />The cached file <em>$filename</em> cannot be read or modified. <br />Please chmod it in order to make it readable and writeable.</b>";
  }
  if(file_exists($stale_filename) && (!is_readable($stale_filename) || !is_writeable($stale_filename))){
  	return "<b>Bens Translator has detected a problem with your filesystem permissions:<br />The cached file <em>$stale_filename</em> cannot be read or modified. <br />Please chmod it in order to make it readable and writeable.</b>";
  }
  
  if (file_exists($filename) && filesize($filename) > 0) {
    
    // We are done, just return the file and exit
    bentr_debug("bentr_get_page_content :: returning cached version ($hash) for url:" . bentr_get_self_url());
    $page .= bentr_load_cached_page($filename);
    
    // Insert Translated From Template
    $replace = bentr_get_trans_template($hash);
    $page = preg_replace("/<body [^>]*>/i", "$replace" ,$page);
    
     $page = preg_replace("/<iframe src=\"[^>]*><\/iframe>/i", "",$page);
     $page = preg_replace ("/<html xmlns=http:\/\/www.w3.org\/1999\/xhtml dir=ltr lang=en-US>/i" , "<meta name=\"language\" content=\"$lang\" />" , $page);
    
    //Sanatise Ouput
    //Clean Docment with DOM
    $bentr_translate_validate = get_option('bentr_translate_validate');
    
    if ($bentr_translate_validate == TRUE){
      $DDoc = new DOMDocument();
      @$DDoc->loadHTML($page);
      $DDoc->normalizeDocument();
      $page = $DDoc->saveHTML();
    }
    
    
    //Fix loss of / in meta etc.
    $page =  preg_replace ("/<(input|img|meta|base|link|col) ([^>]*)([^\/])>/i" , "<$1 $2$3 />" , $page);
    $page =  preg_replace ("/<head><script>([^>]*)<\/script><\/head>/i" , "" , $page);
    $page =  preg_replace ("/<br>/i" , "<br />" , $page);

    
    
    //Finish page
    $page .= "<!--CACHED VERSION ($hash)-->";
    
    $page = bentr_insert_flag_bar($page); //could be skipped 
		//check if needs to be scheduled for a new translation
		$filetime_days = (time() - filemtime($filename)) / 86400;
		
		if (EXPIRE_TIME > 0 && $filetime_days >= EXPIRE_TIME ){
			bentr_debug("bentr_get_page_content :: The file $filename has been created more than " . EXPIRE_TIME . " days ago. Scheduling for a new translation");
			bentr_move_cached_file_to_stale($hash,$lang);
		}

  } else {

    $url_to_translate = bentr_clean_url_to_translate();
  	bentr_debug("bentr_get_page_content :: Connecting to engine for url:" . $url_to_translate);
    $page = bentr_translate($lang);
    //check the content to be cached
		if (bentr_is_valid_translated_content($page)) {
			$bentr_last_cached_url = bentr_get_self_url();
      bentr_debug("bentr_get_page_content :: caching ($filename) [".strlen($page)."] url:" . $bentr_last_cached_url);
      bentr_save_cached_page($page,$filename);
      $page .= "<!--NOT CACHED VERSION: ($hash)-->";
      if (file_exists($stale_filename)){
      	unlink($stale_filename);
      }
    } else {
    	bentr_debug("bentr_get_page_content :: translation not available. Switching to stale for url: $url_to_translate");
	    if (file_exists($stale_filename) && filesize($stale_filename) > 0) {
	      bentr_debug("bentr_get_page_content :: returning stale version ($hash) for url:" . $url_to_translate);

	      $page = bentr_load_cached_page($stale_filename);
		    $page = bentr_insert_flag_bar($page); //could be skipped 
	      $page .= "<!--STALE VERSION: ($hash)-->";
	 			$from_cache = true;
	    } else {
          $bentr_redirect_url = $_GET['bentr_redir'];
          $resource = $bentr_redirect_url;
			    //$resource = bentr_build_translation_url(BASE_LANG, $lang, $url_to_translate);
			  
          //no cache, no translated,no stale
	      	$page = bentr_make_server_redirect_page($resource);
      }
    }
  }
  return $page;
}

/**
 * 
 * @since 0.9
 */
function bentr_save_cached_page($data,$filename){
	if (COMPRESS_CACHE && function_exists('gzcompress')){
		bentr_debug("bentr_save_cached_page :: using zlib for file: $filename");
		$data = gzcompress($data, 9);
	} else {
		bentr_debug("bentr_save_cached_page :: NOT using zlib for file: $filename");
	} 
  $handle = fopen($filename, "wb");
  if (flock($handle, LOCK_EX)) { // do an exclusive lock
    fwrite($handle, $data); //write
    flock($handle, LOCK_UN); // release the lock
  } else {
    fwrite($handle, $data); 
  }
  fclose($handle);
}

/**
 * 
 * @since 0.9
 */
function bentr_load_cached_page($filename){
	$data = file_get_contents($filename);
	if (function_exists('gzuncompress')){
		if (($tmp = @gzuncompress($data))){
			$data = $tmp;
			if (!COMPRESS_CACHE){
				//save the unzipped version
				bentr_save_cached_page($data,$filename);
			}
		} else if (COMPRESS_CACHE) {
			//save the zipped version
			$bentr_file_get_contents = file_get_contents($filename);
			bentr_save_cached_page($bentr_file_get_contents , $filename);
		}
		
	}
	return $data;
}

/**
 * 
 * @since 0.9
 */
function bentr_hashReqUri($uri) {
	$uri = urldecode($uri);
	bentr_debug("bentr_hashReqUri :: original url:$uri");
  $req = preg_replace('(\?.+$)','',$uri); //Remove any queries from url, much cleaner cache
  $req = preg_replace('/(.*)\/$/', '\\1', $req);
  $req = preg_replace('/#.*$/', '', $req);
  bentr_debug("bentr_hashReqUri :: Cleaned url:$uri");
  $hash = str_replace(array('?','<','>',':','\\','/','*','|','"'), '_', $req);
  return $hash;
}

/**
 * 
 * @since 0.9
 */
function bentr_insert_my_rewrite_query_vars($vars) {
  array_push($vars, 'lang', 'url');
  return $vars;
}

    
/**
 * 
 * @since 0.9
 */
function bentr_insert_my_rewrite_parse_query($query) {
  global $bentr_cache_dir,$bentr_is_translated_page;
  $bentr_result = "";
  	  
  if (isset($query->query_vars['lang'])) {
  	$lang = $query->query_vars['lang'];
    $url = $query->query_vars['url'];
    bentr_debug("Loading Page: $url");
    if (empty($url)) {
      $url = '';
    }

    if (!is_dir($bentr_cache_dir)){
      if (!is_readable(WP_CONTENT_DIR) || !is_writable(WP_CONTENT_DIR) ){
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        die ("Unable to complete Bens-Translator initialization. Plese make writable and readable the following directory:
        <ul><li>".WP_CONTENT_DIR."</li></ul>");
      }
    }
    
    $header_url = BLOG_HOME.'/'.$url;
	  $header_url = str_replace(' ','%20',$header_url);
	  $headers = get_headers($header_url, 1);
    if ($headers[0] != 'HTTP/1.1 200 OK'){
      bentr_debug("bentr_insert_my_rewrite_parse_query :: Translate Fail, Header code :: {$headers[0]} for $header_url");
   		return;
    }
    else {
      bentr_debug("bentr_insert_my_rewrite_parse_query :: Translatation successful, server returned {$headers[0]} for $header_url");
    }

    if (bentr_not_translable_uri()){
      bentr_debug("Cannot Translate");
   		return;
		}

  	$chosen_langs = get_option('bentr_preferred_languages');

		$can_translate = true;
		$self_url = bentr_get_self_url();
		$self_uri = preg_replace("/" . BLOG_HOME_ESCAPED . "/", '', $self_url);
		
  	//if (!in_array($lang, $chosen_langs)){
    //  $redirect = bentr_clean_url_to_translate($self_url);
  	//	bentr_debug("Blocking request for not chosed language:$lang redirecting to original page: $redirect");
  	//	header("Location: $redirect", TRUE, 307);
  	//	die();
  	//}
	  
	  if (!bentr_is_user_agent_allowed() && BAN_PREVENTION){
  		bentr_debug("Limiting bot/crawler access to resource:".$url);
  		header("HTTP/1.1 404 Not Found");
			header("Status: 404 Not Found");
  		$bentr_result = NOT_FOUND;
  		$can_translate = false;
  	}
  	
  	if (preg_match("/^(" . LANGS_PATTERN . ")$/", $url) || 
  			preg_match("/^(" . LANGS_PATTERN . ")\/(.+)$/", $url) ){
  		bentr_debug("Fixing request for nested translation request:".$lang."|".$url."|".$self_url);
  		$redirect = preg_replace("/(.*)\/(" . LANGS_PATTERN . ")\/(" . LANGS_PATTERN . ")\/(.*)$/", "\\1/\\2/\\4", $self_url);
      header("Debug: 101");
      header("Location: $redirect", TRUE, 307);
      die();
  	}
		if (REWRITEON && strpos($self_url,'?')===false){
			if (strpos($self_url,'&')>0){
        bentr_debug("Blocking bad request:".$lang."|".$url);
        header("Debug: 404 101");
	  		header("HTTP/1.1 404 Not Found");
				header("Status: 404 Not Found");
	  		$bentr_result = NOT_FOUND;
	  		$can_translate = false;
			} else if (substr($self_url, -1) != '/' && strpos($self_uri,'.') === false){
			  bentr_debug("Blocking bad request 2:".$lang."|".$url);
	  		header("HTTP/1.1 307 Moved Temporarily");
	  		header("Debug: 102");
	  		header("Location: " . bentr_get_self_url() . '/');
	  		die();  		
			} else if (substr($self_url, -1) == '/' && strpos($self_uri,'.') !== false){
			  bentr_debug("Blocking bad request 3:".$lang."|".$url);
			  header("Debug: 103");
        header("HTTP/1.1 307 Moved Temporarily");
        $loc = rtrim(bentr_get_self_url(),'/');
        header("Location: " . $loc);
        die();      
			}
		}  	
  	if ($can_translate) {
      $bentr_result = bentr_get_page_content($lang, $url);
		}
		die($bentr_result);   
  }
  Else{
  //bentr_debug("URL is invalid:".$url);
  //header("HTTP/1.1 404 Not Found");
	//header("Status: 404 Not Found");
  //$bentr_result = NOT_FOUND;
  //$can_translate = false;
  //die();
  }
}

/**
 * Adds the options page to the menu
 * @since 0.9
 */
function bentr_add_options_page() {
	add_menu_page( 'bens-translator' , 'Bens Translator' , '10' , 'bens-translator/core/options-translator.php' );

	add_submenu_page( 'bens-translator/core/options-translator.php' , 'Ben Translator - Settings' , 'Settings' , '10' , 'bens-translator/core/options-translator.php' );
	add_submenu_page( 'bens-translator/core/options-translator.php' , 'Ben Translator - Translated Pages' , 'Translated Pages' , '10' , 'bens-translator/core/bentr-translated.php' );
	add_submenu_page( 'bens-translator/core/options-translator.php' , 'Ben Translator - Cache Management' , 'Cache Management' , '10' , 'bens-translator/core/bentr-cacheman.php' );
}

/**
 * Outputs debug data to file
 * @since 0.9
 */
function bentr_debug($msg) {
  if (DEBUG) {
    $today = date("Y-m-d H:i:s ");
    $myFile = dirname(__file__) . "/debug.log";
    $myFile = str_replace('/core', '', $myFile);
    $fh = fopen($myFile, 'a') or die("Can't open debug file. Please manually create the 'debug.log' file (inside the 'Bens-translator' directory) and make it writable.");
    $ua_simple = preg_replace("/(.*)\s\(.*/","\\1",$_SERVER['HTTP_USER_AGENT']);
    //fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - [mem:" . memory_get_usage() . "] " . $msg . "\n");
    if (is_array($msg)){
    	foreach($msg as $key => $item)
    		fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - " . $key . "=>" . $item . "\n");
    }else
    	fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - " . $msg . "\n");
    fclose($fh);
  }
}

/**
 * 
 * @since 0.9
 */
function bentr_debug_ua($msg) {
  if (DEBUG_UA) {
    $today = date("Y-m-d H:i:s ");
    $myFile = dirname(__file__) . "/ua.log";
    $fh = fopen($myFile, 'a') or die("Can't open debug file. Please manually create the 'debug.log' file (inside the 'Bens-translator' directory) and make it writable.");
    $ua_simple = preg_replace("/(.*)\s\(.*/","\\1",$_SERVER['HTTP_USER_AGENT']);
    //fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - [mem:" . memory_get_usage() . "] " . $msg . "\n");
    fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - " . $msg . "\n");
    fclose($fh);
  }
}

/**
 * 
 * @since 0.9
 */
function bentr_not_translable_uri(){
  $not_translable = array("share-this","download.php");
  $url = bentr_get_self_url();
  if (isset($url))
    $url = strtolower($url);
  else
    $url = "";
  if ($url == "") {
    return false;
  } else {
    while (list($key, $val) = each($not_translable)) {
      if (strstr($url, strtolower($val))) {
        bentr_debug("Detected and blocked untranslable uri: $url");
        return true;
      }
    }
  }  
  return false;
}

/**
 * 
 * @since 0.9
 */
function bentr_is_browser() {
  $browsers_ua = array(
  "MSIE", 
  "UP.Browser",
  "Mozilla", 
  "Opera", 
  "NSPlayer", 
  "Avant Browser",
  "Konqueror",
  "Safari",
  "Netscape"  
  );
  if (isset($_SERVER['HTTP_USER_AGENT']))
    $ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
  else
    $ua = "";

  if ($ua == "") {
    return false;
  } else {
    while (list($key, $val) = each($browsers_ua)) {
      if (strstr($ua, strtoupper($val))) {
        return true;
      }
    }
  }
  return false;
}

/**
 * Defines bad user agents
 * For Bad Robot blocking 
 * @since 0.9
 */
function bentr_is_user_agent_allowed() {
  $not_allowed = array("Wget", "EmailSiphon", "WebZIP", "MSProxy/2.0", "EmailWolf",
    "webbandit", "MS FrontPage", "GetRight", "AdMuncher", "Sqworm", "SurveyBot",
    "TurnitinBot", "WebMirror", "WebMiner", "WebStripper", "WebSauger", "WebReaper",
    "WebSite eXtractor", "Teleport Pro", "CherryPicker", "Crescent Internet ToolPak",
    "EmailCollect", "ExtractorPro", "NEWT ActiveX", "sexsearcher", "ia_archive",
    "NameCrawler", "Email spider", "GetSmart", "Grabber", "GrabNet", "EmailHarvest",
    "Go!Zilla", "LeechFTP", "Vampire", "SmartDownload", "Sucker", "SuperHTTP",
    "Collector", "Zeus", "Telesoft", "URLBlaze", "VobSub", "Vacuum", "Space Bison",
    "WinWAP", "3D-FTP", "Wapalizer", "DTS agent", "DA 5.", "NetAnts", "Netspider",
    "Disco Pump", "WebFetch", "DiscoFinder", "NetZip", "Express WebPictures",
    "Download Demon", "eCatch", "WebAuto", "Offline Expl", "HTTrack",
    "Mass Download", "Mister Pix", "SuperBot", "WebCopier", "FlashGet", "larbin",
    "SiteSnagger", "FlashGet", "NPBot", "Kontiki","Java","ETS V5.1",
    "IDBot", "id-search", "libwww", "lwp-trivial", "curl", "PHP/", "urllib", 
    "GT::WWW", "Snoopy", "MFC_Tear_Sample", "HTTP::Lite", "PHPCrawl", "URI::Fetch", 
    "Zend_Http_Client", "http client", "PECL::HTTP","libwww-perl","SPEEDY SPIDER",
    "YANDEX","YETI","DOCOMO","DUMBOT","PDFBOT","CAZOODLEBOT","RUNNK","ICHIRO",
    "SPHERE SCOUT");

  $allowed = array("compatible; MSIE", "T720", "MIDP-1.0", "AU-MIC", "UP.Browser",
    "SonyEricsson", "MobilePhone SCP", "NW.Browser", "Mozilla", "UP.Link",
    "Windows-Media-Player", "MOT-TA02", "Nokia", "Opera/7", "NSPlayer",
    "GoogleBot", "Opera/6", "Panasonic", "Thinflow", "contype", "klondike", "UPG1",
    "SEC-SGHS100", "Scooter", "almaden.ibm.com",
    "SpaceBison/0.01 [fu] (Win67; X; ShonenKnife)", "Internetseer","MSNBOT-MEDIA/",
    "MEDIAPARTNERS-GOOGLE","MSNBOT","Avant Browser","GIGABOT","OPERA");

  if (isset($_SERVER['HTTP_USER_AGENT']))
    $ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
  else
    $ua = "";
  if ($ua == "") {
    return false;
  } else {
    while (list($key, $val) = each($not_allowed)) {
      if (strstr($ua, strtoupper($val))) {
        //bentr_debug("Detected and blocked user agent: $ua");
        return false;
      }
    }
  }
  $notknown = 1;
  while (list($key, $val) = each($allowed)) {
    if (strstr($ua, strtoupper($val))) {
      $notknown = 0;
    }
  }
  if ($notknown) {
    bentr_debug_ua("$ua");
  }
  return true;
}

/**
 * 
 * @since 0.9
 */
function bentr_erase_common_cache_files($post_ID) {
	global $bentr_cache_dir;
	global $bentr_stale_dir;
	global $bentr_engine;
	
	$start= round(microtime(true),4);
	
  $single_post_pattern = "";

	$categories = array();
	$tags =  array();
	$patterns = array();

	if (isset($post_ID)){
		$post = get_post($post_ID); 
		if ($post->post_status != 'publish'){
			bentr_debug("Post not yet published (status=".$post->post_status."): no cached files to erase");
			return;
		} else {
			bentr_debug("Post published ok to cached files erase");
		}
		if (function_exists('get_the_category')) $categories = get_the_category($post_ID);

		if (function_exists('get_the_tags')) $tags = get_the_tags($post_ID);
  	if (REWRITEON) {
  		$uri = substr (get_permalink($post_ID), strlen(get_option('home')) );
  		$single_post_pattern = bentr_hashReqUri($uri);
			if (isset($categories) && is_array($categories)){
				foreach($categories as $category) { 
			    $patterns[] = '_category_' . strtolower($category->slug); 
				} 
			} else {
		    $patterns[] = '_category_'; 
			}
			if (isset($tags) && is_array($tags)){
				foreach($tags as $tag) { 
			    $patterns[] = '_tag_' . $tag->slug; 
				}
			}else{
		    $patterns[] = '_tag_'; 
			}			
  	} else {
  		$single_post_pattern = $post_ID;
			if (isset($categories) && is_array($categories)){
				foreach($categories as $category) { 
			    $patterns[] = '_cat=' . strtolower($category->cat_ID); 
				} 
			} else {
		    $patterns[] = '_cat='; 
			}
			if (isset($tags) && is_array($tags)){
				foreach($tags as $tag) { 
			    $patterns[] = '_tag=' . $tag->slug;  
				}
			}else{
		    $patterns[] = '_tag='; 
			}
  	}

		$datepattern = "";
		$post_time = $post->post_date;
		if (isset($post_time) && function_exists('mysql2date')){
			$year = mysql2date(__('Y'), $post_time);
			$month = mysql2date(__('m'), $post_time);
			//bentr_debug("==>post y=$year m=$month");
			if (REWRITEON){
				$datepattern = $year . "_" . $month;
			} else {
				$datepattern = "$year$month";
			}
		} else {
			if (REWRITEON){
				$datepattern = "[0-9]{4}_[0-9]{2}";
			} else {
				$datepattern = "[0-9]{6}";
			}
		}
  	
  	
	} else {
			bentr_debug("Post ID not set");
	}
	
  $transl_map = $bentr_engine->get_languages_matrix();

  $translations = $transl_map[BASE_LANG];
  foreach ($translations as $key => $value) {
	  $cachedir = $bentr_cache_dir . "/$key";
	  bentr_debug("begin clean $key");
  if (file_exists($cachedir) && is_dir($cachedir) && is_readable($cachedir)) {
    $handle = opendir($cachedir);
    while (FALSE !== ($item = readdir($handle))) {
    	if( $item != '.' && $item != '..' && $item != 'stale' && !is_dir($item)){
	    		bentr_delete_empty_cached_file($item,$key);
    		$donext = true;
				foreach($patterns as $pattern) { 
          if(strstr($item, $pattern)){
	            bentr_move_cached_file_to_stale($item,$key);
            $donext = false;
            break;
          }
				} 
				if ($donext){
		    	if (REWRITEON) {
		        if(	preg_match('/_(' . LANGS_PATTERN . ')_'.$datepattern.'$/', $item) ||
								preg_match('/_(' . LANGS_PATTERN . ')_page_[0-9]+$/', $item) ||
								preg_match('/_(' . LANGS_PATTERN . ')$/', $item) ||
								preg_match('/_(' . LANGS_PATTERN . ')'.$single_post_pattern.'$/', $item)) {
								  bentr_debug("begin clean $key, stage 2");
			        		bentr_move_cached_file_to_stale($item,$key);
		        }
		      } else {
		      	//no rewrite rules
		        if(	preg_match('/_p='.$single_post_pattern.'$/', $item) ||
		        		preg_match('/_paged=[0-9]+$/', $item) ||
		        		preg_match('/_m='.$datepattern.'$/', $item) ||
		        		preg_match('/_lang=(' . LANGS_PATTERN . ')$/', $item)) {
		        		  bentr_debug("begin clean $key, stage 3");
			        		bentr_move_cached_file_to_stale($item,$key);
		        }
		      }
		    }
    	}
    }
    closedir($handle);
  }
  }
  //bentr_debug("end clean");
  $end= round(microtime(true),4);
 	bentr_debug("Cache cleaning process total time:". ($end - $start) . " seconds");
}

/**
 * Deletes Cache file if empty
 * @since 0.9
 */
function bentr_delete_empty_cached_file($filename,$lang){
	global $bentr_cache_dir;
	global $bentr_stale_dir;
  $cachedir = $bentr_cache_dir."/$lang";
  $path = $cachedir.'/'.$filename;
  if (file_exists($path) && is_file($path) && filesize($path) == 0){
    bentr_debug("Erasing empty file: $path");
  	unlink($path);
  }
}

/**
 * 
 * @since 0.9
 */
function bentr_move_to_new_cache_loc($filename,$lang){
	global $bentr_cache_dir;

	$cachedir = dirname(__file__) . '/cache';
  if (is_dir($cachedir)) {
  $src = $cachedir . '/' . $filename;
  $dst = $bentr_cache_dir . '/' . $filename;
  if (file_exists($src) && !file_exists($dst) ){
	  if (!@rename($src,$dst)){
		  bentr_debug("Unable to move cached file $src to stale $dst");
	  } else {
		  bentr_debug("Moving cached file $src to stale $dst");
	  }
	}
}
  bentr_mkdir($bentr_cache_dir . '/' . $lang);
  $src = $bentr_cache_dir . '/' . $filename;
  $dst = $bentr_cache_dir . '/' . $lang . '/' . $filename;
  if (file_exists($src) && !file_exists($dst)){
    if (!@rename($src,$dst)){
      bentr_debug("Unable to move cached file $src to cache/lang $dst");
    } else {
      bentr_debug("Moving cached file $src to cache/lang $dst");
    }
  }  
}

/**
 * 
 * @since 0.9
 */
function bentr_move_to_new_stale_loc($filename,$lang){
	global $bentr_stale_dir;

	$staledir = dirname(__file__) . '/cache/stale';
  if (is_dir($staledir)) {
  $src = $staledir . '/' . $filename;
  $dst = $bentr_stale_dir . '/' . $filename;
  if (file_exists($src) && !file_exists($dst)){
		if (!@rename($src,$dst)){
		  bentr_debug("Unable to move cached file $src to stale $dst");
	  } else {
		  bentr_debug("Moving cached file $src to stale $dst");
	  }
	}
}
  bentr_mkdir($bentr_stale_dir . '/' . $lang);
  $src = $bentr_stale_dir . '/' . $filename;
  $dst = $bentr_stale_dir . '/' . $lang . '/' . $filename;
  if (file_exists($src) && !file_exists($dst)){
    if (!@rename($src,$dst)){
      bentr_debug("Unable to move stale file $src to stale/lang $dst");
    } else {
      bentr_debug("Moving stale file $src to stale/lang $dst");
    }
  }
}

/**
 * Checks if Cache file is older and moves to stale folder
 * @since 0.9
 */
function bentr_move_cached_file_to_stale($filename,$lang){
  bentr_debug("bentr_move_cached_file_to_stale:: Run");
  $bentr_error_expire_time = EXPIRE_TIME;
  
  global $bentr_cache_dir;
	global $bentr_stale_dir;
	$cachedir = $bentr_cache_dir."/$lang";
	$staledir = $bentr_stale_dir."/$lang";
  $src = $cachedir . '/' . $filename;
  $dst = $staledir . '/' . $filename;
  
  if (file_exists($src)){
    $filetime = filemtime($src);
  }
  else {
    return;
  }
  
  $filetime_days = (time() - $filetime) / 86400;
  bentr_debug("EXPIRE_TIME::$bentr_error_expire_time::$filetime_days");
  if (EXPIRE_TIME > 0 && $filetime_days >= EXPIRE_TIME ){

  if (!@rename($src,$dst)){
	  bentr_debug("Unable to move cached file $src to stale $dst");
  } else {
	  bentr_debug("Moving cached file $src to stale $dst");
  }
  }
  ELSE {
    $expire_time_error = EXPIRE_TIME;
    bentr_debug("Created less than $expire_time_error day(s) ago");
  }
}

/**
 * 
 * @since 0.9
 */
function bentr_delete_cached_file($filename,$lang){
	global $bentr_cache_dir;
  $cachedir = $bentr_cache_dir."/$lang";
  $path = $cachedir.'/'.$filename;
  if (file_exists($path) && is_file($path)){
    bentr_debug("Erasing $path");
  	unlink($path);
  }

}

/**
 * Register Sidebar Widget
 * @since 0.9
 */
function widget_bens_translator_init() {
  if(!function_exists('register_sidebar_widget')) { return; }
  function widget_bens_translator($args) {
    extract($args);
    echo $before_widget . $before_title . "Translator" . $after_title;
    bentr_build_flags_bar();
    echo $after_widget;
  }
  register_sidebar_widget('Bens Translator','widget_bens_translator');
}

/**
 * 
 * @since 0.9
 */
function bentr_get_flags_bar() {
  global $bentr_engine, $wp_query, $bentr_merged_image;
  $num_cols = BAR_COLUMNS;
	if (!isset($bentr_engine) || $bentr_engine == null ){
		bentr_debug("WARNING! Settings not correctly set!");
		return "<b>Bens-Translator not configured yet. Please go to the Options Page</b>";
	}
  
  $buf = '';
  $transl_map = $bentr_engine->get_languages_matrix();
  $translations = $transl_map[BASE_LANG];
  $transl_count = count($translations); 
  $buf .= "\n" . FLAG_BAR_BEGIN; //initial marker

  if (HTML_BAR_TAG == 'TABLE')
    $buf .= "<table border=\"0\"><tr>";
  else if (HTML_BAR_TAG == 'DIV')
    $buf .= "<div id=\"translation_bar\">";
    
  //$buf .= "<span class=\"notranslate\">";
  $curr_col = 0;
  $curr_row = 0;
  $dst_x = 0;
  $dst_y = 0;
  $map_left=0;
  $map_top=0;
  $map_right=16;
  $map_bottom=11;
  $grid;

  //filter preferred
  $preferred_transl = array();
  foreach ($translations as $key => $value) {
    if ($key == BASE_LANG || in_array($key, get_option('bentr_preferred_languages')))
      $preferred_transl[$key] = $value;
  }
  $num_rows=1;
  if ($num_cols > 0){
    $num_rows = (int)(count($preferred_transl)/$num_cols);
    if (count($preferred_transl)%$num_cols>0)$num_rows+=1;
  }

  foreach ($preferred_transl as $key => $value) {
    if ($curr_col >= $num_cols && $num_cols > 0) {
      if (HTML_BAR_TAG == 'TABLE' || HTML_BAR_TAG == 'TEXT') $buf .= "</tr><tr>";
      $curr_col = 0;
      $dst_x = 0;
      $map_left = 0;
      $map_right = 16;
      $curr_row++;
    }
    $dst_y = $curr_row * 15;
    $map_top = $curr_row * 15;
    $map_bottom = $curr_row * 15 + 11;

    $flg_url = bentr_get_translated_url($key, bentr_get_self_url());
    $flg_image_url = bentr_get_flag_image($key);
    $flg_image_path = bentr_get_flag_image_path($key);

    if (HTML_BAR_TAG == 'TABLE') $buf .= "<td>";
    
    if (HTML_BAR_TAG_FLAG == 'TEXT') {
      if (HTML_BAR_TAG == 'TABLE') {  
        $buf .= "<td>";
        $buf .= "<a id=\"flag_$key\" href=\"$flg_url\" hreflang=\"$key\" $lnk_attr>$value</a>";
      }
      else {           
        $buf .= "<a id=\"flag_$key\" href=\"$flg_url\" hreflang=\"$key\" $lnk_attr>$value</a>";
      }
    }
    
    else{
    $buf .= "<a id=\"flag_$key\" href=\"$flg_url\" hreflang=\"$key\" $lnk_attr><img id=\"flag_img_$key\" src=\"$flg_image_url\" alt=\"$value\" title=\"$value\"  border=\"0\"></img></a>";
    }
    
    
    if (HTML_BAR_TAG == 'TABLE') $buf .= "</td>";
    if ($num_cols > 0) $curr_col += 1;

  }//end foreach ($preferred_transl as $key => $value) {

  while ($curr_col < $num_cols && $num_cols > 0) {
    if (HTML_BAR_TAG == 'TABLE') $buf .= "<td>&nbsp;</td>";
    $curr_col += 1;
  }


  if ($num_cols == 0)
    $num_cols = count($translations);
     
  $flg_image_path_ben = bentr_get_flag_image_path_copy();
  
  //Please keep my link, I provide this plugin for free :)
  //or you can move it to another location
  $bentr_link = "<a style=\"font-size:9px;\" href=\"http://benosullivan.co.uk\">Bens Translator</a>";
  
  //$buf .= "</span>";
  
    if (HTML_BAR_TAG == 'TABLE')
    $buf .= "</tr><tr><td colspan=\"$num_cols\">$bentr_link</td></tr></table>";
  else if (HTML_BAR_TAG == 'DIV')
    $buf .= "</div><div id=\"transl_sign\">$bentr_link</div>";
  else
    $buf .= "";

  $buf .= FLAG_BAR_END . "\n"; //final marker
  return $buf;
}

/**
 * Includes Header Template for Visitors
 * @since 1.1
 */
function bentr_get_trans_template($hash) {
  $bentr_translate_template = get_option('bentr_translate_template');
  
  if ($bentr_translate_template == TRUE){
    $lang = get_option('bentr_base_lang');
    $bentr_trans_lang = bentr_decode_lang_code($lang);
    $bentr_get_trans_template_string = "This page has been translated from ";
    $bentr_get_trans_template_string .= $bentr_trans_lang;
    //$bentr_get_trans_template_string .= ". View the original page <a href=\"$url\">here</a>";
    ob_start(); # start buffer
      require_once (dirname(__file__).'/header_template.php');
      $hash = ob_get_contents();
    ob_end_clean(); # end buffer
   return $hash;
  }
}

/**
 * Changes short language code to readable text
 * @since 1.1
 */
function bentr_decode_lang_code($lang) { 
$bentr_lang_code = array( 'zh-CN' => 'Chinese (Simplified)',
                        'zh-TW' => 'Chinese (Traditional)',
									     'it'    => 'Italian',
									     'ko'    => 'Korean',
									     'pt'    => 'Portuguese',
									     'en'    => 'English',
									     'de'    => 'German',
									     'fr'    => 'French',
									     'es'    => 'Spanish',
									     'ja'    => 'Japanese',
									     'ar'    => 'Arabic',
									     'ru'		=> 'Russian',
									     'el'    => 'Greek',
									     'nl'		=> 'Dutch',
								       'bg'		=> 'Bulgarian',
								       'cs'		=> 'Czech',
								       'hr'		=> 'Croatian',
								       'da'		=> 'Danish',
								       'fi'		=> 'Finnish',
								       'pl'		=> 'Polish',
								       'sv'		=> 'Swedish',
    								   'no'		=> 'Norwegian',
								       'iw'		=> 'Hebrew',
								       'sr'		=> 'Serbian',
								       'sk'		=> 'Slovak',
                       'th'    => 'Thai',
                       'tr'    => 'Turkish',
                       'hu'    => 'Hungarian'                    
                      );
                      
                      $bentr_lang_decode = $bentr_lang_code[$lang];
                      return $bentr_lang_decode;
}

/**
 * Checks to see if the permalink structure has changed
 * @since 1.3
 */
function bentr_check_permalink_change () {
  $bentr_current_permalink = get_option ('bentr_current_permalink');
  $bentr_wp_current_permalink = get_option('permalink_structure');
  $bentr_wp_current_permalink_2 = rawurlencode($bentr_wp_current_permalink);

  if ($bentr_current_permalink != $bentr_wp_current_permalink_2){
    $bentr_wp_current_permalink_3 = rawurlencode($bentr_wp_current_permalink);
    update_option ('bentr_current_permalink', "$bentr_wp_current_permalink_3" );
    bentr_debug("Permalink Structure has changed :: From:$bentr_current_permalink:: To:$bentr_wp_current_permalink_2");
    bentr_translations_rewrite();
  }
  else{
    bentr_debug("Permalink Structure is the same");
  }
}
?>