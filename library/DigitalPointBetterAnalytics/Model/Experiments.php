<?php

class DigitalPointBetterAnalytics_Model_Experiments
{
	public static function getStatuses()
	{
		$types = array(
			'DRAFT' => esc_html__('Draft', 'better-analytics'),
			'READY_TO_RUN' => esc_html__('Ready to run', 'better-analytics'),
			'RUNNING' => esc_html__('Running', 'better-analytics'),
			'ENDED' => esc_html__('Ended', 'better-analytics'),
		);

		return $types;
	}

	public static function getStatusNameByCode($statusCode)
	{
		$_types = self::getStatuses();
		return @$_types[$statusCode];
	}

}