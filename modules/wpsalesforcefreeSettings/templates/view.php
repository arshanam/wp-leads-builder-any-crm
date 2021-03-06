<?php
 $config = get_option("wp_{$skinnyData['activatedplugin']}_settings");

	if(isset( $_REQUEST['code'] ) && ($_REQUEST['code'] != '') )
	{
		include_once(WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY."/lib/SmackSalesForceApi.php");
		$response = Getaccess_token( $config , $_GET['code']);
		$access_token = $response['access_token'];
		$instance_url = $response['instance_url'];
		if (!isset($access_token) || $access_token == "") {
		    die("Error - access token missing from response!");
		}
		if (!isset($instance_url) || $instance_url == "") {
		    die("Error - instance URL missing from response!");
		}
		$_SESSION['access_token'] = $access_token;
		$_SESSION['instance_url'] = $instance_url;
		$config['access_token'] = $access_token;
		$config['instance_url'] = $instance_url;
		$config['id_token'] = $response['id_token'];
		$config['signature'] = $response['signature'];
/*
echo "<pre>";
	print_r($response);
	print_r($config);
echo("</pre>");
*/
	}
	$siteurl = site_url(); 
	update_option("wp_{$skinnyData['activatedplugin']}_settings" , $config );
?>
<form id="smack-salesforce-settings-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<h3>Salesforce CRM Settings</h3>
	<img src="<?php echo WP_CONST_ULTIMATE_CRM_CPT_DIR; ?>images/loading-indicator.gif" id="loading-image" style="display: none; position:relative; left:500px;padding-top: 5px; padding-left: 15px;">

	<input type="hidden" name="smack-salesforce-settings-form" value="smack-salesforce-settings-form" />
	<input type="hidden" id="plug_URL" value="<?php echo WP_CONST_ULTIMATE_CRM_CPT_PLUG_URL;?>" />
	<div class="wp-common-crm-content" style="width: 600px;float: left;">
	<table>
		<tr>
			<td><label style="font-weight:bold;">Select Plugin</label></td>
			<td>
			<?php
				$ContactFormPluginsObj = new ContactFormPlugins();
				echo $ContactFormPluginsObj->getPluginActivationHtml();
			?>
			</td>
		</tr>
	</table>
	<table  class="settings-table">
		<tr><td><br/></td></tr>
		<tr>
			<td style='width:160px;'>
				<label> Consumer Key  </label><div style='float:right;'> : </div>
			</td>
			<td>
				<input type='text' class='smack-salesforce-free-settings-text' name='key' id='smack_host_address' value="<?php echo $config['key'] ?>"/>

			</td>
		</tr>
		<tr>
			<td style='width:160px;'>
				<label> Consumer Secret </label><div style='float:right;'> : </div>
			</td>
			<td>
				<input type='password' class='smack-salesforce-free-settings-text' name='secret' id='smack_host_username' value="<?php echo $config['secret'] ?>"/>

			</td>
		</tr>
		<tr>
			<td style='width:160px;'>
				<label> Callback URL </label><div style='float:right;'> : </div>
			</td>
			<td>
				<input type='text' class='smack-salesforce-free-settings-text' name='callback' id='smack_host_access_key' value="<?php echo $config['callback'] ?>"/>
			</td>
		</tr>

		<tr>
			<td>
			</td>
		</tr>
		<tr>
			<td style='width:160px;'>
				<label><div style='float:left;'> Capture Registering User  </div></label>
			</td>
			<td>
				<input type='checkbox' class='smack-salesforce-settings-text' name='user_capture' id='user_capture' value="on" <?php if(isset($config['user_capture']) && $config['user_capture'] == 'on') { echo "checked=checked"; } ?>/>
			</td>
		</tr>


	</table>
	<br/>
<h5  style="font-weight:bold;">Email Notification</h5>
<table>
                <tr>
                        <td style='width:160px;'>
                                <label><div style='float:left;'> Email All Captured Data</div></label>
                        </td>
                        <td>
                                <input type='checkbox' class='smack-salesforce-settings-text' name='smack_email' id='smack_email' value="on" <?php if(isset($config['smack_email']) && $config['smack_email'] == 'on') { echo "checked=checked"; } ?>/>
                        </td>
                </tr>
		 <tr><td><br></td></tr>
                <tr>
                        <td style='width:160px;'>
                                <label><div style='float:left;'> Email Id </div></label>
                        </td>
                        <td>
                     <input type='text' class='smack-salesforce-free-settings-text' name='email' id='email' value="<?php echo $config['email'] ?>"/>
                        </td>
                </tr>  
