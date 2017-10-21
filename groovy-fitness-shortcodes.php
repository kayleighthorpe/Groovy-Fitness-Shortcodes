<?php

   /*
   Plugin Name: Groovy Fitness Shortcodes by Albinofruit.com
   Plugin URI: http://albinofruit.com/groovy-fitness-shortcodes
   Description: A plugin to put your Fitbit data into handy shortcodes.
   Version: 1.0
   Author: Kay
   Author URI: http://albinofruit.com
   License: GPL2
   */

include 'admin-menu.php';


register_activation_hook(__FILE__, 'fitcodes_activation');

function fitcodes_activation() {
    if (! wp_next_scheduled ( 'fitbit_grab_steps' )) {
	wp_schedule_event(time(), '1min', 'fitbit_grab_steps');
    }
    
    if (! wp_next_scheduled ( 'fitbit_use_refresh_token' )) {
	wp_schedule_event(time(), '30min', 'fitbit_use_refresh_token');
    }
    
}


register_deactivation_hook(__FILE__, 'my_deactivation');

function my_deactivation() {
	wp_clear_scheduled_hook('fitbit_grab_steps');
	wp_clear_scheduled_hook('fitbit_use_refresh_token');
}


	add_action( 'admin_init', 'register_my_fitbit_plugin_settings' );
	add_action( 'admin_init', 'register_my_fitbit_plugin_token' );


function register_my_fitbit_plugin_settings() {
	//register our settings
	register_setting( 'fitbit-plugin-settings-group', 'fitbituserid' );
	register_setting( 'fitbit-plugin-settings-group', 'fitbitcliid' );
	register_setting( 'fitbit-plugin-settings-group', 'fitbitsecret' );
	register_setting( 'fitbit-plugin-settings-group', 'fitbitcallback' );
	register_setting( 'fitbit-plugin-settings-group', 'fitbitaccesstoken' );
}


function register_my_fitbit_plugin_token() {
	//register our settings
	register_setting( 'fitbit-plugin-token-group', 'fitbitaccesstoken' );
		register_setting( 'fitbit-plugin-token-group', 'fitbitrefreshtoken' );
}

function my_cron_schedules($schedules){
    if(!isset($schedules["1min"])){
        $schedules["1min"] = array(
            'interval' => 1*60,
            'display' => __('Once every 1 minute'));
    }
    if(!isset($schedules["30min"])){
        $schedules["30min"] = array(
            'interval' => 30*60,
            'display' => __('Once every 30 minutes'));
    }
    return $schedules;
}
add_filter('cron_schedules','my_cron_schedules');




add_action( 'fitbit_grab_steps', 'fitness_task_function' );

function fitness_task_function() { 

// access token grabbing

$fitbitidforurl = get_option('fitbituserid');


$url = "https://api.fitbit.com/1/user/$fitbitidforurl/activities/date/today.json";

$access_token = get_option('fitbitaccesstoken');
$ref_token = get_option('fitbitrefreshtoken');

        $auth_header = array("Authorization: Bearer $access_token");

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);            
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $auth_header);  

$result = curl_exec($curl);
        curl_close($curl);

$jsondir = WP_PLUGIN_DIR."/groovy-fitness-shortcodes/json.json";

file_put_contents($jsondir, $result);


}


add_action( 'fitbit_use_refresh_token', 'refresh_function' );

function refresh_function() { 

// Refresh token code

$secretref = get_option('fitbitsecret');
$clientidref = get_option('fitbitcliid');
$authref = base64_encode("$clientidref:$secretref");

$url2 = "https://api.fitbit.com/oauth2/token";

$ref_token_use = get_option('fitbitrefreshtoken');


$ref_token_settings = array(
				'grant_type' => "refresh_token",
				'refresh_token' => $ref_token_use,
				'expires_in' => "28800"
				);

        $auth_header2 = array("Content-Type: application/x-www-form-urlencoded", "Authorization: Basic $authref");

        $refcurl = curl_init($url2);
        curl_setopt($refcurl, CURLOPT_HEADER, false);
        curl_setopt($refcurl, CURLOPT_POST, true);
        curl_setopt($refcurl, CURLOPT_POSTFIELDS, http_build_query($ref_token_settings));
        curl_setopt($refcurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($refcurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($refcurl, CURLOPT_HTTPHEADER, $auth_header2);

$refresult = curl_exec($refcurl);
        curl_close($refcurl);

$autharrayref = json_decode($refresult, true);

update_option('fitbitaccesstoken', $autharrayref['access_token']);
update_option('fitbitrefreshtoken', $autharrayref['refresh_token']);



}

// Shortcodes

function stepshortcode() {

$dir = WP_PLUGIN_DIR."/groovy-fitness-shortcodes/json.json";	

$json = file_get_contents($dir);

$obj = json_decode($json, true);

return $obj['summary']['steps'];	


}
add_shortcode( 'fitbitsteps', 'stepshortcode' );


function stepsumshortcode() {

$dirsum = WP_PLUGIN_DIR."/groovy-fitness-shortcodes/json.json";	

$jsonsum = file_get_contents($dirsum);

$objsum = json_decode($jsonsum, true);

return $objsum['goals']['steps'];	


}
add_shortcode( 'fitbitsteps-goal', 'stepsumshortcode' );


?>
