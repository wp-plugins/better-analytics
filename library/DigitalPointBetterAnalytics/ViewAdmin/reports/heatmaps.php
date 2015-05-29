<?php
	wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
	wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

	wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
	wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

	echo '<h2>' . __('Reports & Charts', 'better-analytics') . '</h2>';
?>

<h3 class="nav-tab-wrapper">
	<a class="nav-tab nav-tab-active" href="<?php menu_page_url('better-analytics_heatmaps'); ?>"><?php _e( 'Weekly Heatmaps', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php menu_page_url('better-analytics_areacharts'); ?>"><?php _e( 'Charts', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php menu_page_url('better-analytics_events'); ?>"><?php _e( 'Events', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php menu_page_url('better-analytics_monitor'); ?>"><?php _e( 'Issue Monitoring', 'better-analytics' ); ?></a>
</h3>

<div id="chart_loading" class="dashicons dashicons-update"></div>

<div id="Heatmap" class="table">
	<div class="row">
		<div class="cell"></div>
		<div class="cell"><?php _e('Sun<span class="responsiveHide">day</span>', 'better-analytics'); ?></div>
		<div class="cell"><?php _e('Mon<span class="responsiveHide">day</span>', 'better-analytics'); ?></div>
		<div class="cell"><?php _e('Tue<span class="responsiveHide">sday</span>', 'better-analytics'); ?></div>
		<div class="cell"><?php _e('Wed<span class="responsiveHide">nesday</span>', 'better-analytics'); ?></div>
		<div class="cell"><?php _e('Thu<span class="responsiveHide">rsday</span>', 'better-analytics'); ?></div>
		<div class="cell"><?php _e('Fri<span class="responsiveHide">day</span>', 'better-analytics'); ?></div>
		<div class="cell"><?php _e('Sat<span class="responsiveHide">urday</span>', 'better-analytics'); ?></div>
	</div>
	<?php
		foreach ($heatmap_data as $hour_key => $hour_data)
		{
			echo '<div class="row"><div class="cell">' . $hour_map[$hour_key]. '</div>';
			foreach ($hour_data as $day_key => $day_data)
			{
				echo '<div id="slot' . $hour_key . '-' . $day_key . '" class="cell" data-val="' . $day_data . '"></div>';
			}
			echo '</div>';
		}
	?>
</div>

<form>
	<table id="parameters" class="form-table">
		<?php
		echo '<tr valign="top">
						<th scope="row">' . __('Metric', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_metric" name="metric" class="chosen-charts">';

		foreach ($metrics as $label => $group)
		{
			echo '<optgroup label="' . htmlentities($label) . '">';

			foreach ($group as $key => $name)
			{
				echo '<option value="' . $key . '"' . selected($key, 'ga:sessions') . '>' . htmlentities($name) . '</option>';

			}

			echo '</optgroup>';
		}
		echo '</select>
						</td>
					</tr>';


		echo '<tr valign="top">
						<th scope="row">' . __('Segment', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_segment" name="segment" class="chosen-charts">';

		foreach ($segments as $label => $group)
		{
			echo '<optgroup label="' . htmlentities($label) . '">';

			foreach ($group as $key => $name)
			{
				echo '<option value="' . $key . '">' . htmlentities($name) . '</option>';

			}

			echo '</optgroup>';
		}
		echo '</select>
						</td>
					</tr>';

		?>
		<tr>
			<th></th>
			<td>
				<input type="number" name="weeks" id="ba_weeks" size="5" min="1" max="1000" step="1" value="4" /> &nbsp; <?php _e('Weeks Of Data, Ending', 'better-analytics') ?> &nbsp;
				<input type="number" name="end" id="ba_end" size="5" min="0" max="10000" step="1" value="1" /> &nbsp; <?php _e('Days Ago', 'better-analytics') ?>

			</td>
		</tr>

	</table>
</form>