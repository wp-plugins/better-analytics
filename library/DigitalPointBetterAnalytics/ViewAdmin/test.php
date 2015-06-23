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

		$checks['dimensions'] = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getDimensionsByPropertyId(@$checks['matchingProfile']['accountId'], $betterAnalyticsOptions['property_id'], array('Categories', 'Author', 'Tags', 'Year', 'Role', 'User'));
	}

	$checks['licensed'] = DigitalPointBetterAnalytics_Helper_Api::check(true);

?>

<div class="wrap">

	<h2><?php esc_html_e( 'Test Analytics Integration' , 'better-analytics');?></h2>

	<table class="form-table" id="ba_test">
		<?php esc_html_e( 'This is a checklist of things for full integration with Google Analytics. You can click on any of the titles to be taken where you need to go to configure that item.' , 'better-analytics');?>

		<tr><td colspan="3"><h3><?php esc_html_e( 'Things That Can Be Automatically Checked' , 'better-analytics');?></h3></td></tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#general', esc_html__('Web Property ID Defined', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['property_id'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://php.net/manual/en/curl.installation.php', esc_html__('cURL Installed', 'better-analytics'));?></th>

				<?php
					echo (function_exists('curl_multi_init') ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php esc_html_e('Not required, but recommended to have the cURL extensions enabled in PHP.', 'better-analytics');?>
			</td>
		</tr>

		<?php
		// not really needed since most people will not use their own project credentials
		/*
		?>
		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#api', esc_html__('API Project Credentials', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['api']['client_id'] && $betterAnalyticsOptions['api']['client_secret'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td>
			</td>
		</tr>
		*/
		?>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#api', esc_html__('Google Analytics Account Linked', 'better-analytics'));?></th>
				<?php
					echo (DigitalPointBetterAnalytics_Base_Public::getInstance()->getTokens() ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#api', esc_html__('Analytics Profile Selected', 'better-analytics'));?></th>
				<?php
					echo ($betterAnalyticsOptions['api']['profile'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Site Search Setup', 'better-analytics'));?></th>
				<?php
					echo (@$checks['siteSearchSetup'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php
					/* translators: %1$s = <strong>, %2$s = </strong> */
					printf(__('Found in Google Analytics account under %1$sView Settings -> Query Parameter%2$s (should be set to "%1$ss%2$s").', 'better-analytics'), '<strong>', '</strong>');
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Ecommerce Tracking Enabled', 'better-analytics'));?></th>
				<?php
					echo (@$checks['ecommerceTracking'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">

				<?php
					/* translators: %1$s = <strong>, %2$s = </strong> */
					printf(__('Found in Google Analytics account under %1$sEcommerce Settings%2$s.', 'better-analytics'), '<strong>', '</strong>');
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Enhanced Ecommerce Tracking Enabled', 'better-analytics'));?></th>
				<?php
					echo (@$checks['enhancedEcommerceTracking'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php
					/* translators: %1$s = <strong>, %2$s = </strong> */
					printf(esc_html__('Found in Google Analytics account under %1$sEcommerce Settings%2$s.', 'better-analytics'), '<strong>', '</strong>');
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Industry Vertical Set', 'better-analytics'));?></th>
				<?php
					echo (!empty($checks['industryVertical']) && $checks['industryVertical'] != 'UNSPECIFIED' ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php
					/* translators: %1$s = <strong>, %2$s = </strong>, %3$s = current setting in Google Analytics account */
					printf(esc_html__('Found in Google Analytics account under %1$sProperty Settings%2$s (currently set to %1$s%3$s%2$s).', 'better-analytics'), '<strong>', '</strong>', ($checks['industryVertical'] ? $checks['industryVertical'] : 'UNSPECIFIED'));
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Category Tracking', 'better-analytics'));?></th>
				<?php
					echo (!empty($checks['dimensions']['Categories']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php
					/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
					printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Categories');
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Author Tracking', 'better-analytics'));?></th>
				<?php
					echo (!empty($checks['dimensions']['Author']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>

			<td class="description">

				<?php
					/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
					printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Author');
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Tag Tracking', 'better-analytics'));?></th>
				<?php
					echo (!empty($checks['dimensions']['Tags']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">
				<?php
					/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
					printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Tags');
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Publication Year Tracking', 'better-analytics'));?></th>
			<?php
			echo (!empty($checks['dimensions']['Year']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
			?>
			<td class="description">

				<?php
				/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
				printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Year');
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For User Role Tracking', 'better-analytics'));?></th>
			<?php
			echo (!empty($checks['dimensions']['Role']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
			?>
			<td class="description">

				<?php
				/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
				printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Role');
				?>
			</td>
		</tr>



		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For User Tracking', 'better-analytics'));?></th>
				<?php
					echo (!empty($checks['dimensions']['User']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td class="description">

				<?php
					/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
					printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'User');
				?>
			</td>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Category Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo (@$betterAnalyticsOptions['dimension']['category'] > 0 && @$checks['dimensions']['Categories']['index'] == $betterAnalyticsOptions['dimension']['category'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">

			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Author Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo (@$betterAnalyticsOptions['dimension']['author'] > 0 && @$checks['dimensions']['Author']['index'] == $betterAnalyticsOptions['dimension']['author'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Tag Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo (@$betterAnalyticsOptions['dimension']['tag'] > 0 && @$checks['dimensions']['Tags']['index'] == $betterAnalyticsOptions['dimension']['tag'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Publication Year Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo (@$betterAnalyticsOptions['dimension']['year'] > 0 && @$checks['dimensions']['Year']['index'] == $betterAnalyticsOptions['dimension']['year'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('User Role Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo (@$betterAnalyticsOptions['dimension']['role'] > 0 && @$checks['dimensions']['Role']['index'] == $betterAnalyticsOptions['dimension']['role'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>


		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Registered User Tracking Dimension Index Set', 'better-analytics'));?></th>
				<?php
					echo (@$betterAnalyticsOptions['dimension']['user'] > 0 && @$checks['dimensions']['User']['index'] == $betterAnalyticsOptions['dimension']['user'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
				?>
			<td></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_test&utm_medium=wordpress&utm_campaign=plugin', esc_html__('Better Analytics Pro License', 'better-analytics'));?></th>
			<?php
				echo ($checks['licensed'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
			?>
			<td class="description">
				<?php esc_html_e('A few extra advanced features are available when you license the Better Analytics Pro plugin.  Please don\'t steal, a crazy amount of work went into this.  Some of the extra features:', 'better-analytics'); ?>
				<ul>
					<li>
						<?php
							printf(esc_html__('More metrics available for %1$sHeat Maps%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics_heatmaps', false)) . '" target="_blank">', '</a>');
						?>
					</li>
					<li>
						<?php
							printf(esc_html__('More dimensions available for %1$sCharts%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics_areacharts', false)) . '" target="_blank">', '</a>');
						?>
					</li>
					<li>
						<?php
							printf(esc_html__('More %1$sadvertising platform click tracking%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#advertising') . '" target="_blank">', '</a>');
						?>
					</li>
					<li>
						<?php
							printf(esc_html__('More %1$sissue monitoring options%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#monitor') . '" target="_blank">', '</a>');
						?>
					</li>
					<li>
						<?php
							printf(esc_html__('%1$seCommerce tracking%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#ecommerce') . '" target="_blank">', '</a>');
						?>
					</li>
					<li>
						<?php
							printf(esc_html__('Ability to do %1$sserver-side tracking of users%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#advanced') . '" target="_blank">', '</a>');
						?>
					</li>
					<li>
						<?php
							esc_html_e('Faster API calls (utilizes a multi-threaded mechanism)', 'better_analytics');
						?>
					</li>
					<li>
						<?php
							printf(esc_html__('%1$sPriority support%2$s', 'better_analytics'), '<a href="' . esc_url(BETTER_ANALYTICS_SUPPORT_URL . '#utm_source=admin_test&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">', '</a>');
						?>
					</li>
					<li>
						<?php
							esc_html_e('A Warm & Fuzzy Feeling knowing you are helping to continue future development', 'better_analytics');
						?>
					</li>


				</ul>
			</td>
		</tr>





		<tr><td colspan="3"><h3><?php esc_html_e( 'Things That Need To Be Checked Manually' , 'better-analytics');?></h3></td></tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('User-ID Tracking', 'better-analytics'));?></th>
			<td class="good">&nbsp;</td>
			<td class="description">
				<?php printf(esc_html__('Found in Google Analytics account under %1$sTracking Info -> User-ID%2$s.', 'better-analytics'), '<strong>', '</strong>');?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Demographic and Interest Reports', 'better-analytics'));?></th>
			<td class="good">&nbsp;</td>
			<td class="description">
				<?php printf(esc_html__('Found in Google Analytics account under %1$sProperty Settings%1$s.', 'better-analytics'), '<strong>', '</strong>');?>
			</td>
		</tr>


	</table>
</div>