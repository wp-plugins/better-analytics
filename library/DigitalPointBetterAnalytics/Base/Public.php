<?php

class DigitalPointBetterAnalytics_Base_Public
{
	protected static $_instance;

	/**
	 * Protected constructor. Use {@link getInstance()} instead.
	 */
	protected function __construct()
	{
	}

	public static final function getInstance()
	{
		if (!self::$_instance)
		{
			$class = __CLASS__;
			self::$_instance = new $class;

			self::$_instance->_initHooks();
		}

		return self::$_instance;
	}

	public static function autoload($class)
	{
		$filename = self::_autoloaderClassToFile($class);
		if (!$filename)
		{
			return false;
		}

		$proLocation = substr_replace(BETTER_ANALYTICS_PLUGIN_DIR, '-pro', -1);

		$allowed = false;

		if (file_exists($proLocation . $filename))
		{
			$betterAnalyticsInternal = get_transient('ba_int');

			if (@$betterAnalyticsInternal['v'] || strpos(@$_SERVER['SCRIPT_NAME'], '/plugins.php') !== false || strpos(@$_SERVER['SCRIPT_NAME'], '/tools.php') !== false || $class == 'DigitalPointBetterAnalytics_Helper_Api')
			{
				$allowed = true;
			}
		}

		if($allowed)
		{
			include($proLocation . $filename);
			return (class_exists($class, false) || interface_exists($class, false));
		}
		elseif (file_exists(BETTER_ANALYTICS_PLUGIN_DIR . $filename))
		{
			include(BETTER_ANALYTICS_PLUGIN_DIR . $filename);
			return (class_exists($class, false) || interface_exists($class, false));
		}
	}

	protected static function _autoloaderClassToFile($class)
	{
		if (preg_match('#[^a-zA-Z0-9_\\\\]#', $class))
		{
			return false;
		}

		return '/library/' . str_replace(array('_', '\\'), '/', $class) . '.php';
	}



	/**
	 * Initializes WordPress hooks
	 */
	protected function _initHooks()
	{
		add_action('wp_loaded', array($this, 'track_blocked' ));

		add_action('wp_head', array($this, 'insert_code_head' ));
		add_action('wp_footer', array($this, 'insert_code_footer' ));

		add_action('admin_bar_menu', array($this, 'admin_bar_menu'), 100);

		add_action('user_register', array($this, 'user_register' ));

		add_action('wp_insert_comment', array($this, 'insert_comment'), 10, 2);

		add_filter('wp_mail', array($this, 'filter_mail' ));
		add_filter('the_permalink_rss', array($this, 'fiter_rss_links' ));
		add_filter('the_content_feed', array($this, 'filter_rss_content' ));

		add_action('better_analytics_cron_minutely', array('DigitalPointBetterAnalytics_CronEntry_Jobs', 'minute' ));
		add_action('better_analytics_cron_hourly', array('DigitalPointBetterAnalytics_CronEntry_Jobs', 'hour' ));
	}


