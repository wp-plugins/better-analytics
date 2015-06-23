<?php

abstract class DigitalPointBetterAnalytics_Helper_Reporting_Abstract
{
	protected static $_instance;

	protected static $_oAuthEndpoint = 'https://accounts.google.com/o/oauth2/';
	protected static $_dataEndpoint = 'https://www.googleapis.com/analytics/v3/data/ga';
	protected static $_realtimeEndpoint = 'https://www.googleapis.com/analytics/v3/data/realtime';

	protected static $_webPropertiesEndpoint = 'https://www.googleapis.com/analytics/v3/management/accounts/%s/webproperties/';

	protected static $_profilesEndpoint = '%s/profiles';
	protected static $_dimensionsEndpoint = '%s/customDimensions';

	protected static $_curlHandles = array();

	protected static $_cachedResults = array();

	protected $_currentHandle = null;
	protected $_url = null;

	/**
	 * Protected constructor. Use {@link getInstance()} instead.
	 */
	protected function __construct()
	{
	}

	/**
	 * Need to put this method in the abstract class unfortunately because PHP 5.2 doesn't support late static binding
	 */
	protected static function _resolveClass()
	{
		if(class_exists('XenForo_Application'))
		{
			$class = XenForo_Application::resolveDynamicClass('DigitalPointBetterAnalytics_Helper_Reporting');
			self::$_instance = new $class();
		}
		else
		{
			self::$_instance = new DigitalPointBetterAnalytics_Helper_Reporting();
		}
	}

	protected function _postResolveClass()
	{

	}


	/**
	 * Gets the single instance of class.
	 *
	 * @return DigitalPointBetterAnalytics_Helper_Reporting
	 */
	public static final function getInstance()
	{
		if (!self::$_instance)
		{
			self::_resolveClass();
			self::$_instance->_postResolveClass();
		}

		return self::$_instance;
	}

	abstract protected function _getOption($type);

	abstract protected function _saveTokens($tokens);

	abstract protected function _deleteTokens();

	abstract protected function _throwException();

	abstract protected function _showException($message);

	abstract protected function _getAdminAuthUrl();

	abstract protected function _initHttp($url);

	abstract protected function _setParamsAction($params);

	abstract protected function _execHandlerAction($action = 'POST');

	abstract protected function _cacheLoad($cacheKey);

	abstract protected function _cacheSave($cacheKey, $data, $minutes);

	abstract protected function _cacheDelete($cacheKey);



	public function getAuthenticationUrl($state = null)
	{
		return self::$_oAuthEndpoint . 'auth?redirect_uri=' . urlencode($this->_getAdminAuthUrl()) . ($state ? '&state=' . urlencode($state) : '') . '&response_type=code&client_id=' . urlencode($this->_getOption('apiClientId')) . '&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fanalytics&approval_prompt=force&access_type=offline';
	}

	public function exchangeCodeForToken($code)
	{
		$this->_cacheDelete('analytics_profiles');

		$this->_initHttp(self::$_oAuthEndpoint . 'token');
		$this->_setParamsAction(array(
				'code' => $code,
				'redirect_uri' => $this->_getAdminAuthUrl(),
				'client_id' => $this->_getOption('apiClientId'),
				//	'scope' => '',
				'client_secret' => $this->_getOption('apiClientSecret'),
				'grant_type' => 'authorization_code'
			));
		return json_decode($this->_execHandlerAction());
	}

	public function checkAccessToken($throwException = true)
	{
		$tokens = $this->_getOption('tokens');

		if (empty($tokens->refresh_token))
		{
			if ($throwException)
			{
				$this->_throwException();
			}
			else
			{
				return false;
			}
		}

		if ($tokens->expires_at <= time())
		{
			// token has expired... exchange for new one.
			$this->_initHttp(self::$_oAuthEndpoint . 'token');
			$this->_setParamsAction(array(
				'client_id' => $this->_getOption('apiClientId'),
				'client_secret' => $this->_getOption('apiClientSecret'),
				'grant_type' => 'refresh_token',
				'refresh_token' => $tokens->refresh_token
			));
			$response = json_decode($this->_execHandlerAction());

			if (!empty($response->error) && $response->error == 'unauthorized_client')
			{
				$this->_deleteTokens();
				return;
			}
			else
			{
				$tokens->access_token = $response->access_token;
				$tokens->token_type = $response->token_type;
				$tokens->expires_at = time() + $response->expires_in - 100;

				$this->_saveTokens($tokens);
				return $tokens;

			}

		}
		return $tokens;

	}


