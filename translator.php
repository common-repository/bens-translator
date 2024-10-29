<?php
/*
Plugin Name: Bens Translator
Plugin URI: http://benosullivan.co.uk/bens-translator/
Description: Automatically translates a blog in 19 different languages supported by analytics using the Google Translation Engine. After uploading this plugin click 'Activate' (to the left) and then afterwards you must <a href="options-general.php?page=bens-translator/core/options-translator.php">visit the options page</a>.
Version: 1.7.2
Author: Ben O'Sullivan
Author URI: http://benosullivan.co.uk/
Disclaimer: Use at your own risk. No warranty expressed or implied is provided. The author will never be liable for any loss of profit, physical or psychical damage, legal problems. The author disclaims any responsibility for any action of final users. It is the final user's responsibility to obey all applicable local, state, and federal laws.

*/

/*  Copyright 2009  Ben O'Sullivan (email : contact@benosullivan.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Sets hook for Activation and Deactivation of plugin
 * @since 0.9
 */
register_activation_hook(__FILE__, 'bentr_activation');
register_deactivation_hook(__FILE__, 'bentr_deactivation');

/**
 * Creates Fresh Options in DB
 * @since 0.9
 * @updated 1.3 
 */
function bentr_activation(){
  bentr_debug("bentr_plugin_activate :: plugin activation");
        $bentr_col_num = "7";
        add_option('bentr_html_bar_flag','FLAG');
        add_option('bentr_base_lang','en');
	      add_option('bentr_col_num', "$bentr_col_num");
	      add_option('bentr_html_bar_tag','DIV');
	      add_option('bentr_my_translation_engine','google' );
	      add_option('bentr_preferred_languages',array());
				add_option("bentr_last_connection_time",'');
				add_option("bentr_translation_status",'unknown');
	      add_option('bentr_conn_interval','580');
	      add_option('bentr_cache_expire_time','30');
	      add_option('bentr_ban_prevention',true);
	      add_option('bentr_sitemap_integration',false);
	      add_option('bentr_compress_cache',false);
	      add_option('bentr_enable_debug',false);
	      add_option('bentr_translate_template',true);
	      add_option('bentr_current_permalink', '');
        add_option('bentr_translate_validate', true);	      
}

/**
 * Removes all options in DB on deactivation
 * @since 0.9
 */
function bentr_deactivation() {
  bentr_debug("bentr_plugin_activate :: plugin deactivation");
        delete_option('bentr_base_lang');
	      delete_option('bentr_col_num');
	      delete_option('bentr_html_bar_tag');
	      delete_option('bentr_my_translation_engine');
	      delete_option('bentr_preferred_languages');
				delete_option("bentr_last_connection_time");
				delete_option("bentr_translation_status");
	      delete_option('bentr_conn_interval');
	      delete_option('bentr_cache_expire_time');
	      delete_option('bentr_ban_prevention');
	      delete_option('bentr_sitemap_integration');
	      delete_option('bentr_compress_cache');
	      delete_option('bentr_enable_debug');
	      delete_option('bentr_translate_template');
	      delete_option('bentr_html_bar_flag');
	      delete_option('bentr_current_permalink');
	      delete_option('bentr_translate_validate', true);	   
}

// Create Cache Folder, Recreate if missing
	$bentr_cache_dir = WP_CONTENT_DIR . "/ben-translate-cache";
  IF (!file_exists($bentr_cache_dir)){
    mkdir($bentr_cache_dir);
  }

$bentr_result = '';

if (SITEMAP_INTEGRATION) add_action("sm_buildmap","bentr_add_translated_pages_to_sitemap");
add_action('plugins_loaded', 'widget_bens_translator_init');

// Include Required Files 
require_once (dirname(__file__).'/core/settings.php');
require_once (dirname(__file__).'/core/functions.php');
//require_once (dirname(__file__).'/core/cron.php');

$bentr_engine = $bentr_available_engines[TRANSLATION_ENGINE];
?>