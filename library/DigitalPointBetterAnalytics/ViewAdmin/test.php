<?php
	wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

	$betterAnalyticsOptions = get_option('better_analytics');

	if (DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->checkAccessToken(false))
	{
		$checks['profiles'] = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getProfiles();
		$checks['matchingProfile'] = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getProfileByPropertyId($betterAnalyticsOptions['property_id']);

		$checks['siteSearchSetup'] = @$checks['matchingProfile']['siteSearchQueryParameters'] == 's';
		$checks['ecommerceTracking'] = @$checks['matchingProfile']['eCommerceTracking'];
		$checks['enhancedEcommerceTracking'] = @$checks['matchingProfile']['enhancedECommerceTracking'];

		$property = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getPropertyByPropertyId(@$checks['matchingProfile']['accountId'], $betterAnalyticsOptions['property_id']);

		$checks['level'] = @$property['level'];
		$checks['industryVertical'] = @$property['industryVertical'];

		$checks['dimensions'] = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getDimensionsByPropertyId(@$checks['matchingProfile']['accountId'], $betterAnalyticsOptions['property_id'], array('Categories', 'Author', 'Tags', 'User'));
	}

	$checks['licensed'] = DigitalPointBetterAnalytics_Helper_Api::check(true);

//	$checks['user_dimension_set'] = (XenForo_Application::getOptions()->dpBetterAnalyticsDimensionIndexUser > 0 && @$checks['dimensions']['User']['index'] == XenForo_Application::getOptions()->dpBetterAnalyticsDimensionIndexUser);
//	$checks['forum_dimension_set'] = (XenForo_Application::getOptions()->dpBetterAnalyticsDimentionIndex > 0 && @$checks['dimensions']['Forum']['index'] == XenForo_Application::getOptions()->dpBetterAnalyticsDimentionIndex);

?>

