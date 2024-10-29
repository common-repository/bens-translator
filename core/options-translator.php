<?php
/**
 * This file is the Admin page for the Options
 * @since 0.9
 */
//Zero Settings for Cache Information
$bentr_stale_size = 0;
$bentr_cache_size = 0;
$bentr_cached_files_num = 0;
$bentr_stale_files_num = 0;
$showstats = false;

// Get Amount of files in directory for settings page
function bentr_files_stats($dir,$exclusions=""){
  //bentr_debug("====>CALC: $dir");
	$res = array("num"=>0,"size"=>0);
  if (file_exists($dir) && is_dir($dir) && is_readable($dir)) {
  	$files = glob($dir . '/*');
    if (is_array($files)){
      foreach($files as $path){
          if ($exclusions != "" && strpos($path,$exclusions)!==false) {
            //bentr_debug("$dir: EXCLUDING====>$item");
          	continue;
    }
          if (is_dir($path)){
          	//bentr_debug("====>Found dir: $path");
          	$rres = bentr_files_stats($path, $exclusions);
            $res["size"] += $rres["size"];
            $res["num"] += $rres["num"];
            $i2++;
          }else if (file_exists($path) && is_file($path))
            $res["size"] += filesize($path);
            $res["num"]++;
  }
      
      }
    }
  //Removes Folders from count (1.0)
  $res["num"] = $res["num"] - $i2;
  return $res;
  }

// Get Current Files in Cache for Info
function bentr_init_info(){
	global $bentr_stale_size;
	global $bentr_cache_size;
	global $bentr_cached_files_num;
	global $bentr_stale_files_num;
	global $bentr_cache_dir;
	global $bentr_stale_dir;
	//cachedir
  $res_cache = bentr_files_stats($bentr_cache_dir, "stale");
  $bentr_cache_size = $res_cache["size"];
  $bentr_cached_files_num = $res_cache["num"];
  //staledir
  $res_cache = bentr_files_stats($bentr_stale_dir);
  $bentr_stale_size = $res_cache["size"];
  $bentr_stale_files_num = $res_cache["num"];
}

/**
 * 
 * @since 0.9
 */
function bentr_get_last_cached_file_time(){
	$res = -1;
  $last_connection_time = get_option("bentr_last_connection_time");
  if ($last_connection_time > 0){
	  $now = time();
	  $res = $now - $last_connection_time;
  }
	return $res;
}

//This sets the link to the plugin admin page
$location = get_option('siteurl') . '/wp-admin/admin.php?page=bens-translator/core/options-translator.php';
	
$diff_time = bentr_get_last_cached_file_time();

/**
 * Checks whether form was posted and updates settings
 * @since 0.9
 */
