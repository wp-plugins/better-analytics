<?php

wp_enqueue_script('jsapi', 'https://www.google.com/jsapi?autoload=%7B%22modules%22%3A%5B%7B%22name%22%3A%22visualization%22%2C%22version%22%3A%221.1%22%2C%22packages%22%3A%5B%22corechart%22%5D%7D%5D%7D', array(), null );

wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);
echo '<h2>' . __('Reports & Charts', 'better-analytics') . '</h2>';

?>


<h3 class="nav-tab-wrapper">
	<a class="nav-tab" href="<?php menu_page_url('better-analytics_heatmaps'); ?>"><?php _e( 'Weekly Heatmaps', 'better-analytics' ); ?></a>
	<a class="nav-tab nav-tab-active" href="<?php menu_page_url('better-analytics_areacharts'); ?>"><?php _e( 'Charts', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php menu_page_url('better-analytics_events'); ?>"><?php _e( 'Events', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php menu_page_url('better-analytics_monitor'); ?>"><?php _e( 'Issue Monitoring', 'better-analytics' ); ?></a>
</h3>

<div id="chart_loading" class="dashicons dashicons-update"></div>
<div id="area_chart"></div>

<form>
	<table id="parameters" class="form-table">
		<?php
		echo '<tr valign="top">
						<th scope="row">' . __('Dimension', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_dimension" name="dimension" class="chosen-charts">';

		foreach ($dimensions as $label => $group)
		{
			echo '<optgroup label="' . htmlentities($label) . '">';

			foreach ($group as $key => $name)
			{
				echo '<option value="' . $key . '"' . selected($key, 'browser') . '>' . htmlentities($name) . '</option>';

			}

			echo '</optgroup>';
		}
		echo '</select>
						</td>
					</tr>';


		echo '<tr valign="top">
						<th scope="row">' . __('Time Frame', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_time_frame" name="time_frame" class="chosen-charts">';

			echo '<option value="30">' . __('1 Month', 'better-analytics') . '</option>';
			echo '<option value="365" selected="selected">' . __('1 Year', 'better-analytics') . '</option>';
			echo '<option value="730">' . __('2 Years', 'better-analytics') . '</option>';
			echo '<option value="1825">' . __('5 Years', 'better-analytics') . '</option>';
			echo '<option value="3650">' . __('10 Years', 'better-analytics') . '</option>';

		echo '</select>
						</td>
					</tr>';


		echo '<tr valign="top">
						<th scope="row">' . __('Scope', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_scope" name="scope" class="chosen-charts">';

			echo '<option value="day">' . __('Day', 'better-analytics') . '</option>';
			echo '<option value="month" selected="selected">' . __('Month', 'better-analytics') . '</option>';
			echo '<option value="year">' . __('Year', 'better-analytics') . '</option>';

		echo '</select>
						</td>
					</tr>';





		echo '<tr valign="top">
						<th scope="row">' . __('Minimum Value To Plot', 'better-analytics') . '</th>
						<td>';

		echo '<input type="number" id="ba_minimum" name="minimum" value="100" min="0" step="100">
						</td>
					</tr>';

		echo '<tr valign="top">
						<th scope="row">' . __('Display Chart As', 'better-analytics') . '</th>
						<td>';

		echo '<label><input name="chart_type" type="radio" value="percent"  checked="checked">' . __('Stacked Area Percent', 'better-analytics') . '</label> &nbsp;  &nbsp; ';
		echo '<label><input name="chart_type" type="radio" value="absolute">' . __('Stacked', 'better-analytics') . '</label> &nbsp;  &nbsp; ';
		echo '<label><input name="chart_type" type="radio" value="">' . __('Overlap', 'better-analytics') . '</label>


						</td>
					</tr>';



		?>
	</table>

</form>