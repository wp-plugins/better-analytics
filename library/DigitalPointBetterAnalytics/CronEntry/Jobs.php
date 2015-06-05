<?php

class DigitalPointBetterAnalytics_CronEntry_Jobs
{
	public static function minute()
	{
		if (is_active_widget(false, false, 'better-analytics_popular_widget'))
		{
			DigitalPointBetterAnalytics_Model_Widget::getRealtimeData();
		}
	}

	public static function hour($all = false)
	{
		// This really should be a core WordPress function (deleting expired transients), but w/e...

		global $wpdb;

		if (!$all)
		{
			$time = time();
		}
		else
		{
			$time = time() + (86400 * 365);
		}

		$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
                WHERE a.option_name LIKE %s
                AND a.option_name NOT LIKE %s
                AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
                AND b.option_value < %d";
		$wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_ba_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', $time ) );
	}
}