if (isset($_POST['stage'])){
	//submitting something
	$bentr_base_lang 						 = $_POST['bentr_base_lang'];
	$bentr_col_num 							 = $_POST['bentr_col_num'];
	$bentr_html_bar_tag 				 = $_POST['bentr_html_bar_tag'];
	$bentr_html_bar_flag 		     = $_POST['bentr_html_bar_flag'];
	$bentr_my_translation_engine = $_POST['bentr_my_translation_engine'];
	$bentr_conn_interval 				 = $_POST['bentr_conn_interval'];
	$bentr_cache_expire_time 		 = $_POST['bentr_cache_expire_time'];
	$bentr_translate_template    = $_POST['bentr_translate_template'];
  $bentr_translate_validate     = $_POST['bentr_translate_validate'];                       

	if (isset($_POST['bentr_preferred_languages']))
		$bentr_preferred_languages = $_POST['bentr_preferred_languages'];
	
	if(isset($_POST['bentr_enable_debug'])) 
		$bentr_enable_debug = true; 
	else 
		$bentr_enable_debug = false;

	if(isset($_POST['bentr_ban_prevention'])) 
		$bentr_ban_prevention = true; 
	else 
		$bentr_ban_prevention = false;
	
	if(isset($_POST['bentr_sitemap_integration'])) 
		$bentr_sitemap_integration = true; 
	else 
		$bentr_sitemap_integration = false;
	
	if(isset($_POST['bentr_compress_cache'])) 
		$bentr_compress_cache = true; 
	else 
		$bentr_compress_cache = false;
	
	
	
	if ('change' == $_POST['stage']) {
		//recalculate some things
		$bentr_my_translation_engine = $_POST['bentr_my_translation_engine'];
		$bentr_preferred_languages = get_option('bentr_preferred_languages');
	} else if ('process' == $_POST['stage']){
	  if(!empty($_POST["bentr_erase_cache"])) {
	
	  } else {
	  	//update options button pressed

      $iserror = false;
	    if (count ($bentr_preferred_languages) == 0) {
	      $message .= "Error: you must choose almost one of the available translations.";
	      $iserror = true;
	    }
	    
	    if(!$iserror) {
        if (file_exists($bentr_merged_image) && is_file($bentr_merged_image))
          unlink($bentr_merged_image);
	      update_option('bentr_base_lang', $_POST['bentr_base_lang']);
	      update_option('bentr_col_num', $_POST['bentr_col_num']);
	      update_option('bentr_html_bar_tag', $_POST['bentr_html_bar_tag']);
	      update_option('bentr_html_bar_flag', $_POST['bentr_html_bar_flag']);
	      update_option('bentr_my_translation_engine', $_POST['bentr_my_translation_engine']);
	      update_option('bentr_preferred_languages', array());
	      update_option('bentr_preferred_languages', $_POST['bentr_preferred_languages']);
				update_option("bentr_last_connection_time",time());
				update_option("bentr_translation_status","unknown");
				update_option('bentr_translate_template', $_POST['bentr_translate_template']);
				update_option('bentr_translate_validate', $_POST['bentr_translate_validate']);
	      $diff_time = -1;

	      $conn_int = $_POST['bentr_conn_interval'];
	      if (!is_numeric($conn_int))$conn_int = 580;
	      update_option('bentr_conn_interval', $conn_int);
				$bentr_conn_interval = $conn_int;
				
	      $exp_time = $_POST['bentr_cache_expire_time'];
	      if (!is_numeric($exp_time))$exp_time = 30;
	      update_option('bentr_cache_expire_time', $exp_time);
				$bentr_cache_expire_time = $exp_time;
				
	
	      if(isset($_POST['bentr_ban_prevention']))
	        update_option('bentr_ban_prevention', true);
	      else
	        update_option('bentr_ban_prevention', false);

	      if(isset($_POST['bentr_sitemap_integration']))
	        update_option('bentr_sitemap_integration', true);
	      else
	        update_option('bentr_sitemap_integration', false);

	      if(isset($_POST['bentr_compress_cache']))
	        update_option('bentr_compress_cache', true);
	      else
	        update_option('bentr_compress_cache', false);
	
	
	      if(isset($_POST['bentr_enable_debug']))
	        update_option('bentr_enable_debug', true);
	      else
	        update_option('bentr_enable_debug', false);
	
	
				$wp_rewrite->flush_rules();
	      $message = "Options saved.";
	    }
	  }
	}		
} 

/**
 * Page was not posted and opened from menu
 * Loads Settings from DB 
 * @since 0.9
 */