<div class="wrap">

	<h2><?php esc_html_e( 'Test Analytics Integration' , 'better-analytics');?></h2>

	<table class="form-table" id="ba_test">
		<?php esc_html_e( 'This is a checklist of things for full integration with Google Analytics. You can click on any of the titles to be taken where you need to go to configure that item.' , 'better-analytics');?>

		<tr><td colspan="3"><h3><?php esc_html_e( 'Things That Can Be Automatically Checked' , 'better-analytics');?></h3></td></tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#general', __('Web Property ID Defined', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['property_id'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://php.net/manual/en/curl.installation.php', __('cURL Installed', 'better-analytics'));?></th>

				<?php
					echo (function_exists('curl_multi_init') ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Not required, but recommended to have the cURL extensions enabled in PHP.', 'better-analytics');?>
			</td>
		</tr>

		<?php
		// not really needed since most people will not use their own project credentials
		/*
		?>
		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#api', __('API Project Credentials', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['api']['client_id'] && $betterAnalyticsOptions['api']['client_secret'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td>
			</td>
		</tr>
		*/
		?>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#api', __('Google Analytics Account Linked', 'better-analytics'));?></th>
				<?php
					echo (get_option('ba_tokens') ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#api', __('Analytics Profile Selected', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['api']['profile'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Site Search Setup', 'better-analytics'));?></th>
				<?php
					echo ($checks['siteSearchSetup'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>View Settings -&gt; Query Parameter</b> (should be set to "<b>s</b>").', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Ecommerce Tracking Enabled', 'better-analytics'));?></th>
				<?php
					echo ($checks['ecommerceTracking'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Ecommerce Settings</b>.', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Enhanced Ecommerce Tracking Enabled', 'better-analytics'));?></th>
				<?php
					echo ($checks['enhancedEcommerceTracking'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Ecommerce Settings</b>.', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Industry Vertical Set', 'better-analytics'));?></th>
				<?php
					echo ($checks['industryVertical'] && $checks['industryVertical'] != 'UNSPECIFIED' ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php echo sprintf(__('Found in Google Analytics account under <b>Property Settings</b> (currently set to <b>%s</b>).', 'better-analytics'), ($checks['industryVertical'] ? $checks['industryVertical'] : 'UNSPECIFIED'));?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Custom Dimension For Category Tracking', 'better-analytics'));?></th>
				<?php
					echo ($checks['dimensions']['Categories'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Custom Definitions -&gt; Custom Dimensions</b> (should be named "<b>Categories</b>" and scoped for "<b>Hit</b>").', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Custom Dimension For Author Tracking', 'better-analytics'));?></th>
				<?php
					echo ($checks['dimensions']['Author'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Custom Definitions -&gt; Custom Dimensions</b> (should be named "<b>Author</b>" and scoped for "<b>Hit</b>").', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Custom Dimension For Tag Tracking', 'better-analytics'));?></th>
				<?php
					echo ($checks['dimensions']['Tags'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Custom Definitions -&gt; Custom Dimensions</b> (should be named "<b>Tags</b>" and scoped for "<b>Hit</b>").', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Custom Dimension For User Tracking', 'better-analytics'));?></th>
				<?php
					echo ($checks['dimensions']['User'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Custom Definitions -&gt; Custom Dimensions</b> (should be named "<b>User</b>" and scoped for "<b>Hit</b>").', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', __('Category Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['dimension']['category'] > 0 && @$checks['dimensions']['Categories']['index'] == $betterAnalyticsOptions['dimension']['category'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', __('Author Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['dimension']['author'] > 0 && @$checks['dimensions']['Author']['index'] == $betterAnalyticsOptions['dimension']['author'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', __('Tag Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['dimension']['tag'] > 0 && @$checks['dimensions']['Tags']['index'] == $betterAnalyticsOptions['dimension']['tag'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', __('Registered User Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['dimension']['user'] > 0 && @$checks['dimensions']['User']['index'] == $betterAnalyticsOptions['dimension']['user'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_test&utm_medium=wordpress&utm_campaign=plugin', __('Better Analytics Pro License', 'better-analytics'));?></th>
			<?php
				echo ($checks['licensed'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
			?>
			<td class="description"><?php printf(__('A few extra advanced features are available when you license the Better Analytics Pro plugin.  Please don\'t steal, a crazy amount of work went into this.  Some of the extra features:
				<ul>
					<li>More metrics available for <a href="%s" target="_blank">Heatmaps</a></li>
					<li>More dimensions available for <a href="%s" target="_blank">Charts</a></li>
					<li>More <a href="%s" target="_blank">advertising platform click tracking</a></li>
					<li>More <a href="%s" target="_blank">issue monitoring options</a></li>
					<li><a href="%s" target="_blank">eCommerce tracking</a></li>
					<li>Ability to do <a href="%s" target="_blank">server-side tracking of users</a></li>
					<li>Faster API calls (utilizes a multi-threaded mechanism)</li>
					<li><a href="%s" target="_blank">Priority support</a></li>
					<li>A Warm & Fuzzy Feeling knowing you are helping to continue future development</li>
				</ul>', 'better-analaytics'),
					menu_page_url('better-analytics_heatmaps', false),
					menu_page_url('better-analytics_areacharts', false),
					menu_page_url('better-analytics', false) . '#top#advertising',
					menu_page_url('better-analytics', false) . '#top#monitor',
					menu_page_url('better-analytics', false) . '#top#ecommerce',
					menu_page_url('better-analytics', false) . '#top#advanced',
					BETTER_ANALYTICS_SUPPORT_URL
				) ?>
				</td>
		</tr>





		<tr><td colspan="3"><h3><?php esc_html_e( 'Things That Need To Be Checked Manually' , 'better-analytics');?></h3></td></tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('User-ID Tracking', 'better-analytics'));?></th>
			<td class="good">&nbsp;</td>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Tracking Info -&gt; User-ID</b>.', 'better-analytics');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%s" target="_blank">%s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', __('Demographic and Interest Reports', 'better-analytics'));?></th>
			<td class="good">&nbsp;</td>
			<td class="description">
				<?php _e('Found in Google Analytics account under <b>Property Settings</b>.', 'better-analytics');?>
			</td>
		</tr>


	</table>
</div>