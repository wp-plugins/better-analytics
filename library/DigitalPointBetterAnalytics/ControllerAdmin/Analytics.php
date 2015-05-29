<?php

class DigitalPointBetterAnalytics_ControllerAdmin_Analytics
{

	public function actionCharts()
	{
		// sanitize
		$_POST['dimension'] = preg_replace('#[^a-z0-9\:]#i' ,'', @$_POST['dimension']);
		$_POST['metric'] = preg_replace('#[^a-z0-9\:]#i' ,'', @$_POST['metric']);
		$_POST['days'] = absint(@$_POST['days']);
		$_POST['realtime'] = absint(@$_POST['realtime']);

		$dimension = substr($_POST['dimension'], 2);
		$type = substr($_POST['dimension'], 0, 1);

		$betterAnalyticsPick = get_option('ba_dashboard_pick');

		if ($_POST['dimension'] != @$betterAnalyticsPick['dimension'] || $_POST['metric'] != @$betterAnalyticsPick['metric'] || $_POST['days'] != @$betterAnalyticsPick['days'] || $_POST['realtime'] != @$betterAnalyticsPick['realtime'])
		{
			update_option('ba_dashboard_pick', array('dimension' => $_POST['dimension'], 'metric' => $_POST['metric'], 'days' => $_POST['days'], 'realtime' => $_POST['realtime']));
		}

		if (!empty($_POST['realtime']))
		{
			if (!$realTime = get_transient('ba_realtime'))
			{
				$realTime = DigitalPointBetterAnalytics_Model_Widget::getRealtimeData(55, true);
			}

			$realTimeOutput = array('users' => 0);

			if (!empty($realTime))
			{
				foreach ($realTime as $key => $value)
				{
					if (is_array($value))
					{
						$realTimeOutput[$key][] = array(ucwords(strtolower(($key == 'keywords' ? __('Organic Search Keywords', 'better-analytics') : ($key == 'referral_path' ? __('Referring URL', 'better-analytics') : ($key == 'page_path' ? __('Current Page', 'better-analytics') : ($key == 'medium' ? __('Medium', 'better-analytics') : ($key == 'devices' ? __('Devices', 'better-analytics') : ''))))))), __('Visitors', 'better-analytics'));
						foreach ($value as $name => $amount)
						{
							$realTimeOutput[$key][] = array($name, intval($amount));
						}
					}
					else
					{
						$realTimeOutput[$key] = intval($value);
					}
				}
			}

			wp_send_json(array(
				'realtime_data' => $realTimeOutput,
				'title' => __('Realtime', 'better-analytics')
			));

		}
		else
		{
			$validDimensions = $this->getDimensionsForCharts();
			$validMetrics = $this->getMetricsForCharts();

			$chartData = array_merge(array(array($validDimensions[$_POST['dimension']], $validMetrics[$_POST['metric']])), DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getChart(1, intval($_POST['days']), $_POST['metric'], $dimension));
			wp_send_json(array(
				'chart_data' => $chartData,
				'title' => ($dimension == 'ga:date' ? $validMetrics[$_POST['metric']] : $validDimensions[$_POST['dimension']]),
				'type' => $type
			));
		}

	}

	public function actionHeatmaps()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$regEx = '#[^a-z0-9\:\-\=\|\ \;]#i';
			$_POST['metric'] = preg_replace($regEx ,'', @$_POST['metric']);
			$_POST['segment'] = preg_replace($regEx ,'', @$_POST['segment']);
			$_POST['end'] = absint(@$_POST['end']);
			$_POST['weeks'] = absint(@$_POST['weeks']);