	public function canViewReports()
	{
		$currentUser = wp_get_current_user();
		$betterAnalyticsOptions = get_option('better_analytics');

		if (array_intersect((array)$currentUser->roles, (array)@$betterAnalyticsOptions['roles_view_reports']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}



	/**
	 * Do something on activation?
	 * @static
	 */
	public static function plugin_activation()
	{
		if (version_compare($GLOBALS['wp_version'], BETTER_ANALYTICS_MINIMUM_WP_VERSION, '<' ))
		{
			$message = sprintf(esc_html__('%1$sBetter Analytics %4$s requires WordPress %5$s or higher.%2$s%3$sPlease %6$supgrade WordPress%7$s to a current version.', 'better-analytics'),
				'<strong>',
				'</strong>',
				'<br />',
				BETTER_ANALYTICS_VERSION,
				BETTER_ANALYTICS_MINIMUM_WP_VERSION,
				'<a href="' . esc_url('https://codex.wordpress.org/Upgrading_WordPress') .  '" target="_blank">',
				'</a>'
			);

			self::_bailOnActivation($message);
		}

		wp_schedule_event(time(), 'minutely', 'better_analytics_cron_minutely');
		wp_schedule_event(time(), 'hourly', 'better_analytics_cron_hourly');

		if (!get_option('better_analytics'))
		{
			$_defaultOptions = array(
				'property_id' => '',
				'track_userid' => 1,
				'events' => array(
					'user_registration' => 1,
					'create_comment' => 1,
					'youtube' => 1,
					'email' => 1,
					'link_click' => 1,
					'downloads' => 1,
					'missing_images' => 1,
					'error_404' => 1,
					'ajax_request' => 1,
					'error_js' => 1,
					'error_ajax' => 1,
					'error_console' => 1,
					'error_youtube' => 1
				),
				'demographic_tracking' => 1,
				'force_ssl' => 1,
				'source_link' => array(
					'rss' => 1,
					'email' => 1
				),
				'roles_view_reports' => array('administrator'),
				'file_extensions' => array(
					'avi', 'doc', 'exe', 'gz', 'mpg', 'mp3', 'pdf', 'ppt', 'psd', 'rar', 'wmv', 'xls', 'zip'
				),
				'track_blocked' => 'never',
				'javascript' => array(
					'location' => 'header',
					'run_time' => 'ready'
				),
				'campaign_tracking' => 'anchor',

				'sample_rate' => 100
			);

			update_option('better_analytics', $_defaultOptions);
		}

		DigitalPointBetterAnalytics_Helper_Api::check(true);

	}

	/**
	 * Do something on deactivation?
	 * @static
	 */
	public static function plugin_deactivation()
	{
		wp_clear_scheduled_hook('better_analytics_cron_minutely');
		wp_clear_scheduled_hook('better_analytics_cron_hourly');
	}


	private static function _bailOnActivation($message, $deactivate = true)
	{
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="<?php bloginfo('charset'); ?>">
			<style>
				* {
					text-align: center;
					margin: 0;
					padding: 0;
					font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
				}
				p {
					margin-top: 1em;
					font-size: 18px;
				}
			</style>
		<body>
		<p><?php echo ($message ); ?></p>
		</body>
		</html>
		<?php
		if ($deactivate)
		{
			$plugins = get_option('active_plugins');
			$betterAnalytics = plugin_basename(BETTER_ANALYTICS_PLUGIN_DIR . 'better-analytics.php');
			$update  = false;
			foreach ($plugins as $i => $plugin )
			{
				if ($plugin === $betterAnalytics)
				{
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ($update)
			{
				update_option( 'active_plugins', array_filter($plugins));
			}
		}
		exit;
	}



	public function track_blocked()
	{
		wp_enqueue_script('jquery');

		DigitalPointBetterAnalytics_Base_Pro::track_blocked();
	}


	public function insert_code_head()
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		if ($betterAnalyticsOptions['javascript']['location'] == 'header')
		{
			include(BETTER_ANALYTICS_PLUGIN_DIR . 'js/universal.php');
		}
	}

	public function insert_code_footer()
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		if ($betterAnalyticsOptions['javascript']['location'] == 'footer')
		{
			include(BETTER_ANALYTICS_PLUGIN_DIR . 'js/universal.php');
		}
	}

	public function admin_bar_menu($wp_admin_bar)
	{
		if ($this->canViewReports())
		{
			$wp_admin_bar->add_node(array(
				'id' => 'analytics',
				'title' => '<span class="ab-icon"></span><span id="ab-analytics" class="ab-label analytics">' . esc_html__('Page Analytics', 'better-analytics') . '</span>',
				'href' => get_admin_url(null, 'admin.php?page=better-analytics_heatmaps&page_path=' . urlencode($_SERVER['REQUEST_URI'])),
				'meta' => array('target' => '_blank')
			));

			$_menu = array(
				'heatmaps' => 'Heat Maps',
				'areacharts' => 'Charts',
				'events' => 'Events',
				'monitor' => 'Issue Monitor'
			);
			foreach ($_menu as $id => $title)
			{
				$wp_admin_bar->add_node(array(
					'parent' => 'analytics',
					'id' => $id,
					'title' => esc_html__($title, 'better-analytics'),
					'href' => get_admin_url(null, 'admin.php?page=better-analytics_' . $id . '&page_path=' . urlencode($_SERVER['REQUEST_URI'])),
					'meta' => array('target' => '_blank')
				));
			}

			echo '<style>
			#wpadminbar #wp-admin-bar-analytics .ab-icon:before {
    			content: "\\f238";
    			top: 4px;
			}
			@media screen and (max-width: 782px) {
					#wpadminbar li#wp-admin-bar-analytics {
					display: block;
				}
			}
		</style>';

		}

	}




