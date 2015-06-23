<?php

class DigitalPointBetterAnalytics_Helper_Reporting extends DigitalPointBetterAnalytics_Helper_Reporting_Advanced
{
	protected $_credentials = array(
		'client_id' => '416831151869-fks5s6f1d9q3a1j6ua0158hqclde21ta.apps.googleusercontent.com',
		'client_secret' => 'zkhfn3qPn0y-Dg8ZXHBeHBR9',
		'auth_url' => 'https://api.digitalpoint.com/v1/better-analytics/link'
	);

	protected $_urlInfo = array();

	protected function _getOption($option)
	{
		switch ($option)
		{
			case 'apiClientId':
				$betterAnalyticsSiteOptions = get_site_option('better_analytics_site');
				$betterAnalyticsOptions = get_option('better_analytics');
				if (!empty($betterAnalyticsSiteOptions['api']['use_own']))
				{
					return @$betterAnalyticsSiteOptions['api']['client_id'];
				}
				elseif (!empty($betterAnalyticsOptions['api']['use_own']))
				{
					return @$betterAnalyticsOptions['api']['client_id'];
				}
				else
				{
					return $this->_credentials['client_id'];
				}
			case 'apiClientSecret':
				$betterAnalyticsSiteOptions = get_site_option('better_analytics_site');
				$betterAnalyticsOptions = get_option('better_analytics');
				if (!empty($betterAnalyticsSiteOptions['api']['use_own']))
				{
					return @$betterAnalyticsSiteOptions['api']['client_secret'];
				}
				elseif (!empty($betterAnalyticsOptions['api']['use_own']))
				{
					return @$betterAnalyticsOptions['api']['client_secret'];
				}
				else
				{
					return $this->_credentials['client_secret'];
				}
			case 'apiProfile':
				$betterAnalyticsOptions = get_option('better_analytics');
				return @$betterAnalyticsOptions['api']['profile'];

			case 'tokens':
				return @json_decode(DigitalPointBetterAnalytics_Base_Public::getInstance()->getTokens());

			case 'internalV':
				$betterAnalyticsOptions = get_transient('ba_int');
				return @$betterAnalyticsOptions['v'];

			default:
				return false;
		}
	}

	protected function _saveTokens($tokens)
	{
		DigitalPointBetterAnalytics_Base_Public::getInstance()->updateTokens($tokens);
	}

	protected function _deleteTokens()
	{
		DigitalPointBetterAnalytics_Base_Public::getInstance()->deleteTokens();
	}

	protected function _throwException()
	{
		$this->_cacheSave('ba_last_error', __('No API tokens to refresh.', 'better-analytics'), 0.15); // 9 seconds

		return;
	}

	protected function _showException($message)
	{
		$this->_cacheSave('ba_last_error', $message, 0.15); // 9 seconds
		error_log($message);
		return;
	}

	protected function _getAdminAuthUrl()
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		if (@$betterAnalyticsOptions['api']['use_own'])
		{
			return menu_page_url('better-analytics_auth', false);
		}
		else
		{
			return $this->_credentials['auth_url'];
		}
	}


	protected function _initHttp($url)
	{
		$this->_urlInfo['url'] = $url;
	}

	protected function _setParamsAction($params)
	{
		$this->_urlInfo['params'] = $params;
	}

	protected function _execHandlerAction($action = 'POST')
	{
		if ($action == 'POST')
		{
			$response = wp_remote_post($this->_urlInfo['url'],
				array(
					'body' => $this->_urlInfo['params']
				)
			);
		}
		else
		{
			$response = wp_remote_get($this->_urlInfo['url'] . '?' . http_build_query($this->_urlInfo['params']));
		}

		return $response['body'];
	}


	protected function _cacheLoad($cacheKey)
	{
		$result = get_transient($cacheKey);

		self::$_cachedResults[$cacheKey] = $result;

		return $result;
	}

	protected function _cacheSave($cacheKey, $data, $minutes)
	{
		if (!empty($data['id']) || !empty($data['totalResults']))
		{
			set_transient($cacheKey, $data, intval($minutes * 60));

			self::$_cachedResults[$cacheKey] = $data;
		}
		elseif (!empty($data['error']['message']))
		{
			set_transient('ba_last_error', $data['error']['message'], 10);
		}
	}

	protected function _cacheDelete($cacheKey)
	{
		delete_transient($cacheKey);
	}

}