<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 10:23 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Traveler_Model') ){
	class Traveler_Model
	{
		/**
		 * Name of the Table
		 * @var bool
		 * @since 1.0
		 */
		protected $table_name=FALSE;


		/**
		 * Table version to upgrade
		 * @var bool
		 * @since 1.0
		 */
		protected $table_version='1.0';


		/**
		 * All Columns
		 * @var array
		 * @since 1.0
		 */
		protected $columns=array();


		/**
		 * Check if table meta is created
		 * @var bool
		 * @since 1.0
		 */
		protected $is_ready=FALSE;



		/**
		 * Identity Column Name for search
		 * @var string
		 * @since 1.0
		 */
		protected $table_key='id';



		/**
		 * If dont want to create meta table, just set it to TRUE
		 * @var bool
		 * @since 1.0
		 */
		protected $ignore_create_table=FALSE;


		protected $_where_query=array();
		protected $_select_query=array();
		protected $_order_query=array();
		protected $_limit_query=array();
		protected $_last_query=array();
		protected $_last_result=array();

		/**
		 * @since 1.0
		 */
		function __construct(){
			global $wpdb;
			if($this->ignore_create_table==FALSE and $this->table_name){

				add_action('after_setup_theme', array($this, '_check_meta_table_is_working'));
			}
		}
		function last_query()
		{
			return $this->_last_query;
		}

		function where($key,$value){
			if(is_array($key) and !empty($key)){
				$this->_where_query=array($this->_where_query,$key);
			}
			if(is_string($key)){
				$this->_where_query[$key]=$value;
			}

			return $this;
		}
		function orderby($key,$value){
			if(is_array($key) and !empty($key)){
				$this->_order_query=array($this->_order_query,$key);
			}
			if(is_string($key)){
				$this->_order_query[$key]=$value;
			}
			return $this;
		}
		function limit($key,$value=0){
			$this->_limit_query[0]=$key;
			$this->_limit_query[1]=$value;

			return $this;
		}
		function get()
		{
			global $wpdb;
			$query=$this->_get_query();
			$this->_last_query=$query;
			$this->_last_result=$wpdb->get_results();

			return $this;
		}

		function row()
		{
			return isset($this->_last_result[0])?$this->_last_result[0]:FALSE;
		}
		function result()
		{
			return $this->_last_result;
		}

		function update($data=array())
		{
			if(empty($data))
			{
				return FALSE;
			}
			global $wpdb;
			$table_name = $wpdb->prefix . $this->table_name;

			$where=FALSE;
			if(!empty($this->_where_query)){
				$where='WHERE 1=1 ';

				foreach($this->_where_query as $key=>$value){
					$where.=$wpdb->prepare(' AND %s=%s',array($key,$value));
				}
			}

			$set=FALSE;
			foreach($data as $key=>$value){
				$set.="$key='$value',";
			}
			$set=substr($set,0,-1);

			$query="UPDATE ".$table_name." SET %s";

			$query=$wpdb->prepare($query,array($set));
			$query.=$where;

			return $wpdb->query($query);

		}

		function insert($data=array())
		{
			if(empty($data))
			{
				return FALSE;
			}
			global $wpdb;
			$table_name = $wpdb->prefix . $this->table_name;

			$set=FALSE;
			$set_data=array();
			foreach($data as $key=>$value){
				$set.="%s,";
				$set_data[]=$value;
			}
			$set_columns=FALSE;
			foreach($data as $key=>$value){
				$set_columns.=$key.",";
			}
			$set=substr($set,0,-1);
			$set_columns=substr($set_columns,0,-1);

			$query="INSERT INTO ".$table_name." ({$set_columns}) VALUES ($set)";

			$query=$wpdb->prepare($query,$set_data);

			$wpdb->query($query);

			return $wpdb->insert_id;

		}

		/**
		 * Get single row by table key
		 * @param $id
		 * @return array|bool|null|object|void
		 */
		function find($id){

			if(!$this->table_key or !$this->is_ready()) return FALSE;

			return $this->where($this->table_key,$id)->limit(1)->get()->row();
		}

		/**
		 * Get single row by key and value
		 * @param $key
		 * @param $id
		 * @return array|bool|null|object|void
		 */
		function find_by($key,$id){

			if(!$this->is_ready()) return FALSE;
			return $this->where($key,$id)->limit(1)->get()->row();
		}

		function find_all_by($key,$value)
		{
			if(!$this->table_key or !$this->is_ready()) return FALSE;

			return $this->where($key,$value)->get()->result();
		}

		/**
		 * Get columns of the table
		 * @return array
		 */
		function get_columns(){
			return apply_filters('traveler_model_table_'.$this->table_name.'_columns',$this->columns);
		}

		/**
		 * Check Meta Table is ready
		 * @since 1.0
		 * @return bool
		 */
		function is_ready(){

			if($this->ignore_create_table) return true;

			return $this->is_ready;
		}

		/**
		 * @since 1.0
		 */
		function _check_meta_table_is_working(){
			global $wpdb;

			$table_name = $wpdb->prefix . $this->table_name;
			$table_columns = $this->get_columns();

			if (!$this->is_ready() and $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

				//table is not created. you may create the table here.
				global $wpdb;
				$charset_collate = $wpdb->get_charset_collate();

				// Column String
				$col_string = '';
				if (!empty($table_columns)) {
					$i = 0;
					foreach ($table_columns as $key => $value) {
						$s_char = ',';
						if ($i == count($table_columns) - 1) {
							$s_char = '';
						}
						// Unique key
						$unique_key = '';

						// Check is AUTO_INCREMENT col
						if (isset($value['AUTO_INCREMENT']) and $value['AUTO_INCREMENT']) {
							$unique_key = $key;
							$col_string .= ' ' . sprintf('%s %s NOT NULL AUTO_INCREMENT PRIMARY KEY', $key, $value['type']) . $s_char;
						} else {
							$prefix = '';
							//Add length for varchar data type
							switch (strtolower($value['type'])) {
								case "varchar":
									if (isset($value['length']) and $value['length']) {
										$prefix = '(' . $value['length'] . ')';
									}
									break;
							}
							$col_string .= ' ' . $key . ' ' . $value['type'] . $prefix . $s_char;
						}

						$i++;

					}


				}

				$sql = "CREATE TABLE $table_name (
                        $col_string
                    ) $charset_collate;";

				$wpdb->query($sql);

				update_option($this->table_name.'_version', $this->table_version);

				if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

					$this->_is_ready = FALSE;

				} else {
					$this->_is_ready = TRUE;

				}

			} else {
				$this->_is_ready = TRUE;
			}

			if ($this->is_ready) {
				// check upgrade data
				if ($db_version = get_option($this->table_name.'_version')) {
					if (version_compare($db_version, $this->table_version, '<')) {
						$this->_upgrade_table();
						update_option($this->table_name.'_version', $this->table_version);
					}
				}

			}
		}

		/**
		 * Upgrade meta table
		 *
		 * @since 1.0
		 */
		protected  function _upgrade_table()
		{
			global $wpdb;
			$table_name = $wpdb->prefix . $this->table_name;
			$table_columns = $this->get_columns();

			$insert_key = $table_columns;
			$update_key = array();

			//Old table columns
			$query = "SELECT *
                    FROM information_schema.COLUMNS
                    WHERE
                        TABLE_SCHEMA = %s
                    AND TABLE_NAME = %s";
			$old_coumns = $wpdb->get_results(
				$wpdb->prepare($query, array(
					$wpdb->dbname,
					$table_name

				))
			);

			if ($old_coumns and !empty($old_coumns)) {
				foreach ($old_coumns as $key => $value) {
					unset($insert_key[ $value->COLUMN_NAME ]);

					// for columns need update
					if (isset($table_columns[ $value->COLUMN_NAME ])) {
						if (strtolower($table_columns[ $value->COLUMN_NAME ]['type']) != strtolower($value->DATA_TYPE)) {
							$update_key[ $value->COLUMN_NAME ] = $table_columns[ $value->COLUMN_NAME ];
						}
					}
				}
			}


			// Do create new columns
			if (!empty($insert_key)) {
				$insert_col_string = '';
				foreach ($insert_key as $key => $value) {
					$prefix = '';
					//Add length for varchar data type
					switch (strtolower($value['type'])) {
						case "varchar":
							if (isset($value['length']) and $value['length']) {
								$prefix = '(' . $value['length'] . ')';
							}
							break;
					}

					$col_type = $value['type'];
					$insert_col_string .= " ADD $key $col_type" . $prefix . ',';
				}
				$insert_col_string = substr($insert_col_string, 0, -1);
				// do update query
				$query = "ALTER TABLE $table_name " . $insert_col_string;

				$wpdb->query($query);
			}

			// Do update columns (change columns data type)
			if (!empty($update_key)) {
				$update_col_string = '';
				foreach ($update_key as $key => $value) {
					$prefix = '';
					//Add length for varchar data type
					switch (strtolower($value['type'])) {
						case "varchar":
							if (isset($value['length']) and $value['length']) {
								$prefix = '(' . $value['length'] . ')';
							}
							break;
					}

					$col_type = $value['type'];
					$update_col_string .= " MODIFY $key $col_type" . $prefix . ',';
				}
				$update_col_string = substr($update_col_string, 0, -1);
				// do update query
				$query = "ALTER TABLE $table_name " . $update_col_string;

				$wpdb->query($query);
			}
		}


		protected function _get_query()
		{
			global $wpdb;
			$table_name = $wpdb->prefix . $this->table_name;

			$select=FALSE;
			if(!empty($this->_select_query)){

				$select=implode(',',$this->_select_query);
			}else{
				$select='*';
			}

			$where=FALSE;
			if(!empty($this->_where_query)){
				$where='WHERE 1=1 ';

				foreach($this->_where_query as $key=>$value){
					$where.=$wpdb->prepare(' AND %s=%s',array($key,$value));
				}
			}
			$order=FALSE;
			if(!empty($this->_order_query)){
				$order=' ORDER BY ';
				$order.=implode(',',$this->_order_query);
			}

			$limit=FALSE;
			if(!empty($this->_limit_query[0])){
				$limit=' LIMIT ';

				$offset=!empty($this->_limit_query[1])?$this->_limit_query[1]:0;

				$limit.=$offset.','.$this->_limit_query[0];

			}

			if($select){
				$query="SELECT  %s FROM %s";
				$query=$wpdb->prepare($query,array($select,$table_name));

				$query.=$where;
				$query.=$order;
				$query.=$limit;

				return $query;
			}
			return FALSE;
		}


		protected function _clear_query()
		{
			$this->_where_query=array();
			$this->_select_query=array();
			$this->_order_query=array();
			$this->_limit_query=array();
			$this->_last_query=array();
		}
	}

}