			if (!DigitalPointBetterAnalytics_Helper_Api::check())
			{
				if (array_search($_POST['metric'], array('ga:users', 'ga:sessions', 'ga:hits', 'ga:organicSearches')) === false)
				{
					wp_send_json(array('error' => sprintf(__('Not all metrics are available for unlicensed copies of the Better Analytics plugin.<br /><br />You can license a copy <a href="%s" target="_blank">over here</a>.<br /><br />If this is a valid license, make sure the purchaser of the add-on has verified ownership of this domain <a href="https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin" target="_blank">over here</a>.', 'better-analytics'), BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin')));
				}
			}
			$heatmapData = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getWeeklyHeatmap($_POST['end'], $_POST['weeks'], $_POST['metric'], $_POST['segment']);

			wp_send_json(array('heatmap_data' => $heatmapData));
		}

		$heatmapData = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getWeeklyHeatmap(7, 10, 'ga:sessions');
		$_hourMap = array();
		for($i = 0; $i < 24; $i++)
		{
			$_hourMap[$i] = date('g A', $i * 3600);
		}

		$this->_view('reports/heatmaps', array(
			'heatmap_data' => $heatmapData,
			'metrics' => $this->_getMetrics(),
			'segments' => $this->_getSegments(),
			'hour_map' => $_hourMap
		));
	}


	public function actionAreacharts()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$_POST['dimension'] = preg_replace('#[^a-z0-9]#i' ,'', @$_POST['dimension']);
			$_POST['scope'] = preg_replace('#[^a-z0-9]#i' ,'', @$_POST['scope']);
			$_POST['time_frame'] = absint(@$_POST['time_frame']);
			$_POST['minimum'] = absint(@$_POST['minimum']);
			$_POST['chart_type'] = preg_replace('#[^a-z]#i' ,'', @$_POST['chart_type']);
			if (!$_POST['chart_type'])
			{
				$_POST['chart_type'] = false;
			}


			if (!$_POST['time_frame'])
			{
				wp_send_json(array('error' => __('Invalid number of days.', 'better-analytics')));
			}
			elseif (!$_POST['dimension'])
			{
				wp_send_json(array('error' => __('Invalid dimension.', 'better-analytics')));
			}
			elseif (!DigitalPointBetterAnalytics_Helper_Api::check())
			{
				if (array_search($_POST['dimension'], array('browser', 'operatingSystem', 'source', 'medium')) === false)
				{
					wp_send_json(array('error' => sprintf(__('Not all dimensions are available for unlicensed copies of the Better Analytics plugin.<br /><br />You can license a copy <a href="%s" target="_blank">over here</a>.<br /><br />If this is a valid license, make sure the purchaser of the add-on has verified ownership of this domain <a href="https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin" target="_blank">over here</a>.', 'better-analytics'), BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin')));
				}
			}


			$dateStart = date("Y-m-d", max(1104580800, time() - (86400 * ($_POST['time_frame'] + 1))));
			$dateEnd = date("Y-m-d", time() - (86400));

			switch ($_POST['scope'])
			{
				case 'month':
					$scope = 'yearMonth';
					break;
				case 'year':
					$scope = 'year';
					break;
				default:
					$scope = 'date';
			}
			$originalDimension = $_POST['dimension'];

			if ($_POST['dimension'] == 'searchNotProvided')
			{
				$extraFilter = ';ga:medium==organic;ga:keyword==(not provided)';
				$_POST['dimension'] = 'keyword';
			}
			elseif ($_POST['dimension'] == 'oraganicSearchMarketshare')
			{
				$extraFilter = ';ga:medium==organic';
				$_POST['dimension'] = 'source,ga:medium';

			}
			elseif ($_POST['dimension'] == 'mobileOperatingSystem')
			{
				$extraFilter = ';ga:isMobile==Yes';
				$_POST['dimension'] = 'operatingSystem';
			}
			else
			{
				$extraFilter = '';
			}

			$cacheKey = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
				$dateStart,
				$dateEnd,
				'ga:sessions', // metric
				'ga:' . $scope . ',ga:' . $_POST['dimension'], // dimensions
				'ga:' . $scope . ',-ga:sessions', // sort
				'ga:sessions>' . $_POST['minimum'] . $extraFilter // filters
			);

			if ($originalDimension == 'searchNotProvided')
			{
				$cacheKey2 = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
					$dateStart,
					$dateEnd,
					'ga:sessions', // metric
					'ga:' . $scope, // dimensions
					'ga:' . $scope . ',-ga:sessions', // sort
					'ga:sessions>' . $_POST['minimum'] . ';ga:medium==organic'// filters
				);
			}

			$results = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey);

			if ($originalDimension == 'searchNotProvided')
			{
				$resultTotal = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey2);

				$totalNotProvided = $consolidated = array();

				if (!empty($results['rows']))
				{
					foreach ($results['rows'] as $row)
					{
						if ($row[1] == '(not provided)')
						{
							$totalNotProvided[$row[0]] = $row[2];
						}
					}
					foreach ($resultTotal['rows'] as $row)
					{
						$consolidated[] = array_merge(array($row[0]), array(__('Keywords Provided', 'better-analytics'), $row[1] - @$totalNotProvided[$row[0]]));
						$consolidated[] = array_merge(array($row[0]), array(__('Keywords Not Provided', 'better-analytics'), @$totalNotProvided[$row[0]] + 0));
					}

					$results['rows'] = $consolidated;
				}



			}
			elseif ($originalDimension == 'oraganicSearchMarketshare')
			{

				if (!empty($results['rows']))
				{
					foreach ($results['rows'] as &$row)
					{
						$row[2] = $row[3];
						$row[1] = (strlen($row[1]) > 3 ? ucwords($row[1]) : strtoupper($row[1]));
						unset($row[3]);
					}
				}
			}