else {
	$bentr_base_lang = get_option('bentr_base_lang');
	$bentr_col_num = get_option('bentr_col_num');
	$bentr_html_bar_tag = get_option('bentr_html_bar_tag');
	$bentr_html_bar_flag = get_option('bentr_html_bar_flag');
	$bentr_my_translation_engine = get_option('bentr_my_translation_engine');
	$bentr_preferred_languages = get_option('bentr_preferred_languages');
	$bentr_ban_prevention = get_option('bentr_ban_prevention');
	$bentr_sitemap_integration = get_option('bentr_sitemap_integration');
	$bentr_compress_cache = get_option('bentr_compress_cache');
	
	$bentr_enable_debug = get_option('bentr_enable_debug');
	$bentr_conn_interval = get_option('bentr_conn_interval');
	$bentr_cache_expire_time = get_option('bentr_cache_expire_time');
	$bentr_translate_template = get_option('bentr_translate_template');
	$bentr_translate_validate = get_option('bentr_translate_validate');


	$bentr_current_engine = $bentr_available_engines[$bentr_my_translation_engine];
	$bentr_lang_matrix = $bentr_current_engine->get_languages_matrix();
	if (count($bentr_preferred_languages) == 0) {
		$i = 0;
		foreach($bentr_lang_matrix[$bentr_base_lang] as $lang_key => $lang_value){
			if ($lang_key == $bentr_base_lang) continue;
			$bentr_preferred_languages[]=$lang_key;
			$i++;
		}
		update_option('bentr_preferred_languages', $bentr_preferred_languages);
	}

  $cachedir = $bentr_cache_dir;
  
  $message = "";

  if (!is_writeable(dirname(__file__))){
    $message = "Unable to complete Bens-Translator initialization. Please make writable and readable the following directory:
    <ul><li>".dirname(__file__)."</li></ul>";
  } else
  if (!is_dir($cachedir) && (!is_readable(WP_CONTENT_DIR) || !is_writable(WP_CONTENT_DIR) )){
    $message = "Unable to complete Bens-Translator initialization. Please make writable and readable the following directory:
    <ul><li>".WP_CONTENT_DIR."</li></ul>";
  } else {
  
  if (!is_dir($cachedir)){
    if(!mkdir($cachedir, 0777)){
      $message = "Unable to complete Bens-Translator initialization. Please manually create and make readable and writeable the following directory:
      <ul><li>".WP_CONTENT_DIR."</li></ul>";
    }
  } else if (!is_readable($cachedir) || !is_writable($cachedir) ){
    $message = "Unable to complete Bens-Translator initialization. Please make readable and writeable the following directory:
    <ul><li>".$cachedir."</li></ul>";
  }

  if (is_dir($cachedir) && is_readable($cachedir) && is_writable($cachedir)){
    $staledir = $bentr_stale_dir;
    if (!is_dir($staledir)){
      if(!mkdir($staledir, 0777)){
        $message = "Unable to complete Bens-Translator initialization. Please manually create and make readable and writeable the following directory:
        <ul><li>".$cachedir."</li></ul>";
      }
    } else if (!is_readable($staledir) || !is_writable($staledir) ){
      $message = "Unable to complete Bens-Translator initialization. Please make readable and writeable the following directory:
      <ul><li>".$staledir."</li></ul>";
    }
  }
 }
}
/*Get options for form fields*/
$bentr_current_engine = $bentr_available_engines[$bentr_my_translation_engine];
$bentr_lang_matrix = $bentr_current_engine->get_languages_matrix();

/**
 * Build Java for working out languages
 * @since 0.9
 */
function bentr_build_js_function($base_lang, $selected_item) {
	global $bentr_current_engine;
	global $bentr_lang_matrix;
?>  
<script type="text/javascript">
calculateOptions('<?php echo $base_lang ?>', <?php echo $selected_item ?>);

function languageItem(lang, flags_num){
  this.lang=lang;
  this.flags_num=flags_num;
}

function calculateOptions(lang, selectedItem) {
  var flags_num = 0;
  var list = new Array();
<?php  
  $j=0;
  foreach($bentr_lang_matrix as $key => $value){
    echo "  list[$j] = new languageItem('$key', " . count($bentr_lang_matrix[$key]) . ");\n";
    $j++;
  }
?>  
  for (z = 0; z < document.forms['form1'].bentr_col_num.options.length; z++) {
    document.forms['form1'].bentr_col_num.options[z] = null;
  }
  document.forms['form1'].bentr_col_num.options.length = 0;
  
  for (y = 0; y < list.length; y++) {
    if (list[y].lang == lang){
      flags_num = list[y].flags_num;
      break;
    }
  }
  for (i = 0; i < flags_num; i++) {
    if (i == 0) {
      opt_text='all the flags in a single row (default)';
    } else if (i == 1) {
      opt_text='1 flag for each row';
    } else {
      opt_text= i + ' flags for each row';
    }
    
    if (i == 0)
    	document.forms['form1'].bentr_col_num.options[i]=new Option(opt_text, flags_num);
    else
    document.forms['form1'].bentr_col_num.options[i]=new Option(opt_text, i);
  }
  
  //I need to cycle again on the options list in order to correctly choose the selected item
  for (i = 0; i < flags_num; i++) {
    document.forms['form1'].bentr_col_num.options[i].selected = (selectedItem == i);
  }
}

function calculateAvailableTranslations(lang, selectedItem) {
  var list = new Array();
<?php  
  $j=0;
  foreach($bentr_lang_matrix as $key => $value){
    echo "  list[$j] = new languageItem('$key', " . count($bentr_lang_matrix[$key]) . ");\n";
    $j++;
  }
?>  
  for (z = 0; z < document.forms['form1'].bentr_col_num.options.length; z++) {
    document.forms['form1'].bentr_col_num.options[z] = null;
  }
  document.forms['form1'].bentr_col_num.options.length = 0;
  
  for (y = 0; y < list.length; y++) {
    if (list[y].lang == lang){
      flags_num = list[y].flags_num;
      break;
    }
  }
  for (i = 0; i <= flags_num; i++) {
    if (i == 0) {
      opt_text='all the flags in a single row (default)';
    } else if (i == 1) {
      opt_text='1 flag for each row';
    } else {
      opt_text= i + ' flags for each row';
    }
    document.forms['form1'].bentr_col_num.options[i]=new Option(opt_text, i);
  }
  
  //I need to cycle again on the options list in order to correctly choose the selected item
  for (i = 0; i <= flags_num; i++) {
    document.forms['form1'].bentr_col_num.options[i].selected = (selectedItem == i);
  }
}
</script>
<?php
}