	public function user_register($userId)
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if ($betterAnalyticsOptions['events']['user_registration'])
		{
			$analyticsClientId = self::getAnalyticsId();

			DigitalPointBetterAnalytics_Helper_Analytics::getInstance()->event(
				$betterAnalyticsOptions['property_id'],
				$analyticsClientId,
				$userId,
				@$_SERVER['REMOTE_ADDR'],
				'User',
				'Registration'
			);
		}
	}

	public function insert_comment($id, $commentObject)
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if ($betterAnalyticsOptions['events']['create_comment'])
		{
			$analyticsClientId = self::getAnalyticsId();

			DigitalPointBetterAnalytics_Helper_Analytics::getInstance()->event(
				$betterAnalyticsOptions['property_id'],
				$analyticsClientId,
				$commentObject->user_id,
				@$_SERVER['REMOTE_ADDR'],
				'Content',
				'Comment',
				@$_SERVER['HTTP_REFERER']

			);
		}
	}

	public function fiter_rss_links($link)
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if (is_feed() && $betterAnalyticsOptions['source_link']['rss'])
		{
			if ($betterAnalyticsOptions['$betterAnalyticsOptions'] == 'anchor')
			{
				$urlDelimiter = '#';
			}
			elseif (strpos($link, '?') !== false)
			{
				$urlDelimiter = '&amp;';
			}
			else
			{
				$urlDelimiter = '?';
			}
			return $link . $urlDelimiter . 'utm_source=rss&amp;utm_medium=rss'; // '&amp;utm_campaign=' . urlencode($post->post_title)
		}

		return $link;
	}

	public function filter_rss_content ($content)
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if ($betterAnalyticsOptions['source_link']['rss'])
		{
			$content = preg_replace_callback(
				'#(?<=[^a-z0-9@-]|^)(https?://|www\.)[^\s"><]+#iu',
				array(__CLASS__, '_autoLinkUrlCallbackRss'),
				$content
			);
		}
		return $content;
	}


	public function filter_mail($atts)
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		$betterAnalyticsInternal = get_transient('ba_int');

		if ($betterAnalyticsOptions['source_link']['email'])
		{
			$atts['message'] = preg_replace_callback(
				'#(?<=[^a-z0-9@-]|^)(https?://|www\.)[^\s"><]+#iu',
				array(__CLASS__, '_autoLinkUrlCallbackEmail'),
				$atts['message']
			);
		}

		if (!empty($betterAnalyticsOptions['property_id']) && @$betterAnalyticsOptions['events']['email'] && @$betterAnalyticsInternal['v'])
		{
			$analyticsClientId = uniqid();

			$atts['message'] = '<html><body>' . nl2br(htmlentities($atts['message'])) . '<img src="https://www.google-analytics.com/collect?v=1&tid=' . urlencode($betterAnalyticsOptions['property_id']) . '&cid=' . $analyticsClientId . '&t=event&ec=Email&ea=Open&el=' . urlencode($atts['subject']) . '&cm=email&z=' . uniqid() . '" /></body></html>';
			$atts['headers']['content_type'] = 'Content-Type: text/html; charset=UTF-8';

			DigitalPointBetterAnalytics_Helper_Analytics::getInstance()->event(
				$betterAnalyticsOptions['property_id'],
				$analyticsClientId,
				null,
				null,
				'Email',
				'Send',
				$atts['subject'],
				'email',
				true
			);
		}

		return $atts;
	}


	public static function filter_cron_schedules($schedules)
	{
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => esc_html__('Every Minute', 'better-analytics')
		);
		return $schedules;
	}

	private static function _autoLinkUrlCallbackEmail($match)
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if (@$betterAnalyticsOptions['$betterAnalyticsOptions'] == 'anchor')
		{
			$urlDelimiter = '#';
		}
		elseif (strpos($match[0], '?') !== false)
		{
			$urlDelimiter = '&';
		}
		else
		{
			$urlDelimiter = '?';
		}

		return $match[0] . $urlDelimiter . 'utm_source=email&utm_medium=email';

	}

	private static function _autoLinkUrlCallbackRss($match)
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if ($betterAnalyticsOptions['$betterAnalyticsOptions'] == 'anchor')
		{
			$urlDelimiter = '#';
		}
		elseif (strpos($match[0], '?') !== false)
		{
			$urlDelimiter = '&';
		}
		else
		{
			$urlDelimiter = '?';
		}

		return $match[0] . $urlDelimiter . 'utm_source=rss&utm_medium=rss';
	}


	public function usingMultisiteTokens()
	{
		if (is_multisite())
		{
			if (get_site_option('ba_site_tokens'))
			{
				return true;
			}
		}
		return false;
	}

	public function getTokens()
	{
		if (is_multisite())
		{
			if (!$tokens = get_site_option('ba_site_tokens'))
			{
				$tokens = get_option('ba_tokens');
			}
		}
		else
		{
			$tokens = get_option('ba_tokens');
		}

		return $tokens;
	}

	public function updateTokens($tokens, $forceMultisite = false)
	{
		if (!is_string($tokens))
		{
			$tokens = json_encode($tokens);
		}
		if ($forceMultisite || $this->usingMultisiteTokens())
		{
			update_site_option('ba_site_tokens', $tokens);
		}
		else
		{
			update_option('ba_tokens', $tokens);
		}
	}

	public function deleteTokens()
	{
		if ($this->usingMultisiteTokens())
		{
			delete_site_option('ba_site_tokens');
		}
		else
		{
			delete_option('ba_tokens');
		}
	}



	/**
	 * Log debugging info to the error log.
	 *
	 * Enabled when WP_DEBUG_LOG is enabled, but can be disabled via the better_analytics_debug_log filter.
	 *
	 * @param mixed $better_analytics_debug The data to log.
	 */
	public static function log( $better_analytics_debug )
	{
		if (apply_filters( 'better_analytics_debug_log', defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ))
		{
			error_log( print_r( compact( 'better_analytics_debug' ), true ) );
		}
	}

	public static function getAnalyticsId()
	{
		if (!empty($_COOKIE['_ga']))
		{
			$analyticsClientId = $_COOKIE['_ga'];
		}
		else
		{
			$analyticsClientId = null;
		}

		return $analyticsClientId;
	}
}