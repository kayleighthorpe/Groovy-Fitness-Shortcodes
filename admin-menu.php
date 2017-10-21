<?php

/** Step 2 (from text above). */
add_action( 'admin_menu', 'fitbit_plugin' );

/** Step 1. */
function fitbit_plugin() {
	add_options_page( 'My Plugin Options', 'Groovy Fitness Shortcodes', 'manage_options', 'groovy-fitness-shortcodes', 'my_plugin_options' );
}


/** Step 3. */
function my_plugin_options() {

?>

	<h1>Fitbit Settings</h1>
	



<hr>

	<form method='post' action='options.php'>
	
<?php settings_fields( 'fitbit-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'fitbit-plugin-settings-group' ); ?>
<hr><p>
		<h3>Fitbit App Details</h3>
</p>
<hr>

		<div class='form-padding'>
		<table class='form-table'>
		
					<tr valign='top'>
			<th scope='row'>Fitbit User ID:</th>
			<td>
				<input type='text' name='fitbituserid' value='<?php echo get_option('fitbituserid'); ?>' />
			</td>
			</tr>
			
			<tr valign='top'>
			<th scope='row'>Client ID:</th>
			<td>
				<input type='text' name='fitbitcliid' value='<?php echo get_option('fitbitcliid'); ?>' />
			</td>
			</tr>
			 
			<tr valign='top'>
			<th scope='row'>Client consumer Secret:</th>
			<td>
				<input type='text' name='fitbitsecret' value='<?php echo get_option('fitbitsecret'); ?>' />
			</td>
			</tr>
			
						<tr valign='top'>
			<th scope='row'>Redirect URI:</th>
			<td>
				<input type='text' name='fitbitcallback' value='<?php echo get_option('fitbitcallback'); ?>' />
			</td>
			</tr>

		</table> <!-- .form-table -->
		<p>
			<strong>Setup Guide:</strong>
			<ol>
				<li>Obtain your Fitbit User ID by logging into <a href='https://fitbit.com/' target="_blank">fitbit.com</a>. </li>
				<li>Click on your avatar in the top right-hand corner and you will see a Public URL like this one: www.fitbit.com/user/YOURID - copy and paste your ID into the first field. </li>
				<li>Sign up as a FitBit Developer at <a href='https://dev.fitbit.com/' target="_blank">dev.fitbit.com</a>.</li>
				<li>Click "Register a new app"</li>
				<li>Enter the basic description and your site's web address.</li>
				<li>Set your "redirect_uri" to <?php echo admin_url('options-general.php?page=groovy-fitness-shortcodes') ?> in the Fitbit App.</li>
				<li>Set the "OAuth 2.0 Application Type" type to "Server"</li>
				<li>Set the "Default Access Type" to "Read-Only", and save </li>
				<li>Paste your Client OAuth2 ID/Secret provided by FitBit into the fields above, then click the Save all settings button.</li>
				<li>Set your "redirect_uri" to <?php echo admin_url('options-general.php?page=groovy-fitness-shortcodes') ?> in the fields above.</li>
				<li>Save your settings then click the link to authorise your app.</li>
			</ol>
		</p>
		<?php submit_button('Save all settings') ?>
	</form>

	<?php

$fitbitid = get_option('fitbituserid');
$clientid = get_option('fitbitcliid');
$secret = get_option('fitbitsecret');
$callback = get_option('fitbitcallback');
$auth = base64_encode("$clientid:$secret");


	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	
?>

<hr>
<p>

<a href="<?php

echo 'https://www.fitbit.com/oauth2/authorize?response_type=code&client_id=', $clientid, '&redirect_uri=', $callback, '&scope=activity%20heartrate%20location%20nutrition%20profile%20settings%20sleep%20social%20weight&expires_in=604800';

?>">Click here after saving your settings to authorise your app with Fitbit.</a> 
</p>
<hr>
	


<?php


	
if (isset($_GET['code'])) {
    $grabthecode = $_GET['code'];
}else{
    // Fallback behaviour goes here
}
	
// echo $grabthecode;



?>



<P>




<?php



/** Curl script to take the code and use it to pull an access token */
	

$token_url = "https://api.fitbit.com/oauth2/token";
$access_token_settings = array(
				'grant_type' => "authorization_code",
				'code' =>  $grabthecode,
				'redirect_uri' => $callback,
				'client_id' =>  $clientid,
				'client_secret' => $secret
				);

        $auth_headers = array("Content-Type: application/x-www-form-urlencoded", "Authorization: Basic {$auth}");
	
        $ch = curl_init($token_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $auth_headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($access_token_settings));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

       
/** Decode the output of the curl and print the response  */

$autharray = json_decode($response, true);



/** Just grab the access token  */

//echo $autharray['access_token'];
update_option('fitbitaccesstoken', $autharray['access_token']);
update_option('fitbitrefreshtoken', $autharray['refresh_token']);


	
}








?>