/**
 * Displays Form
 * @since 0.9
 */
?>
<script type="text/javascript">
<!--
    function toggleVisibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
//-->
</script>

<!-- Start Options HTML -->
			
<div class="wrap" style="width:800px;">
  <div style="border: 1px solid rgb(221, 221, 221); padding: 5px; float: right; background-color: white; margin: 15px 15px 0;">
    
    <!-- Donations Section -->
    <div style="width: 250px; height: 100px;float:left;">
	     <h3 style="margin:3px;"><?php _e('Donate', 'bens-translator') ?></h3>
        <em><?php _e('If you like this plugin and find it useful, help keep this plugin free and actively developed by clicking the', 'bens-translator') ?> <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=donations%40benosullivan%2eco%2euk&amp;lc=GB&amp;currency_code=GBP&amp;bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted"><strong><?php _e('donate', 'bens-translator') ?></strong></a> <?php _e('button', 'bens-translator') ?>.</em>
    </div>
	  <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=donations%40benosullivan%2eco%2euk&amp;lc=GB&amp;currency_code=GBP&amp;bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" title="Donate" target="_blank">
      <img style="float:right;margin-top:25px;" alt="Donate with Paypal" src="<?php echo WP_PLUGIN_URL; ?>/bens-translator/images/donate.png" />	
    </a>
  </div>
  
  <!-- Header -->
  
  <h2 style="width:300px;">Bens Translator <?php echo $bentr_version_number; ?></h2>
  <?php
  //Print out the message to the user, if any
  if($message!="") {
    echo "<div class=\"updated\" style=\"width:300px;\"><p><b>";
    echo $message;
    echo "</b></p></div>";
  } 
  //Check if Global Translator is Installed
  if(bentr_bentr_plugin_detected() == TRUE) {
    echo "<div class=\"error\" style=\"width:300px;\"><p><b>";
    echo "Global Translator is installed. This can cause problems.";
    echo "</b></p></div>";
  } 
  
?>
  <div class="updated" style="width:300px;">
    <p style="line-height:1.4em;">
      <b>Get Help With Bens Translator<br />
        <a href="http://benosullivan.co.uk/bens-translator/#commentform">Leave a Comment</a><br />
        <a href="http://benosullivan.co.uk/bens-translator/help-guide-bens-translator/">FAQ</a>
      </b>
    </p>
  </div>


