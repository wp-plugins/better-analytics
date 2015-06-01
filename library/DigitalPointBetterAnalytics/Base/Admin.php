<?php

class DigitalPointBetterAnalytics_Base_Admin
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

	protected function _initHooks()
	{
		add_action('admin_init', array($this, 'admin_init'), 20);
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_head', array($this, 'admin_head'));

		add_action( 'wp_dashboard_setup', array($this, 'dashboard_setup'));

		add_action('wp_ajax_better-analytics_heatmaps', array($this, 'display_page'));
		add_action('wp_ajax_better-analytics_area_charts', array($this, 'display_page'));
		add_action('wp_ajax_better-analytics_monitor', array($this, 'display_page'));
		add_action('wp_ajax_better-analytics_events', array($this, 'display_page'));

		add_action('wp_ajax_better-analytics_charts', array($this, 'display_charts'));

		add_filter('plugin_action_links', array($this, 'plugin_action_links' ), 10, 2);
		add_filter('wp_redirect', array($this, 'filter_redirect'));
		add_filter( 'admin_footer_text', array($this, 'admin_footer_text' ));
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		$betterAnalyticsOptions = get_option('better_analytics');
		if (!$betterAnalyticsOptions['property_id'])
		{
			add_action( 'admin_notices', array($this, 'not_configured' ) );
		}

		if (get_transient('ba_last_error'))
		{
			add_action( 'admin_notices', array($this, 'last_error' ) );
		}
	}

	public function admin_init()
	{
		register_setting('better-analytics-group', 'better_analytics');

		// allows us to use a redirect on the better_analytics-auth page
		ob_start();
	}


	public function admin_menu()
	{
		$hook = add_management_page( __('Test Analytics Setup', 'better-analytics'), __('Test Analytics Setup', 'better-analytics'), 'manage_options', 'better-analytics_test', array($this, 'display_test_page') );
		$hook = add_management_page( __('OAuth2 Endpoint', 'better-analytics'), __('Oauth2 Endpoint', 'better-analytics'), 'manage_options', 'better-analytics_auth', array($this, 'api_authentication') );


		if ($this->canViewReports())
		{
			$hook = add_menu_page(__('Analytics', 'better-analytics'), __('Analytics', 'better-analytics'), 'read', 'better-analytics_heatmaps', null, 'dashicons-chart-line', 3.1975123 );
			$hook = add_submenu_page( 'better-analytics_heatmaps', __('Heat Maps', 'better-analytics'), __('Reports', 'better-analytics'), 'read', 'better-analytics_heatmaps', array($this, 'display_page') );

			$hook = add_submenu_page( 'better-analytics_heatmaps', __('Charts', 'better-analytics'), __('Charts', 'better-analytics'), 'read', 'better-analytics_areacharts', array($this, 'display_page') );
			$hook = add_submenu_page( 'better-analytics_heatmaps', __('Issue Monitor', 'better-analytics'), __('Issue Monitor', 'better-analytics'), 'read', 'better-analytics_monitor', array($this, 'display_page') );
			$hook = add_submenu_page( 'better-analytics_heatmaps', __('Events', 'better-analytics'), __('Events', 'better-analytics'), 'read', 'better-analytics_events', array($this, 'display_page') );
		}


		$hook = add_submenu_page( 'better-analytics_heatmaps', __('Settings', 'better-analytics'), __('Settings', 'better-analytics'), 'manage_options', 'options-general.php' . '?page=better-analytics' );
		$hook = add_submenu_page( 'better-analytics_heatmaps', __('Test Setup', 'better-analytics'), __('Test Setup', 'better-analytics'), 'manage_options', 'tools.php' . '?page=better-analytics_test' );


		$hook = add_options_page( __('Better Analytics', 'better-analytics'), __('Better Analytics', 'better-analytics'), 'manage_options', 'better-analytics', array($this, 'display_configuration_page'));
		add_action( "load-$hook", array($this, 'admin_help'));
	}


	public function plugin_action_links( $links, $file)
	{
		if ($file == plugin_basename(BETTER_ANALYTICS_PLUGIN_DIR . '/better-analytics.php'))
		{
			$betterAnalyticsInternal = get_transient('ba_int');

			wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

			$links['settings'] = '<a href="' . esc_url(menu_page_url('better-analytics', false)) . '">' . esc_html__('Settings' , 'better-analytics').'</a>';

			krsort($links);
			end($links);
			$key = key($links);
			$links[$key] .= '<p class="' . (DigitalPointBetterAnalytics_Base_Pro::$installed && @$betterAnalyticsInternal['v'] && @$betterAnalyticsInternal['l'] == DigitalPointBetterAnalytics_Base_Pro::$version ? 'green' : 'orange') . '"> ' .
				(DigitalPointBetterAnalytics_Base_Pro::$installed ?
					(@$betterAnalyticsInternal['v'] ?
						(@$betterAnalyticsInternal['l'] != DigitalPointBetterAnalytics_Base_Pro::$version ?
							sprintf('<a href="%1$s" target="_blank">%2$s</a><br />%3$s %4$s<br />%5$s %6$s', esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin'), __('Pro version not up to date.', 'better-analytics'), __('Installed:', 'better-analytics'), DigitalPointBetterAnalytics_Base_Pro::$version, __('Latest:', 'better-analytics'), @$betterAnalyticsInternal['l']) :
							sprintf('<a href="%1$s" target="_blank">%2$s</a> (%3$s)', esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin'), __('Pro version installed', 'better-analytics'), @$betterAnalyticsInternal['l'])
						) :
						sprintf(__('Pro version installed, but not active.  Did you %1$sverify ownership of your domain%2$s?', 'better-analytics'), '<a href="' . esc_url('https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">', '</a>')
					) :
					sprintf('<a href="%1$s" target="_blank">%2$s</a>', __('Pro version not installed.', 'better-analytics'), esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin'))
				) .
				'</p>';
		}

		return $links;
	}

	public function admin_head()
	{
		remove_submenu_page( 'tools.php', 'better-analytics_auth' );

		$_reportingPages = array(
			'better-analytics_heatmaps',
			'better-analytics_areacharts',
			'better-analytics_monitor',
			'better-analytics_events'
		);

		$currentPage = (empty($GLOBALS['plugin_page']) || array_search($GLOBALS['plugin_page'], $_reportingPages) === false ? $_reportingPages[0] : $GLOBALS['plugin_page']);

		foreach($_reportingPages as $page)
		{
			if ($currentPage != $page)
			{
				remove_submenu_page( 'better-analytics_heatmaps', $page);
			}
		}
		
		$betterAnalyticsOptions = get_option('better_analytics');
		if (@$betterAnalyticsOptions['javascript']['use_in_admin'])
		{
			include(BETTER_ANALYTICS_PLUGIN_DIR . 'js/universal.php');
		}

	}

	public function dashboard_setup()
	{
		if ($this->canViewReports())
		{
			wp_add_dashboard_widget(
				'better-analytics',
				__('Better Analytics', 'better-analytics'),
				array($this, 'dashboard_display')
			);
		}
	}

	public function dashboard_display()
	{
		$this->view('dashboard');
	}

	public function filter_redirect($location)
	{
		// Kind of a janky way to redirect back to the right tab... boo.
		if (strpos($location, '/wp-admin/options-general.php?page=better-analytics&settings-updated=true') !== false && !empty($_POST['current_tab']))
		{
			$location .= '#top#' . $_POST['current_tab'];
		}

		return $location;
	}

	public function not_configured()
	{
		$this->_displayError(sprintf('%1$s<p><a href="%2$s" class="button button-primary">%3$s</a></p>', __('Google Analytics Web Property ID not selected.', 'better-analytics'), esc_url(menu_page_url('better-analytics', false)), __('Settings', 'better-analytics')));
	}

	public function last_error()
	{
		$this->_displayError(sprintf('<strong>%1$s</strong><br /><br />%2$s', __('Last Analytics Error:'), get_transient('ba_last_error')));
	}

	protected function _displayError($error)
	{
		echo '<div class="error"><p>' . $error . '</p></div>';

	}


	public function display_configuration_page()
	{
		$this->view('config');
	}

	public function display_test_page()
	{
		$this->view('test');
	}

	public function display_page()
	{
		if ($this->canViewReports())
		{
			global $plugin_page;

			$method = 'action' . ucwords(strtolower(preg_replace('#[^a-z0-9]#i', '', substr($plugin_page ? $plugin_page : @$_REQUEST['action'], 17))));

			$controller = $this->_getController();
			if (method_exists($controller, $method))
			{
				$this->_getController()->$method();
			}
			else
			{
				echo sprintf('%1$s %2$s', __('Invalid method:', 'better-analytics'), $method);
			}
		}
	}


	public function display_charts()
	{
		if ($this->canViewReports())
		{
			$this->_getController()->actionCharts();
		}
	}


	public function api_authentication()
	{
		if(!empty($_REQUEST['code']))
		{
			$code = $_REQUEST['code'];

			$response = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->exchangeCodeForToken($code);

			if (!empty($response->error) && !empty($response->error_description))
			{
				echo sprintf('%1$s<br /><br /><b>%2$s</b>: %3$s', __('Invalid Google API Code:', 'better-analytics'), $response->error, $response->error_description);
				return;
			}

			if (empty($response->expires_in))
			{
				echo sprintf('%1$s:<br /><br />%2$s', __('Unknown Google API Error:', 'better-analytics'), nl2br(var_export($response, true)));
				return;
			}

			$response->expires_at = time() + $response->expires_in - 100;
			unset($response->expires_in);

			update_option('ba_tokens', json_encode($response));
			DigitalPointBetterAnalytics_CronEntry_Jobs::hour(true);

			wp_redirect(menu_page_url('better-analytics', false) . '#top#api', 302);

			return;
		}

		wp_redirect(DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getAuthenticationUrl(menu_page_url('better-analytics_auth', false)), 302);
	}



	/**
	 * Add help to the Better Analytics page
	 *
	 * @return false if not the Better Analytics page
	 */
	public function admin_help() {
		$current_screen = get_current_screen();

		// Screen Content
		if ( current_user_can( 'manage_options' ))
		{
			//configuration page
			$current_screen->add_help_tab(
				array(
					'id'		=> 'overview',
					'title'		=> __( 'Overview' , 'better-analytics'),
					'content'	=>
						'<p><strong>' . esc_html__( 'Better Analytics' , 'better-analytics') . '</strong></p>' .
						'<p>' . esc_html__( 'At the most basic level, it will automatically add Google Analytics Universal code to your website.  It gives you the flexibility to track virtually everything about your site.  From page views to YouTube video engagement (and everything in between).' , 'better-analytics') . '</p>',
				)
			);

			$current_screen->add_help_tab(
				array(
					'id'		=> 'pro',
					'title'		=> __( 'Pro' , 'better-analytics'),
					'content'	=>
						'<p><strong>' . esc_html__( 'Pro Version' , 'better-analytics') . '</strong></p>' .
						'<p>' . esc_html__( 'There is a Pro version of this plugin that gives you a few added features.  More metrics/dimensions, more tracking options, etc.' , 'better-analytics') . '</p>' .
						''
				)
			);

		}

		// Help Sidebar
		$current_screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:' , 'better-analytics') . '</strong></p>' .
			'<p><a href="' . esc_url(BETTER_ANALYTICS_PRODUCT_URL . '#utm_source=admin_settings_help&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">'     . esc_html__( 'Info' , 'better-analytics') . '</a></p>' .
			'<p><a href="' . esc_url(BETTER_ANALYTICS_SUPPORT_URL . '#utm_source=admin_settings_help&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">' . esc_html__( 'Support' , 'better-analytics') . '</a></p>' .
			'<p><a href="' . esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_settings_help&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">' . esc_html__( 'Pro' , 'better-analytics') . '</a></p>'

		);
	}


	public function admin_footer_text($footerText)
	{
		$currentScreen = get_current_screen();

		if (isset($currentScreen->id) && strpos($currentScreen->id, 'better-analytics') !== false)
		{
			$_type = array(__('colossal', 'better-analytics'), __('elephantine', 'better-analytics'), __('glorious', 'better-analytics'), __('grand', 'better-analytics'), __('huge', 'better-analytics'), __('mighty', 'better-analytics'), sprintf('<span class="tooltip" title="%1$s">%2$s</span>', __('WTF?', 'better-analytics'), __('sexy', 'better-analytics')));
			$_type = $_type[array_rand($_type)];
			if (strpos($_type, '"tooltip"') !== false)
			{
				wp_enqueue_script('tooltipster_js', esc_url(BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/js/jquery.tooltipster.min.js'), array(), BETTER_ANALYTICS_VERSION );
				wp_enqueue_style('tooltipster_css', esc_url(BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/css/tooltipster.css'), array(), BETTER_ANALYTICS_VERSION);
			}

			$footerText = sprintf(__('If you like %1$s, please leave us a %2$s rating. A %3$s thank you in advance!', 'better-analytics'),
				'<strong>' . __('Better Analytics', 'better-analytics') . '</strong>',
				'<a href="https://wordpress.org/support/view/plugin-reviews/better-analytics?filter=5#postform" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
				$_type
			);
		}
		return $footerText;
	}

	public function plugin_row_meta($links, $file)
	{
		if ($file == plugin_basename(BETTER_ANALYTICS_PLUGIN_DIR . '/better-analytics.php'))
		{
			$links['support'] = '<a href="' . esc_url(BETTER_ANALYTICS_SUPPORT_URL ) . '" title="' . esc_attr( __( 'Visit Support Forum', 'better-analytics' ) ) . '">' . __( 'Support', 'better-analytics' ) . '</a>';
		}

		return $links;
	}

	public static function getProfilePropertyIds($profiles)
	{
		$profilesOutput = array();

		if (count($profiles) > 0)
		{
			foreach ($profiles as $profile)
			{
				if (empty($profilesOutput[$profile['webPropertyId']]))
				{
					$profilesOutput[$profile['webPropertyId']] = array($profile['websiteUrl'], $profile['name']);
				}
			}
		}

		return $profilesOutput;
	}


	public static function groupProfiles($profiles)
	{
		$profileOptions = array();

		if (!empty($profiles))
		{
			$internalWebPropertyId = null;
			$groupName = null;
			$group = array();

			foreach ($profiles as &$profile)
			{

				if ($profile['internalWebPropertyId'] != $internalWebPropertyId)
				{
					if (!empty($groupName))
					{
						$profileOptions[$groupName] = $group;
					}
					$group = array();
					$groupName = $profile['websiteUrl'];
				}
				$group[$profile['id']] = $profile['name'];

				$internalWebPropertyId = $profile['internalWebPropertyId'];
			}
			$profileOptions[$groupName] = $group;

		}
		return $profileOptions;
	}

	public function view($name, array $args = array())
	{
		// Shouldn't happen, but sanitize anyway
		$name = preg_replace('#[^a-z0-9\/\_\-]#i' ,'', $name);

		$args = apply_filters('better_analytics_view_arguments', $args, $name);

		foreach ($args AS $key => $val)
		{
			$$key = $val;
		}

		include(BETTER_ANALYTICS_PLUGIN_DIR . 'library/DigitalPointBetterAnalytics/ViewAdmin/'. $name . '.php');
	}

	protected function _getController()
	{
		return new DigitalPointBetterAnalytics_ControllerAdmin_Analytics();
	}

}