	public function getProfiles($accountId = '~all', $profileId = '~all')
	{
		$cacheKey = 'ba_prof_' . md5($accountId . '-' . $profileId);

		$profiles = $this->_cacheLoad($cacheKey);

		if (!$profiles)
		{
			$fromCache = false;

			if ($tokens = $this->checkAccessToken())
			{
				if ($profileId)
				{
					$url = sprintf(self::$_webPropertiesEndpoint . self::$_profilesEndpoint, $accountId, $profileId);
				}
				else
				{
					$url = sprintf(self::$_webPropertiesEndpoint, $accountId);
				}

				$this->_initHttp($url);
				$this->_setParamsAction(array('access_token' => $tokens->access_token));

				$response = $this->_execHandlerAction('GET');

				$profiles = json_decode($response, true);

				if (!empty($profiles['error']['errors']))
				{
					$this->_showException(@$profiles['error']['errors'][0]['domain'] . ' / ' . @$profiles['error']['errors'][0]['reason'] . ': ' . @$profiles['error']['errors'][0]['message'] . '  ' . @$profiles['error']['errors'][0]['extendedHelp']);
				}

			}
		}
		else
		{
			$fromCache = true;
		}

		if (!$fromCache)
		{
			$this->_cacheSave($cacheKey, $profiles, 1);
		}
		return $profiles;
	}


	public function getDimensions($accountId = '~all', $propertyId = '~all')
	{
		$cacheKey = 'ba_dim_' . md5($accountId . '-' . $propertyId);

		$dimensions = $this->_cacheLoad($cacheKey);

		// Not caching dimensions (only 1 minute cache anyway)
		$dimensions = false;

		if (!$dimensions)
		{
			$fromCache = false;

			if ($tokens = $this->checkAccessToken())
			{
				if ($propertyId)
				{
					$url = sprintf(self::$_webPropertiesEndpoint . self::$_dimensionsEndpoint, $accountId, $propertyId);
				}
				else
				{
					$url = sprintf(self::$_webPropertiesEndpoint, $accountId);
				}

				$this->_initHttp($url);
				$this->_setParamsAction(array('access_token' => $tokens->access_token));

				$response = $this->_execHandlerAction('GET');

				$dimensions = json_decode($response, true);
			}
		}
		else
		{
			$fromCache = true;
		}

		if (!$fromCache)
		{
			$this->_cacheSave($cacheKey, $dimensions, 1);
		}
		return $dimensions;
	}




	public function getDimensionsByPropertyId($accountId, $propertyId, $names)
	{
		$dimensions = $this->getDimensions($accountId, $propertyId);

		$foundDimensions = array();
		if(!empty($dimensions['items']))
		{
			foreach ($dimensions['items'] as $dimension)
			{
				$key = array_search($dimension['name'], $names);

				if ($key !== false && $dimension['scope'] == 'HIT')
				{
					$foundDimensions[$dimension['name']] = $dimension;
				}
			}
		}

		return $foundDimensions;
	}



	public function getProfileByPropertyId($propertyId)
	{
		$profiles = $this->getProfiles();

		$foundProfile = null;
		if(!empty($profiles['items']))
		{
			foreach ($profiles['items'] as $profile)
			{
				if ($profile['webPropertyId'] == $propertyId)
				{
					$foundProfile = $profile;
					break;
				}
			}
		}

		return $foundProfile;
	}

	// this is a little weird... getting profiles with ~all doesn't return industryVertical, but this does.  Bug on their end?
	public function getPropertyByPropertyId($accountId, $propertyId)
	{
		$profiles = $this->getProfiles($accountId, null);

		$foundProfile = null;
		if(!empty($profiles['items']))
		{
			foreach ($profiles['items'] as $profile)
			{
				if ($profile['id'] == $propertyId)
				{
					$foundProfile = $profile;
					break;
				}
			}
		}

		return $foundProfile;
	}



	public function getWeeklyHeatmap($endDaysAgo, $weeks, $metric, $segment = null)
	{
		$filters = null;

		if (strpos($metric, '|'))
		{
			$split = explode('|', $metric);
			$metric = $split[0];
			$filters = $split[1];
		}

		$cacheKey = $this->getData(($endDaysAgo + ($weeks * 7) - 1) . 'daysAgo', $endDaysAgo . 'daysAgo', $metric, 'ga:hour,ga:dayOfWeek', 'ga:hour,ga:dayOfWeek', $filters, $segment);
		$data = $this->getResults($cacheKey);

		$preparedData = array();

		for ($i = 0; $i < 24; $i++)
		{
			$preparedData[$i] = array_fill(0, 7, 0);
		}

		if (!empty($data['rows']))
		{
			foreach ($data['rows'] as &$row)
			{
				$preparedData[intval($row[0])][intval($row[1])] = intval($row[2]);
			}
		}

		return $preparedData;
	}


