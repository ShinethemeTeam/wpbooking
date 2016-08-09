<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/5/2016
 * Time: 9:39 AM
 */
if(!class_exists('WPBooking_Query_Inject')){
	class WPBooking_Query_Inject{

		static $_inst;


		protected $_where_query = array();
		protected $_join_query = array();
		protected $_select_query = array();
		protected $_order_query = array();
		protected $_limit_query = array();
		protected $_last_query = array();
		protected $_last_result = array();
		protected $_groupby = array();
		protected $_having = array();
		protected $_like_query = array();

		protected $_query_args=array();

		function inject()
		{
			add_filter('posts_join',array($this,'posts_join'));
			add_filter('posts_where',array($this,'posts_where'));
			add_filter('posts_fields',array($this,'posts_fields'));
			add_filter('posts_groupby',array($this,'posts_groupby'));

			add_filter('wpbooking_wb_query_arg',array($this,'apply_query_args'));

		}
		/**
		 * Add Where Clause to current Query
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $key
		 * @param bool|FALSE $value
		 * @param $raw_where bool
		 * @return $this
		 */
		function where($key, $value = FALSE, $raw_where = FALSE)
		{
			if (is_array($key) and !empty($key)) {
				foreach ($key as $k1 => $v1) {
					$this->where($k1, $v1, $raw_where);
				}

				return $this;
			}
			if (is_string($key)) {
				$this->_where_query[] = array(
					'key'    => $key,
					'value'  => $value,
					'clause' => 'and',
					'is_raw' => $raw_where
				);
			}

			return $this;
		}
		/**
		 * Add OR Where Clause to current Query
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $key
		 * @param bool|FALSE $value
		 * @param bool
		 * @return $this
		 */
		function or_where($key, $value = FALSE, $raw_where = FALSE)
		{
			if (is_array($key) and !empty($key)) {
				foreach ($key as $k1 => $v1) {
					$this->or_where($k1, $v1, $raw_where);
				}

				return $this;
			}
			if (is_string($key)) {
				$this->_where_query[] = array(
					'key'    => $key,
					'value'  => $value,
					'clause' => 'or',
					'is_raw' => $raw_where
				);
			}

			return $this;
		}

		/**
		 * Add Select
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $column_name string|array Name of Column or Array of Column
		 * @return Object Current Object
		 *
		 */
		function select($column_name)
		{
			if (is_array($column_name) and !empty($column_name)) {
				foreach ($column_name as $v) $this->select($v);
			}

			if (is_string($column_name)) {
				$this->_select_query[] = $column_name;
			}

			return $this;
		}

		/**
		 * Add Table Join
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $table string Name of table
		 * @param $on_clause string on clause
		 * @param $join_key string join keyword, default is INNER
		 * @return Object Current Object
		 */
		function join($table, $on_clause, $join_key = 'INNER')
		{
			if (is_array($table) and !empty($table)) {
				foreach ($table as $v) {
					$v = wp_parse_args($v, array(
						'table'   => '',
						'on'      => '',
						'keyword' => '',
					));
					$this->join($v['table'], $v['on'], $v['keyword']);
				}
			}

			if (is_string($table)) {
				$this->_join_query[] = array('table' => $table, 'on' => $on_clause, 'keyword' => $join_key);
			}

			return $this;
		}

		/**
		 * Add Order by Clause
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $key
		 * @param string $value
		 * @return $this
		 */
		function orderby($key, $value = 'asc')
		{
			if (is_array($key) and !empty($key)) {
				foreach ($key as $k1 => $v1) {
					$this->orderby($k1, $v1);
				}

				return $this;
			}
			if (is_string($key)) {
				$this->_order_query[$key] = $value;
			}

			return $this;
		}

		/**
		 * Add Group By Clause
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $key
		 * @return $this
		 */
		function groupby($key)
		{
			if (is_array($key) and !empty($key)) {
				foreach ($key as $v1) {
					$this->groupby($v1);
				}
			}
			if (is_string($key)) {
				$this->_groupby[] = $key;
			}

			return $this;
		}

		/**
		 * Add Having Clause
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $key
		 * @param $clause
		 * @return $this
		 */
		function having($key,$clause='and')
		{
			if (is_array($key) and !empty($key)) {
				foreach ($key as $v1) {
					$this->having($v1['key'],$v1['clause']);
				}
			}
			if (is_string($key)) {
				$this->_having[]=array(
					'key'=>$key,
					'clause'=>$clause
				);
			}

			return $this;
		}

		/**
		 * Add Query Arg to Current Query
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $key
		 * @param bool|FALSE $value
		 * @return $this
		 */
		function add_arg($key,$value=FALSE){
			if (is_array($key) and !empty($key)) {
				foreach ($key as $v1) {
					$this->add_arg($v1);
				}
			}
			if (is_string($key)) {
				$this->_query_args[$key] = $value;
			}

			return $this;
		}

		/**
		 * Get Query Arg By Key
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $key
		 * @return bool
		 */
		function get_arg($key){
			return isset($this->_query_args[$key])?$this->_query_args[$key]:FALSE;
		}

		/**
		 * Add Join Clause to the Query
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $join_default
		 * @return mixed
		 */
		function posts_join($join_default){

			global $wpdb;

			$join = FALSE;
			if (!empty($this->_join_query)) {
				foreach ($this->_join_query as $j) {
					$j = wp_parse_args($j, array(
						'table'    => FALSE,
						'on'      => FALSE,
						'keyword' => FALSE
					));

					if (!$j['table'] or !$j['on']) continue;

					$table = $wpdb->prefix . $j['table'];

					$join .= ' ' . $j['keyword'] . ' JOIN ' . $table . ' ON ' . $j['on'];
				}
			}

			if($join)
			$join_default.=$join;

			return $join_default;
		}


		/**
		 * Add Where Clause to the Query
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $where_default
		 * @return string
		 */
		function posts_where($where_default){
			global $wpdb;
			$where = FALSE;
			if (!empty($this->_where_query)) {
				foreach ($this->_where_query as $key => $value) {
					$value = wp_parse_args($value, array(
						'key'    => FALSE,
						'value'  => FALSE,
						'clause' => 'and',
						'is_raw' => FALSE
					));
					if (!$value['is_raw']) {
						$last = substr($value['key'], -1);
						switch ($last) {
							case ">":
							case "<":
							case "=":
								$where .= $wpdb->prepare(' ' . $value['clause'] . ' ' . $value['key'] . '%s ', array($value['value']));
								break;
							default:
								$where .= $wpdb->prepare(' ' . $value['clause'] . ' ' . $value['key'] . '=%s ', array($value['value']));
								break;

						}
					} else {
						$where .= ' ' . $value['clause'] . ' ' . $value['key'];
					}

				}
			}
			$where_default.=' '.$where;

			return $where_default;
		}

		/**
		 * Apply Group By & Having to Query
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $group_by_default
		 * @return string
		 */
		function posts_groupby($group_by_default)
		{
			$groupby = FALSE;

			if (!empty($this->_groupby)) {
				foreach ($this->_groupby as $k => $v) {
					$groupby .= ' ' . $v . ',';
				}

				$groupby = substr($groupby, 0, -1);

				$having = FALSE;
				if (!empty($this->_having)) {
					$having .= ' HAVING 1=1';
					foreach ($this->_having as $k => $v) {
						$having.=' '.$v['clause'].' '.$v['key'].' ';
					}

					$having = substr($having, 0, -1);

					$groupby .= ' ' . $having;
				}

			}
			if($groupby)
			$group_by_default=$groupby;

			return $group_by_default;
		}

		/**
		 * Add Select Clause to the Query
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $select
		 * @return mixed
		 */
		function posts_fields($select)
		{
			if (!empty($this->_select_query)) {
				$select_add = implode(',', $this->_select_query);

				$select.=','.$select_add;
			}
			return $select;
		}

		/**
		 * Apply Filter For Query Args
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $args
		 * @return array
		 */
		function apply_query_args($args)
		{
			if(empty($this->_query_args)) return $args; return array_merge($args,$this->_query_args);
		}

		/**
		 * Clear all hook of query
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function clear()
		{
			$this->_where_query = array();
			$this->_select_query = array();
			$this->_order_query = array();
			$this->_limit_query = array();
			$this->_join_query = array();
			$this->_groupby = array();
			$this->_having = array();

			remove_filter('posts_join',array($this,'posts_join'));
			remove_filter('posts_where',array($this,'posts_where'));
			remove_filter('posts_groupby',array($this,'posts_groupby'));
			remove_filter('posts_fields',array($this,'posts_fields'));
			remove_filter('wpbooking_wb_query_arg',array($this,'apply_query_args'));
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}
}