<?php
  
  /**
 * Checks to see if PHP and Wordpress Minimum supported
 * @since 0.9
 */
  if (bentr_check_php_version() == FALSE){  
    echo "<div class=\"error\" style=\"width:200px;\"><p><b>";
    echo "Your PHP version is not supported, Please use PHP 5 and above.";
    echo "</b></p></div>";
  }

  if (bentr_check_wordpress_version() == FALSE){
    echo "<div class=\"error\" style=\"width:200px;\"><p><b>";
    echo "Your Wordpress version is not supported, Please use 2.8 and above.";
    echo "</b></p></div>";
  }

  function bentr_check_php_version(){
    return version_compare(PHP_VERSION, '5.0.0', '>');
  }

  function bentr_check_wordpress_version(){
    global $wp_version;
    $minimum_wp = '2.8';
    return version_compare($wp_version, $minimum_wp, '>=');
  }
  ?>
  
  <!-- Start Form -->
  
  <form id="bentr_form" name="form1" method="post" action="<?php echo $location ?>">
  	<input type="hidden" name="stage" value="process" />
    <input type="hidden" name="bentr_stats" value="<?php echo(($showstats==true)?"show":"hide");?>" />
    <input type="hidden" onclick="document.forms['form1'].stage.value='change';document.forms['form1'].submit();" 
        name="bentr_my_translation_engine" value="google" />
  <div style="width:800px;height:50px;">      
  </div>
  
  <!-- Display Flags Settings Section -->
    
  <div style="float:left;width:300px;" >
    <div style="float:left">
  		<h3><?php _e('Base settings', 'bens-translator') ?></h3>
  		  <fieldset class="options">
    		<label><?php _e('My Blog is written in', 'bens-translator') ?>:
            <select name="bentr_base_lang" onchange="document.forms['form1'].stage.value='change';document.forms['form1'].submit();">
              <?php    
              $languages = $bentr_current_engine->get_available_languages();
              foreach($languages as $key => $value){
                if ($bentr_base_lang == $key) {
              ?>
              <option value="<?php echo $key ?>" selected="selected" ><?php echo $value ?></option>
              <?php
                } else {
              ?>
              <option value="<?php echo $key ?>"  ><?php echo $value ?></option>
              <?php
                }
              }
              ?>
            </select>
          </label>
          </fieldset>
      </div>
      <div style="float:left;width:800px;margin:20px 0 20px;">
        <p style="padding:5px;"><?php _e('Choose which translations you want to make available for your visitors', 'bens-translator') ?>:</p>
          <div style="float:left">
            <fieldset>
            <?php    
              foreach($bentr_lang_matrix as $key => $langs){
                if ($bentr_base_lang == $key) {
                	$i = 0;
                	foreach($langs as $lang_key => $lang_value){
                		if ($bentr_base_lang == $lang_key) continue;
                		$chk_val = "";
                		if (count ($bentr_preferred_languages) == 0 || in_array($lang_key, $bentr_preferred_languages) ) 
                			$chk_val = "checked=\"checked\""; 
                		echo '<div style="float:left;width:175px;" ><input type="checkbox" name="bentr_preferred_languages[' . $i . ']" ' . $chk_val . ' value="' . $lang_key . '" />
                		<img alt="flag ' . $lang_key . '" src="' . bentr_get_flag_image($lang_key) . '" />' . $lang_value . '</div>';
                		$i++;
                	}
                }
              }
              ?>
              </fieldset>
            </div>
        </div>
  </div>

  <!-- Options Display Section -->

  <div style="float:left;width:800px;margin:20px 0 20px;">
  	<fieldset class="options">
  		<h3>Settings</h3>
  		  <table width="100%" cellpadding="5" class="editform">
  		    <tr>
  		      <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">
  		        <a onclick="toggleVisibility('ben_trans_request_tip');" title="Click for Help!" style="cursor: pointer;">
                <?php _e('Time between translation requests', 'bens-translator') ?>:
              </a>
            </th>
            <td>
	        	  <input size="4"  maxlength="5" name="bentr_conn_interval" type="text" id="bentr_conn_interval" value="<?php echo($bentr_conn_interval);?>"/> 
              <div id="ben_trans_request_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                <?php _e('This setting stops you getting banned from google translate default is 580, Time is in seconds', 'bens-translator') ?>
              </div>
            </td>
          </tr>
          
          
          <tr>
  		      <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">
	        	  <?php if (function_exists('gzcompress')){?>                  
                <a onclick="toggleVisibility('ben_trans_compress_tip');" title="Click for Help!" style="cursor: pointer;">
                  <?php _e('Enable cache compression', 'bens-translator') ?>:
                </a>
            </th>
            <td>
                <input name="bentr_compress_cache" type="checkbox" id="bentr_compress_cache" <?php if($bentr_compress_cache == TRUE) {?> checked="checked" <?php } ?> /> 
                <div id="ben_trans_compress_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                  <?php _e('this will strongly decrease the disk space but could give some problems on certain hosts). Disable this for plain text cache files.', 'bens-translator') ?>
                </div>
            </td>
          </tr>
          
          
          <tr>
            <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">  
						  <a onclick="toggleVisibility('ben_trans_schedule_tip');" title="Click for Help!" style="cursor: pointer;">
                  <?php _e('Schedule Cached pages for update after', 'bens-translator') ?>:
              </a>
						</th>
						<td>            
              <input size="4"  maxlength="5" name="bentr_cache_expire_time" type="text" id="bentr_cache_expire_time" value="<?php echo($bentr_cache_expire_time);?>"/> 
              <?php _e('day(s)', 'bens-translator') ?>
              <div id="ben_trans_schedule_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                <?php _e('This allows cached pages to expire and to be re-cached, "0" means "never"', 'bens-translator') ?>
              </div> 
            </td>
            <?php } else {?>
            <td>
              <input name="bentr_compress_cache" disabled="true" type="checkbox" id="bentr_compress_cache"/> Unable to provide cache compression feature: ZLIB not available on you php installation.</label>
            </td>
            <?php }?>
          </tr>
          
          
          <tr>
  		      <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">  
              <a onclick="toggleVisibility('ben_trans_spider_tip');" title="Click for Help!" style="cursor: pointer;">
                <?php _e('Block "bad" web spiders and crawlers', 'bens-translator') ?>
              </a>
            </th>
            <td>
              <input name="bentr_ban_prevention" type="checkbox" id="bentr_ban_prevention" <?php if($bentr_ban_prevention == TRUE) {?> checked="checked" <?php } ?> />   
              <div id="ben_trans_spider_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                <?php _e('This will check the header of the spider and will block many "bad" spiders from hammering your cache', 'bens-translator') ?>
              </div>
            </td>
          </tr>
          
          
          <tr>
            <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">
              <?php if (bentr_sitemap_plugin_detected()) { ?>
              <a onclick="toggleVisibility('ben_trans_sitemap_tip');" title="Click for Help!" style="cursor: pointer;">
                <?php _e('Enable sitemap integration', 'bens-translator') ?>
              </a>
            </th>
            <td> 
              <input name="bentr_sitemap_integration" type="checkbox" id="bentr_sitemap_integration" <?php if($bentr_sitemap_integration == TRUE) {?> checked="checked" <?php } ?> />                 
              <div id="ben_trans_sitemap_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                <?php _e('This will add all your pages to your sitemap. I recommend not enabling this untill your cache is fully built', 'bens-translator') ?>
              </div>
              <?php
                } else {?>
                    <?php _e('Enable sitemap integration', 'bens-translator') ?>:
                </th>
                <td>         
                  <?php _e('Google XML Sitemaps Generator, Not installed', 'bens-translator') ?><br />
        	       <?php _e('Please download, install and activate the', 'bens-translator') ?> "
                 <a target="_blank" href="http://www.arnebrachhold.de/projects/wordpress-plugins/google-xml-sitemaps-generator/">
                 <?php _e('Google XML Sitemaps Generator for WordPress to enable this feature', 'bens-translator') ?>
              <?php } ?>
              </td>
            </tr>
            
            
            <tr>
              <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">               
                <a onclick="toggleVisibility('ben_trans_debug_tip');" title="Click for Help!" style="cursor: pointer;">
                  <?php _e('Enable Debug', 'bens-translator') ?>:
                </a>
              </th>
              <td>
                <input name="bentr_enable_debug" type="checkbox" id="bentr_enable_debug" <?php if($bentr_enable_debug == TRUE) {?> checked="checked" <?php } ?> /> 
                <div id="ben_trans_debug_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                  <strong>"debug.log"</strong><?php _e(' file, which will be saved in the following directory:', 'bens-translator') ?> <br />
	        	      <strong><?php echo(dirname(__file__));?></strong>.<br />
                </div>   
              </td>
            </tr>
            
            
            <tr>
              <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">    
                <a onclick="toggleVisibility('ben_trans_table_tip');" title="Click for Help!" style="cursor: pointer;">      
                  <?php _e('Enclose the flags(links) inside a', 'bens-translator') ?>:
                </a>
              </th>
              <td>
                <label>
                  TABLE
                  <input type="radio" <?php if($bentr_html_bar_tag == 'TABLE') {?> checked="checked" <?php } ?> name="bentr_html_bar_tag" value="TABLE" />
                </label>
                <label>
                  DIV
                  <input type="radio" <?php if($bentr_html_bar_tag == 'DIV') {?> checked="checked" <?php } ?> name="bentr_html_bar_tag" value="DIV" />
                </label>
                <div id="ben_trans_table_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                  <?php _e('Changes the encapsulation code you want to use for your flag bar. This will allow for validation and CSS styling', 'bens-translator') ?> 
                </div>
              </td>
            </tr>
        
        
            <tr>
              <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">    
                <a onclick="toggleVisibility('ben_trans_table_tip_flag');" title="Click for Help!" style="cursor: pointer;">      
                  <?php _e('Show language links as', 'bens-translator') ?>:
                </a>
              </th>
              <td>
                <label>
                  Text
                  <input type="radio" <?php if($bentr_html_bar_flag == 'TEXT') {?> checked="checked" <?php } ?> name="bentr_html_bar_flag" value="TEXT" />
                </label>
                <label>
                  Flags
                  <input type="radio" <?php if($bentr_html_bar_flag == 'FLAG') {?> checked="checked" <?php } ?> name="bentr_html_bar_flag" value="FLAG" />
                </label>
                <div id="ben_trans_table_tip_flag" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                  <?php _e('Chooses whether you want the links in the translation bar to be plain text or flags', 'bens-translator') ?>
                </div>
              </td>
            </tr>
            
            <tr>
              <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">
                <a onclick="toggleVisibility('ben_trans_table_row_tip');" title="Click for Help!" style="cursor: pointer;">      
                  <?php _e('Show how many flags per row (table)', 'bens-translator') ?>:
                </a>
              </th>
              <td>
                <select name="bentr_col_num"></select>
                <div id="ben_trans_table_row_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                  <?php _e('This changes how many flags are shown per line TABLE only. For DIVs you need to CSS it.', 'bens-translator') ?>
                </div>
              </td>
            </tr>               
           
           
            <tr>
              <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">               
                <a onclick="toggleVisibility('ben_trans_translate_template_tip');" title="Click for Help!" style="cursor: pointer;">
                  <?php _e('Enable Translation Visitor Warning', 'bens-translator') ?>:
                </a>
              </th>
              <td>
                <input name="bentr_translate_template" type="checkbox" id="bentr_translate_template" <?php if($bentr_translate_template == TRUE) {?> checked="checked" <?php } ?> /> 
                <div id="ben_trans_translate_template_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                  <?php _e('This enables a warning bar to be displayed to visitors showing them that the page is translated', 'bens-translator') ?>
                </div>   
              </td>
            </tr>
            
            
             <tr>
              <th style="text-align: right; vertical-align: top;width:320px;padding:10px;" scope="row">               
                <a onclick="toggleVisibility('ben_trans_translate_validate_tip');" title="Click for Help!" style="cursor: pointer;">
                  <?php _e('Enable Validation Engine (BETA)', 'bens-translator') ?>:
                </a>
              </th>
              <td>
                <input name="bentr_translate_validate" type="checkbox" id="bentr_translate_validate" <?php if($bentr_translate_validate == TRUE) {?> checked="checked" <?php } ?> /> 
                <div id="ben_trans_translate_validate_tip" style="max-width: 600px; text-align: left; display: none; margin:5px;">
                  <?php _e('This enables a Validation engine to fix errors in html tags and will try to validate the page. Please disable if you are having strange outputs', 'bens-translator') ?>
                </div>   
              </td>
            </tr>
             
      
          </table>
      </fieldset>
  </div>
  
  <!-- Start Cache Information Section -->
  
  <div style="float:left;width:800px;margin:20px 0 20px;">
    <?php 
    /**
      * Loads Cache Information
      * @since 0.9
      */
    bentr_init_info(); 
    ?>
      <?php 
        $count_posts = wp_count_posts(); 
        $published_posts = $count_posts->publish;
        $count_pages = wp_count_posts('page');
        $published_pages = $count_pages->publish;
        $total_languages = count ($bentr_preferred_languages);
        
        $total_translate = ($published_posts + $published_pages) * $total_languages;
        
        $total_pages = $total_translate;
      ?>
	    <h3>Statistics</h3>
	      <ul>
          <li><?php _e('Estimated total of pages to translate:', 'bens-translator') ?><strong><?php echo ($total_pages);?></strong></li>
	        <li><?php _e('Your cache directory currently contains', 'bens-translator') ?> <strong><?php echo($bentr_cached_files_num)?></strong> <?php _e('successfully translated and cached pages.', 'bens-translator') ?></li>
	        <li><strong><?php _e('Cache directory size', 'bens-translator') ?></strong>: <?php $size=round($bentr_cache_size/1024/1024,1); echo ($size);?> MB</li>
	        <li><?php _e('Your stale directory currently contains', 'bens-translator') ?> <strong><?php echo($bentr_stale_files_num)?></strong> <?php _e('successfully translated and cached pages waiting for a new translation.', 'bens-translator') ?></li>
	        <li><strong><?php _e('Stale directory size', 'bens-translator') ?></strong>: <?php $size=round($bentr_stale_size/1024/1024,1); echo ($size);?> MB</li>
	      </ul>
	      <ul style="margin-top:30px;">
						<?php
	        	//$diff_time = bentr_get_last_cached_file_time();
						if ($diff_time > 0){
		        	echo ("<li>Latest allowed connection to the translation engine: <strong>");
	    	      if ($diff_time < 60){
				      	echo (round(($diff_time)) . " second(s) ago</strong>");
	      			}
              else if ($diff_time > 60*60){
	      				echo (round(($diff_time)/3600) . " hour(s) ago</strong>");    
		      		}
              else{
		      			echo (round(($diff_time)/60) . " minute(s) ago</strong>");
		      		}
		      		global $bentr_last_cached_url;
		      		if (strlen($bentr_last_cached_url)>0){
		      			echo (". [<a target='_blank' href='$bentr_last_cached_url'>See latest translated page</a>]");
		      		}
						} 
            else {
							echo ("<li>Latest allowed connection to the translation engine: <strong>not available</strong>");
						}
						echo ("</li>");
						echo ("<li><strong>Translations status</strong>:");	
						$ban_status = get_option("bentr_translation_status");					
						if ($ban_status == 'banned'){
							echo("<strong><font color='red'>Bad or unhandled response from the '".strtoupper(get_option('bentr_my_translation_engine'))."' translation engine.</font></strong> This could mean that:
							<ul><li>Your blog has been temporarily banned: increase the time interval between the translation requests and wait for some days or switch to another translation engine</li>
							<li>The translation engine is currently not responding/working: wait for some days or switch to another translation engine</li>
							<li>The translation engine has changed something (i.e. the translation url): wait for the next release of Bens-Translator :-)</li>
							<li>You haven't added the flags widget on your pages: adding the flags bar is mandatory in order to make Bens-Translator able to work correctly</li></font>");
						} else if ($ban_status == 'working'){
							echo("<strong><font color='green'>Working properly</font></strong>");
						} else {
							echo("<strong>not available</strong>");
						}
						echo ("</li>");
	        	?>
	        </ul>
	  </div>
	  
	  <!-- Submit Form Button Section -->
    
    <div style="float:left;width:800px;margin:20px 0 20px;">
      <p class="submit">
        <input type="submit" name="bentr_save" value="<?php _e('Update options', 'bens-translator') ?> &raquo;" />
      </p>
    </div>
    
  </form>
</div>
<?php
if (!is_numeric($bentr_col_num))$bentr_col_num = 0;
bentr_build_js_function($bentr_base_lang, $bentr_col_num);
?>