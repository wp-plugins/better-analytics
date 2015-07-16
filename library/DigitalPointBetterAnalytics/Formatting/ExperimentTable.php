<?php

class DigitalPointBetterAnalytics_Formatting_ExperimentTable extends WP_List_Table
{
	public function __construct( $args = array())
	{
		global $status;

		$validStatuses = DigitalPointBetterAnalytics_Model_Experiments::getStatuses();
		$validStatuses = array_keys($validStatuses);
		$validStatuses = array_map('strtolower', $validStatuses);

		$status = 'all';
		if (isset( $_REQUEST['experiment_status'] ) && in_array( $_REQUEST['experiment_status'], $validStatuses))
		{
			$status = $_REQUEST['experiment_status'];

			$experiments = array();

			if (!empty($args['experiments']) && is_array($args['experiments']))
			{
				foreach ($args['experiments'] as $key => $experiment)
				{
					if ($_REQUEST['experiment_status'] == 'active' && $experiment['active'])
					{
						$experiments[$key] = $experiment;
					}
					elseif ($_REQUEST['experiment_status'] == 'inactive' && !$experiment['active'])
					{
						$experiments[$key] = $experiment;
					}
				}

				$args['experiments'] = $experiments;
			}
		}

		// because this isn't hacky, right?  lol
		if (in_array(@$_GET['action'], $validStatuses) !== false)
		{
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('id', 'action', '_wpnonce'), $_SERVER['REQUEST_URI']);
		}

		parent::__construct($args);
	}

	protected function _getCurrentUrl()
	{
		return set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}

	public function ajax_user_can()
	{
		return current_user_can('manage_options');
	}

	public function prepare_items()
	{
		global $totals, $status;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->items = @$this->_args['experiments']['items'];

		if (!empty($_REQUEST['orderby']))
		{
			$sortOrders = array(
				'name' => array('key' => 'name', 'type' => SORT_STRING),
				'status' => array('key' => 'status', 'type' => SORT_STRING),
				'winner' => array('key' => 'winnerFound', 'type' => SORT_STRING),
				'created' => array('key' => 'created', 'type' => SORT_STRING),
				'updated' => array('key' => 'updated', 'type' => SORT_STRING),
			);

			if (!empty($sortOrders[$_REQUEST['orderby']]))
			{
				$sortOrder = array();

				foreach($this->items as $item)
				{
					$sortOrder[] = strtolower($item[$sortOrders[$_REQUEST['orderby']]['key']]);
				}

				array_multisort($sortOrder, (@$_REQUEST['order'] == 'desc' ? SORT_DESC : SORT_ASC), $sortOrders[$_REQUEST['orderby']]['type'], $this->items);
			}
		}

		$this->set_pagination_args( array(
			'total_items' => $totals[$status],
			'per_page' => 1000,
		));
	}

	protected function get_table_classes()
	{
		return array('experiments', 'widefat', $this->_args['plural'] );
	}

	public function get_columns()
	{
		return array(
			'cb'		=> '<input type="checkbox" />',
			'name'		=> esc_html__('Name', 'better-analytics'),
			'status'		=> esc_html__('Status', 'better-analytics'),
			'winner'		=> esc_html__('Winner', 'better-analytics'),
			'created'	=> esc_html__('Created', 'better-analytics'),
			'updated'	=> esc_html__('Updated', 'better-analytics'),
		);
	}

	protected function get_sortable_columns()
	{
		return array(
			'name'		=> array('name', false),
			'status'	=> array('status', false),
			'winner'	=> array('winner', true),
			'created'	=> array('created', true),
			'updated'	=> array('updated', true),
		);
	}

	protected function get_views()
	{
		global $totals, $status;

		$status_links = array();
		foreach ($totals as $type => $count)
		{
			if (!$count)
			{
				continue;
			}

			switch ( $type ) {
				case 'all':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('All', 'All', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;

				case 'active':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Active', 'Active', $count),
						'<span class="count">',
						'</span>',
						$count
					);
					break;

				case 'inactive':
					/* translators: %1$s = label, %2$s = <span>, %3$s = </span>, %4$u = number */
					$text = sprintf(esc_html__('%1$s %2$s(%4$u)%3$s', 'better-analytics'),
						_n('Inactive', 'Inactive', $count),
						'<span class="count">',
						'</span>',
						$count
					);
			}

			if ( 'search' != $type )
			{
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					add_query_arg('goal_status', $type, menu_page_url('better-analytics_experiments', false)),
					( $type == $status ) ? ' class="current"' : '',
					sprintf( $text, number_format_i18n( $count ) )
				);
			}
		}

		return $status_links;
	}

	protected function get_bulk_actions()
	{
		global $status;

		$actions = array();

		if ($status != 'active')
		{
			$actions['activate-selected'] = esc_html__('Activate', 'better-analytics');
		}
		if ( $status != 'inactive')
		{
			$actions['deactivate-selected'] = esc_html__('Deactivate', 'better-analytics');
		}

		return $actions;
	}

	public function single_row($item)
	{
		echo '<tr' . ($item['active'] ? ' class="active"' : '') . '>';
		$this->single_row_columns($item);
		echo '</tr>';
	}

	protected function single_row_columns($item)
	{
		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class='$column_name column-$column_name'";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			if ( 'cb' == $column_name ) {
				echo '<th scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			}
			elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				echo "</td>";
			}
			else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				echo "</td>";
			}
		}
	}

	protected function column_cb($item)
	{
		echo "<label class='screen-reader-text' for='checkbox_" . $item['id'] . "' >" . sprintf(esc_html__('Select %s', 'better-analytics'), $item['name']) . "</label>"
			. "<input type='checkbox' name='checked[]' value='" . esc_attr( $item['id'] ) . "' id='checkbox_" . $item['id'] . "' />";
	}


	protected function column_name($item)
	{
		echo '<strong><a class="row-title" href="' . add_query_arg(array('action' => 'create_edit', 'id' => $item['id']), menu_page_url('better-analytics_experiments', false)) . '">' . sanitize_text_field($item['name']) . '</a></strong>';
	}

	protected function column_status($item)
	{
		echo DigitalPointBetterAnalytics_Model_Experiments::getStatusNameByCode($item['status']);
	}

	protected function column_value($item)
	{
		echo number_format_i18n($item['value'], 2);
	}

	protected function column_created($item)
	{
		/* translators: PHP date format - see: http://php.net/manual/function.date.php */
		echo get_date_from_gmt($item['created'], esc_html__('Y/m/d g:i a', 'better-analytics'));
	}

	protected function column_updated($item)
	{
		/* translators: PHP date format - see: http://php.net/manual/function.date.php */
		echo get_date_from_gmt($item['updated'], esc_html__('Y/m/d g:i a', 'better-analytics'));
	}

}