			$resultsOrdered = $resultsOutput = $allLabels = array();
			if (!empty($results['rows']))
			{
				foreach ($results['rows'] as $row)
				{
					$allLabels[$row[1]] = null;
					$resultsOrdered[$row[0]][$row[1]] = intval($row[2]);
				}

				ksort($allLabels, SORT_STRING);

				foreach ($resultsOrdered as &$labels)
				{
					$labels = array_merge($labels, array_diff_key($allLabels, $labels));
					ksort($labels, SORT_STRING);
				}

				$resultsOutput = array(array_merge(array('Date'), array_keys($allLabels)));
				foreach ($resultsOutput[0] as &$item)
				{
					$item = (string)$item;
				}

				foreach ($resultsOrdered as $date => $values)
				{
					$resultsOutput[] = array_merge(array($date), array_values($values));
				}

				$title = '';
				$dimensions = $this->_getDimensions();
				foreach ($dimensions as $group)
				{
					foreach ($group as $key => $value)
					{
						if ($originalDimension == $key)
						{
							$title = $value;
							break;
						}
					}
				}

				wp_send_json(array(
					'chart_data' => $resultsOutput,
					'chart_type' => $_POST['chart_type'],
					'title' =>sprintf(__('History for %s', 'better-analytics'), $title)
				));
			}