</table> 
		<tr><td><br></td></tr>
<table>
	<h5  style="font-weight:bold;">Debug Mode</h5>
		 <tr>
                                <td style='width:160px;'>
                                        <label><div style='float:left;'> Enable Debug Mode  </div></label>
                                </td>
                                <td>
                                        <input type='checkbox' class='smack-vtiger-settings-text' name='debug_mode' id='debug_mode' value="on" <?php if(isset($config['debug_mode']) && $config['debug_mode'] == 'on') { echo "checked=checked"; } ?>/>
                                </td>
                </tr>


        </table>
        <br/>

<!--
	<h3>Google reCAPTCHA Settings</h3>
	<table>
		<tr style="display:block;">
			<td>
				<label>Do you need captcha to visible : </label>
			</td>
			<td>
				<input type='radio' class='smack-salesforce-settings-radio-captcha' name='smack_recaptcha' id='smack_recaptcha_no'  value="no"
	<?php
	if($config['smack_recaptcha']=='no' || !isset($config['smack_recaptcha']))
	{
		echo "checked";
	}
	?>
	 onclick="showOrHideRecaptcha('no');"> No
				<input type='radio' class='smack-salesforce-settings-radio-captcha' name='smack_recaptcha' id='smack_recaptcha_yes'  value="yes" 
	<?php
	if($config['smack_recaptcha']=='yes')
	{
		echo "checked";
	}
	?>
	 onclick="showOrHideRecaptcha('yes');"> Yes
			</td>
		</tr>

		<tr id="recaptcha_public_key"
	<?php
	if($config['smack_recaptcha']=='no' || !isset($config['smack_recaptcha']))
	{
		echo 'style="display:none"';
	}
	else
	{
		echo 'style="display:block"';
	}
	?>
	>
			<td style='width:100px;'>
				<label> Recaptcha Public Key  : </label>
			</td>
			<td>
				<input type='text' class='smack-salesforce-free-settings-text' name='recaptcha_public_key' id='smack_public_key' value="<?php echo $config['recaptcha_public_key'] ?>" placeholder="Enter recaptcha public key here"/>
			</td>
		</tr>
		<tr id="recaptcha_private_key" <?php
	if($config['smack_recaptcha']=='no' || !isset($config['smack_recaptcha']))
	{
		echo 'style="display:none"';
	}
	else
	{
		echo 'style="display:block;"';
	}
	?>
	>
			<td style='width:100px;'>
				<label> Recaptcha Private Key : </label>
			</td>
			<td>
				<input type='text' class='smack-salesforce-free-settings-text' name='recaptcha_private_key' id='smack_private_key' value="<?php echo $config['recaptcha_private_key'] ?>" placeholder="Enter recaptcha private key here"/>
			</td>
		</tr>
	</table>
-->
	<table>
		<tr>
			<td>
				<input type="hidden" name="posted" value="<?php echo 'posted';?>">

				<p class="submit">


					<input type="submit" value="<?php _e('Save Settings');?>" class="button-primary" onclick="document.getElementById('loading-image').style.display = 'block'"/>

<?php 

$auth_url =  "https://login.salesforce.com/services/oauth2/authorize?response_type=code&client_id=" . $config['key'] . "&redirect_uri=" . urlencode($config['callback']); 


?>

<a href="<?php echo "$auth_url"?>" ><input name="submit" type="button" value="Authenticate" class="button-primary" /> </a>

				</p>
			</td>
		</tr>
	</table>
	</div>
	<!--
	<div id="wp-salesforce-free-video">
		<span style="padding-top: 10px; font-size: 14px; padding-bottom: 20px; font-weight: bold;">How to configure WP salesforce free</span>
		<iframe width="560" height="315" src="http://www.youtube.com/embed/AE6MvSQuubg?list=PL2k3Ck1bFtbQnYh2ak-jM7fYyo0kzMlv-" frameborder="0" allowfullscreen></iframe>
	</div>
	-->
</form>