	public function getChart($endDaysAgo, $days, $metric, $dimension, $segment = null)
	{
		$filters = null;

		if (strpos($metric, '|'))
		{
			$split = explode('|', $metric);
			$metric = $split[0];
			$filters = $split[1];
		}

		$cacheKey = $this->getData(($endDaysAgo + $days - 1) . 'daysAgo', $endDaysAgo . 'daysAgo', $metric, $dimension, ($dimension == 'ga:date' ? $dimension : '-' . $metric), $filters, $segment);
		$data = $this->getResults($cacheKey);

		if ($dimension == 'ga:date')
		{
			return $data['rows'];
		}

		$chartData = $outputData = array();
		if (!empty($data['rows']))
		{
			foreach($data['rows'] as $row)
			{
				$split = explode(',', $row[0]);
				foreach ($split as $name)
				{
					$name = trim($name);
					@$chartData[$name] += $row[1];
				}
			}
		}

		arsort($chartData);
		if ($chartData)
		{
			foreach ($chartData as $name => $value)
			{
				$outputData[] = array((string)$name, $value);
			}
		}

		return $outputData;
	}



	public function getData($startDate, $endDate, $metrics, $dimensions = null, $sort = null, $filters = null, $segment = null, $samplingLevel = null, $maxResults = 10000, $output = 'json', $userIp = null)
	{
		$profile = $this->_getOption('apiProfile');
		$cacheKey = 'ba_data_' . md5($profile . ' ' . $startDate . ' ' . $endDate . ' ' . $metrics . ' ' . $dimensions . ' ' . $sort . ' ' . $filters . ' ' . $segment . ' ' . $samplingLevel . ' ' . $maxResults . ' ' . $output);

		if (!$data = $this->_cacheLoad($cacheKey))
		{
			$tokens = $this->checkAccessToken();

			$this->_getHandler(self::$_dataEndpoint);

			$params = array(
				'ids' => 'ga:' . $profile,
				'start-date' => $startDate,
				'end-date' => $endDate,
				'metrics' => $metrics,
				'max-results' => $maxResults,
				'output' => $output,
				'access_token' => $tokens->access_token
			);

			if (!empty($dimensions))
			{
				$params['dimensions'] = $dimensions;
			}

			if (!empty($sort))
			{
				$params['sort'] = $sort;
			}

			if (!empty($filters))
			{
				$params['filters'] = $filters;
			}

			if (!empty($segment))
			{
				$params['segment'] = $segment;
			}

			if (!empty($samplingLevel))
			{
				$params['samplingLevel'] = $samplingLevel;
			}

			if (!empty($userIp))
			{
				$params['userIp'] = $userIp;
			}
			elseif (!empty($_SERVER['REMOTE_ADDR']))
			{
				$params['userIp'] = $_SERVER['REMOTE_ADDR'];
			}

			$this->_setParams($params);
			$this->_execHandler($cacheKey);
		}

		return $cacheKey;
	}

	public function getRealtime($metrics, $dimensions = null, $sort = null, $filters = null, $maxResults = 10000)
	{
		$profile = $this->_getOption('apiProfile');
		$cacheKey = 'ba_rt_' . md5($profile . ' ' . $metrics . ' ' . $dimensions . ' ' . $sort . ' ' . $filters . ' ' . $maxResults);

		//if (!$data = self::_cacheLoad($cacheKey))
		//{
		$tokens = $this->checkAccessToken();

		$this->_getHandler(self::$_realtimeEndpoint);

		$params = array(
			'ids' => 'ga:' . $profile,
			'metrics' => $metrics,
			'max-results' => $maxResults,
			'access_token' => $tokens->access_token
		);

		if (!empty($dimensions))
		{
			$params['dimensions'] = $dimensions;
		}

		if (!empty($sort))
		{
			$params['sort'] = $sort;
		}

		if (!empty($filters))
		{
			$params['filters'] = $filters;
		}

		$this->_setParams($params);
		$this->_execHandler($cacheKey);
		//}

		return $cacheKey;
	}

	protected function _canUseCurlMulti()
	{
		return false;
	}


	protected function _getHandler($url)
	{
		$this->_currentHandle = $this->_initHttp($url);
	}

	protected function _setParams(array $params)
	{
		$params['v'] = 1;
		$params['ds'] = 'server side';

		$this->_setParamsAction($params);
	}


	protected function _execHandler($cacheKey)
	{
		$this->_currentHandle = $this->_execHandlerAction('GET');

		self::$_curlHandles[$cacheKey] = $this->_currentHandle;
	}



	public function getResults($cacheKey)
	{
		$results = @json_decode(self::$_curlHandles[$cacheKey], true);
		$this->_cacheSave($cacheKey, $results, 60);


		if (!empty(self::$_cachedResults[$cacheKey]))
		{
			return self::$_cachedResults[$cacheKey];
		}
		else
		{
			return false;
		}
	}
}