			wp_send_json(array(
				'error' => __('No data for the criteria given', 'better-analytics')
			));


		}



		$this->_view('reports/area_charts', array(
			'dimensions' => $this->_getDimensions(),
		));
	}

	public function actionMonitor()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$_POST['days'] = absint(@$_POST['days']);

			if (!$_POST['days'])
			{
				wp_send_json(array('error' => __('Invalid number of days.', 'better-analytics')));
			}

			$cacheKey = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
				$_POST['days'] . 'daysAgo',
				'yesterday',
				'ga:totalEvents', // metric
				'ga:eventCategory,ga:eventAction,ga:eventLabel', // dimensions
				'-ga:totalEvents,ga:eventCategory,ga:eventAction,ga:eventLabel', // sort
				'ga:eventCategory==Error,ga:eventCategory==Image' // filters
			);

			$results = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey);

			$chartOutput = array(array(
				__('Events', 'better-analytics'),
				__('Category', 'better-analytics'),
				__('Type', 'better-analytics'),
				__('Detail', 'better-analytics')
			));
			if (@$results['rows'])
			{
				foreach ($results['rows'] as $row)
				{
					$chartOutput[] = array(absint($row[3]), $row[0], $row[1], $row[2]);
				}

				wp_send_json(array(
					'chart_data' => $chartOutput
				));
			}

			wp_send_json(array(
				'error' => __('No data for the criteria given', 'better-analytics')
			));
		}

		$this->_view('reports/events', array('type' => 'monitor'));
	}

	public function actionEvents()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$_POST['days'] = absint(@$_POST['days']);

			if (!$_POST['days'])
			{
				wp_send_json(array('error' => __('Invalid number of days.', 'better-analytics')));
			}

			$cacheKey = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
				$_POST['days'] . 'daysAgo',
				'yesterday',
				'ga:totalEvents', // metric
				'ga:eventCategory,ga:eventAction,ga:eventLabel', // dimensions
				'-ga:totalEvents,ga:eventCategory,ga:eventAction,ga:eventLabel', // sort
				'ga:eventCategory!=Error;ga:eventCategory!=Image' // filters
			);

			$results = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey);

			$chartOutput = array(array(
				__('Events', 'better-analytics'),
				__('Category', 'better-analytics'),
				__('Type', 'better-analytics'),
				__('Detail', 'better-analytics')
			));
			if (@$results['rows'])
			{
				foreach ($results['rows'] as $row)
				{
					$chartOutput[] = array(absint($row[3]), $row[0], $row[1], $row[2]);
				}

				wp_send_json(array(
					'chart_data' => $chartOutput
				));
			}


			wp_send_json(array(
				'error' => __('No data for the criteria given', 'better-analytics')
			));
		}

		$this->_view('reports/events', array('type' => 'events'));
	}




	protected function _assertLinkedAccount()
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if (!get_option('ba_tokens') || !@$betterAnalyticsOptions['api']['profile'])
		{
			$this->_responseException(sprintf(__('No Linked Google Analytics Account.  You can link one in the <a href="%s">Better Analytics API settings</a>.', 'better-analytics'), menu_page_url('better-analytics', false) . '#top#api'));
			return false;
		}
		return true;
	}


	protected function _getMetrics()
	{
		return array(
			__('User', 'better-analytics') => array(
				'ga:users' => __('Users', 'better-analytics'),
			),
			__('Session', 'better-analytics') => array(
				'ga:sessions' => __('Sessions', 'better-analytics'),
				'ga:hits' => __('Hits', 'better-analytics'),
			),
			__('Traffic Sources', 'better-analytics') => array(
				'ga:organicSearches' => __('Organic Search', 'better-analytics'),
			),
			__('AdWords', 'better-analytics') => array(
				'ga:impressions' => __('Impressions', 'better-analytics'),
				'ga:adClicks' => __('Clicks', 'better-analytics'),
				'ga:adCost' => __('Cost', 'better-analytics'),
				'ga:CPM' => __('CPM', 'better-analytics'),
				'ga:CPC' => __('CPC', 'better-analytics'),
			),
			__('Social Activities', 'better-analytics') => array(
				'ga:socialActivities' => __('Social Activities', 'better-analytics'),
			),
			__('Page Tracking', 'better-analytics') => array(
				'ga:pageviews' => __('Page Views', 'better-analytics'),
			),
			__('Internal Search', 'better-analytics') => array(
				'ga:searchUniques' => __('Unique Searches', 'better-analytics'),
			),
			__('Site Speed', 'better-analytics') => array(
				'ga:pageLoadTime' => __('Page Load Time', 'better-analytics'),
			),
			__('Event Tracking', 'better-analytics') => array(
				'ga:totalEvents' => __('Total Events', 'better-analytics'),
				'ga:uniqueEvents' => __('Unique Events', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==User;ga:eventAction==Registration' => __('User Registrations', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Content;ga:eventAction==Comment' => __('Comments Created', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==YouTube Video;ga:eventAction==Playing' => __('YouTube Video Played', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==YouTube Video;ga:eventAction==Paused' => __('YouTube Video Paused', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==YouTube Video;ga:eventAction==Ended' => __('YouTube Video Plays To End', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Email;ga:eventAction==Send' => __('Emails Sent', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Email;ga:eventAction==Open' => __('Emails Opened', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Link;ga:eventAction==Click' => __('External Links Clicked', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Link;ga:eventAction==Download' => __('File Downloads', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Image;ga:eventAction==Not Loaded' => __('Images Not Loading', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==Page Not Found' => __('Page Not Found (404)', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==AJAX Request;ga:eventAction==Trigger' => __('AJAX Requests', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==JavaScript' => __('JavaScript Errors', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==AJAX' => __('AJAX Errors', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==Browser Console' => __('Browser Console Errors', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction=~^YouTube' => __('YouTube Errors', 'better-analytics'),

				'ga:totalEvents|ga:eventCategory==Advertisement;ga:eventAction==Click' => __('Advertisement Clicked', 'better-analytics'),

			),
			__('Ecommerce', 'better-analytics') => array(
				'ga:transactions' => __('Transactions', 'better-analytics'),
				'ga:transactionRevenue' => __('Transaction Revenue', 'better-analytics'),
				'ga:revenuePerTransaction' => __('Revenue Per Transaction', 'better-analytics'),
			),
			__('Social Interactions', 'better-analytics') => array(
				'ga:socialInteractions' => __('Social Interactions', 'better-analytics'),
				'ga:uniqueSocialInteractions' => __('Unique Social Interactions', 'better-analytics'),
			),
			__('DoubleClick Campaign Manager', 'better-analytics') => array(
				'ga:dcmCPC' => __('CPC', 'better-analytics'),
				'ga:dcmCTR' => __('CTR', 'better-analytics'),
				'ga:dcmClicks' => __('Clicks', 'better-analytics'),
				'ga:dcmCost' => __('Cost', 'better-analytics'),
				'ga:dcmImpressions' => __('Impressions', 'better-analytics'),
			),
			__('AdSense', 'better-analytics') => array(
				'ga:adsenseRevenue' => __('Revenue', 'better-analytics'),
				'ga:adsenseAdsViewed' => __('Views', 'better-analytics'),
				'ga:adsenseAdsClicks' => __('Clicks', 'better-analytics'),
				'ga:adsensePageImpressions' => __('Page Impressions', 'better-analytics'),
				'ga:adsenseCTR' => __('CTR', 'better-analytics'),
				'ga:adsenseECPM' => __('ECPM', 'better-analytics'),
				'ga:adsenseExits' => __('Exits', 'better-analytics'),
				'ga:adsenseViewableImpressionPercent' => __('Viewable Impressions', 'better-analytics'),
				'ga:adsenseCoverage' => __('Coverage', 'better-analytics'),
			)
		);
	}

	protected function _getSegments()
	{
		return array(
			__('Default Segments', 'better-analytics') => array(
				'' => __('Everything', 'better-analytics'),
				'gaid::-1' => __('All Visits', 'better-analytics'),
				'gaid::-2' => __('New Visitors', 'better-analytics'),
				'gaid::-3' => __('Returning Visitors', 'better-analytics'),
				'gaid::-4' => __('Paid Search Traffic', 'better-analytics'),
				'gaid::-5' => __('Non-paid Search Traffic', 'better-analytics'),
				'gaid::-6' => __('Search Traffic', 'better-analytics'),
				'gaid::-7' => __('Direct Traffic', 'better-analytics'),
				'gaid::-8' => __('Referral Traffic', 'better-analytics'),
				'gaid::-9' => __('Visits with Conversions', 'better-analytics'),
				'gaid::-10' => __('Visits with Transactions', 'better-analytics'),
				'gaid::-11' => __('Mobile and Tablet Traffic', 'better-analytics'),
				'gaid::-12' => __('Non-bounce Visits', 'better-analytics'),
				'gaid::-13' => __('Tablet Traffic', 'better-analytics'),
				'gaid::-14' => __('Mobile Traffic', 'better-analytics'),
				'dynamic::ga:userGender==male' => __('Male Users', 'better-analytics'),
				'dynamic::ga:userGender==female' => __('Female Users', 'better-analytics'),

			)
		);
	}



	protected function _getDimensions()
	{
		return array(
			__('Visitor', 'better-analytics') => array(
				'visitorType' => __('Visitor Type', 'better-analytics'),
				'visitCount' => __('Visit Count', 'better-analytics'),
				'daysSinceLastVisit' => __('Days Since Last Visit', 'better-analytics'),
			),
			__('Traffic Sources', 'better-analytics') => array(
				'source' => __('Source', 'better-analytics'),
				'medium' => __('Medium', 'better-analytics'),
				'socialNetwork' => __('Social Network', 'better-analytics'),
				'searchNotProvided' => __('Search Keywords Provided', 'better-analytics'),
				'oraganicSearchMarketshare' => __('Organic Search Marketshare', 'better-analytics'),
			),
			__('Platform', 'better-analytics') => array(
				'browser' => __('Browser', 'better-analytics'),
				'operatingSystem' => __('Operating System', 'better-analytics'),
				'operatingSystemVersion' => __('Operating System Version', 'better-analytics'),
				'isMobile' => __('Is Mobile', 'better-analytics'),
				'isTablet' => __('Is Tablet', 'better-analytics'),
				'mobileOperatingSystem' => __('Mobile Operating System', 'better-analytics'),
				'mobileDeviceMarketingName' => __('Mobile Device Marketing Name', 'better-analytics'),
				'mobileDeviceBranding' => __('Mobile Device Branding', 'better-analytics'),
				'mobileDeviceModel' => __('Mobile Device Model', 'better-analytics'),
				'mobileInputSelector' => __('Mobile Input Selector', 'better-analytics'),
				'mobileDeviceInfo' => __('Mobile Device Info', 'better-analytics'),
			),
			__('Geo / Network', 'better-analytics') => array(
				'continent' => __('Continent', 'better-analytics'),
				'subContinent' => __('Sub-Continent', 'better-analytics'),
				'country' => __('Country', 'better-analytics'),
			),
			__('System', 'better-analytics') => array(
				'flashVersion' => __('Flash Version', 'better-analytics'),
				'javaEnabled' => __('Java Enabled', 'better-analytics'),
				'language' => __('Language', 'better-analytics'),
				'screenColors' => __('Screen Colors', 'better-analytics'),
				'screenResolution' => __('Screen Resolution', 'better-analytics'),
			),
			__('Internal Search', 'better-analytics') => array(
				'searchUsed' => __('Search Used', 'better-analytics'),
			),
			__('Page Tracking', 'better-analytics') => array(
				'hostname' => __('Hostname', 'better-analytics'),
			),
			__('Social Interactions', 'better-analytics') => array(
				'socialInteractionNetwork' => __('Social Network', 'better-analytics'),
				'socialInteractionAction' => __('Social Action', 'better-analytics'),
				'socialInteractionNetworkAction' => __('Social Network Action', 'better-analytics'),
				'socialEngagementType' => __('Social Engagement Type', 'better-analytics'),
			),
			__('Custom Variables', 'better-analytics') => array(
				'dimension1' => __('Custom Dimension 1', 'better-analytics'),
				'dimension2' => __('Custom Dimension 2', 'better-analytics'),
				'dimension3' => __('Custom Dimension 3', 'better-analytics'),
				'dimension4' => __('Custom Dimension 4', 'better-analytics'),
				'dimension5' => __('Custom Dimension 5', 'better-analytics'),
				'customVarValue1' => __('Custom Variable Value 1', 'better-analytics'),
				'customVarValue2' => __('Custom Variable Value 2', 'better-analytics'),
				'customVarValue3' => __('Custom Variable Value 3', 'better-analytics'),
				'customVarValue4' => __('Custom Variable Value 4', 'better-analytics'),
				'customVarValue5' => __('Custom Variable Value 5', 'better-analytics'),
			),
			__('Audience', 'better-analytics') => array(
				'visitorAgeBracket' => __('Age Bracket', 'better-analytics'),
				'visitorGender' => __('Gender', 'better-analytics'),
				'interestAffinityCategory' => __('Interest Affinity', 'better-analytics'),
				'interestInMarketCategory' => __('Interest In Market', 'better-analytics'),
				'interestOtherCategory' => __('Interest Other', 'better-analytics'),
			),
		);
	}



	protected function _responseException($error)
	{
		echo '<div class="error"><p>' . $error . '</p></div>';
	}

	protected function _responseError($error)
	{
		echo '<div class="error"><p>' . $error . '</p></div>';
	}

	static public function getDimensionsForCharts()
	{
		$dimensions = array();

		$betterAnalyticsOptions = get_option('better_analytics');

		$dimensions['l:ga:date'] = __('Date', 'better-analytics');

		if (!empty($betterAnalyticsOptions['dimension']['category']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['category']] = __('Categories', 'better-analytics');
		}
		if (!empty($betterAnalyticsOptions['dimension']['author']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['author']] = __('Authors', 'better-analytics');
		}
		if (!empty($betterAnalyticsOptions['dimension']['tag']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['tag']] = __('Tags', 'better-analytics');
		}

		$dimensions['p:ga:source'] = __('Source', 'better-analytics');
		$dimensions['p:ga:fullReferrer'] = __('Referrer', 'better-analytics');

		$dimensions['p:ga:medium'] = __('Medium', 'better-analytics');
		$dimensions['g:ga:country'] = __('Country', 'better-analytics');
		//$dimensions['g:ga:region'] = __('Region', 'better-analytics');
		//$dimensions['g:ga:city'] = __('City', 'better-analytics');


		/*
		if (!empty($betterAnalyticsOptions['dimension']['user']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['user']] = __('Top Registered Users', 'better-analytics');
		}
		*/
		return $dimensions;
	}

	static public function getMetricsForCharts()
	{
		$metrics = array(
			'ga:pageviews' => __('Page Views', 'better-analytics'),
			'ga:sessions' => __('Sessions', 'better-analytics'),
			'ga:users' => __('Users', 'better-analytics'),
			'ga:avgSessionDuration' => __('Session Length', 'better-analytics'),
			'ga:organicSearches' => __('Organic Search', 'better-analytics')
		);

		return $metrics;
	}

	protected function _view($name, array $args = array())
	{
		DigitalPointBetterAnalytics_Base_Admin::getInstance()->view($name, $args);
	}
}