<?php
/**
 * Cron Script
 * Action and hooks to be moved here TODO 
 * @since 1.1
 */
//btr_cron_send(); //Jumps in to test code (Remove for production)
//wp_unschedule_event( '1256593621', 'btr_cron_send_hook' );

//Add filter to create custom time for Cron
add_filter('cron_schedules', 'more_reccurences');
//Add action for Cron Job
add_action('bentr_cron_hook', 'bentr_cron_send');

/**
 * Creates a cron job using custom time
 * Action and hooks to be moved here TODO 
 * @since 1.1
 */
function bentr_create_cron (){
  //Add line to debug showing cron ran
  bentr_debug("bentr_plugin_activate :: cron function ran");
  //Add cron event to wordpress cron
  wp_schedule_event(time(), 'bentr_cron_custom' , 'bentr_cron_hook' );
}

/**
 * Creates a custom interval for the cron to run
 * Action and hooks to be moved here TODO 
 * @since 1.1
 */
function more_reccurences($schedules) {
  //Gets connection interval from database
  $bentr_conn_interval = get_option('bentr_conn_interval');
  //Adds ten seconds to interval to stop collision
  $bentr_conn_interval_plus = $bentr_conn_interval + "10";
  //Create custom timing array
	$schedules['bentr_cron_custom'] = array('interval' => $bentr_conn_interval, 'display' => 'Bens Translator Custom');
	return $schedules;
}

// Function for the actual Cron
function bentr_cron_send() {
  $bentr_cron_date = date("d/m/y : H:i:s", time());
  bentr_debug("bentr_cron_send :: Cron Job Fired, $bentr_cron_date");
}


?>