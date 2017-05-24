<?php
// Custom Banners Welcome Page template

ob_start();
$learn_more_url = 'https://goldplugins.com/special-offers/upgrade-to-wp-social-pro/?utm_source=wp_social_free&utm_campaign=welcome_screen_upgrade&utm_content=col_1_learn_more';$app_id_secret_key_link = 'https://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/?utm_source=wp_social_free&utm_campaign=welcome_screen_upgrade&utm_content=col_1_learn_more';
$pro_registration_url = menu_page_url('ikfb_configuration_options', false) . '#tab-registration-settings';
$settings_url = menu_page_url('ikfb_configuration_options', false);
$utm_str = '?utm_source=wp_social_free&utm_campaign=welcome_screen_help_links';
$utm_str_2 = '?utm_source=wp_social_free&utm_campaign=welcome_screen_upgrade_section';
?>

<p class="aloha_intro"><strong>Thank you for installing <?php echo $plugin_title; ?>!</strong> This page is here to help you get up and running. If you're already familiar with <?php echo $plugin_title; ?>, you can skip it and <a href="<?php echo $settings_url; ?>">continue to the Basic Settings page</a>. <?php if (!$is_pro): ?>You may also wish to <a href="<?php echo $pro_registration_url; ?>">enter your API key to upgrade your plugin</a>.<?php endif;?></p>
<p class="aloha_tip"><strong>Tip:</strong> You can always access this page via the <strong>WP Social Settings &raquo; About Plugin</strong> menu.</p>
<h1>Helpful Links</h1>
<div class="three_col">
	<div class="col">
		<?php if ($is_pro): ?>
			<h3>WP Social Pro: Active</h3>
			<p class="plugin_activated">WP Social Pro is licensed and active.</p>
			<a href="<?php echo $pro_registration_url; ?>">Registration Settings</a>
		<?php else: ?>
			<h3>Upgrade To Pro</h3>
			<p>WP Social Pro is the Professional, fully-functional version of WP Social, which features technical support and access to all features and themes.</p>
			<a class="button" href="<?php echo $learn_more_url; ?>">Click Here To Learn More</a>
			<br>
			<br>
			<p><strong>Already upgraded to WP Social Pro?</strong></p>
			<a href="<?php echo $pro_registration_url; ?>">Click here to enter your WP Social Pro API Key</a>			
		<?php endif; ?>
	</div>
	<div class="col">
		<h3>Getting Started</h3>
		<ul>
			<li><a href="<?php echo $app_id_secret_key_link; ?>">Click Here To Get An App ID and Secret Key From Facebook</a></li>
			<li><a href="https://goldplugins.com/documentation/wp-social-pro-documentation/configuration-options-and-instructions/<?php echo $utm_str; ?>">Getting Started With <?php echo $plugin_title; ?></a></li>
			<li><a href="https://goldplugins.com/documentation/wp-social-pro-documentation/frequently-asked-questions/<?php echo $utm_str; ?>">Frequently Asked Questions (FAQs)</a></li>
			<li><a href="https://goldplugins.com/contact/<?php echo $utm_str; ?>">Contact Technical Support</a></li>
		</ul>
	</div>
	<div class="col">
		<h3>Further Reading</h3>
		<ul>
			<li><a href="https://goldplugins.com/documentation/wp-social-pro-documentation/<?php echo $utm_str; ?>"><?php echo $plugin_title; ?> Documentation</a></li>
			<li><a href="https://wordpress.org/support/plugin/ik-facebook<?php echo $utm_str; ?>">WordPress Support Forum</a></li>
			<li><a href="https://goldplugins.com/documentation/wp-social-pro-documentation/wp-social-pro-changelog/<?php echo $utm_str; ?>">Recent Changes</a></li>
			<li><a href="https://goldplugins.com/<?php echo $utm_str; ?>">Gold Plugins Website</a></li>
		</ul>
	</div>
</div>

<div class="continue_to_settings">
	<p><a href="<?php echo $settings_url; ?>">Continue to Basic Settings &raquo;</a></p>
</div>

<?php 
$content =  ob_get_contents();
ob_end_clean();
return $content;
?>