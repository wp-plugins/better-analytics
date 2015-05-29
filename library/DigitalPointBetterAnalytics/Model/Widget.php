<?php

class DigitalPointBetterAnalytics_Model_Widget
{
	public static function getRealtimeData($secondsToCache = 300, $returnData = false)
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		if (get_option('ba_tokens') && @$betterAnalyticsOptions['api']['profile'])
		{
			$reportingObject = DigitalPointBetterAnalytics_Helper_Reporting::getInstance();
			$cacheKey = $reportingObject->getRealtime('rt:activeUsers', 'rt:source,rt:medium,rt:referralPath,rt:pagePath,rt:deviceCategory,rt:country,rt:keyword');
			$data = $reportingObject->getResults($cacheKey);

			$realTimeOutput = array('users' => intval(@$data['totalsForAllResults']['rt:activeUsers']));
			if ($data['rows'])
			{
				foreach ($data['rows'] as $row)
				{
					if (strlen($row[1]) < 4)
					{
						$medium = strtoupper($row[1]);
					}
					else
					{
						$medium = ucwords(strtolower($row[1]));
					}

					$realTimeOutput['medium'][$medium] += $row[7];

					if ($row[1] == 'REFERRAL')
					{
						$realTimeOutput['referral_path'][$row[0] . $row[2]] += $row[7];
					}
					$realTimeOutput['page_path'][$row[3]] += $row[7];

					$deviceCategory = ucwords(strtolower($row[4]));

					$realTimeOutput['devices'][$deviceCategory] += $row[7];
					$realTimeOutput['country'][$row[5]] += $row[7];
					if ($row[1] == 'ORGANIC')
					{
						$keywords = trim(strtolower($row[6]));
						$realTimeOutput['keywords'][$keywords] += $row[7];
					}
				}

				foreach ($realTimeOutput as &$array)
				{
					if (is_array($array))
					{
						arsort($array);
					}
				}
			}

			set_transient('ba_realtime', $realTimeOutput, $secondsToCache);

			if ($returnData)
			{
				return $realTimeOutput;
			}
		}


	}
}