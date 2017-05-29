<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('WPBooking_Admin_Location'))
{
	class WPBooking_Admin_Location extends WPBooking_Controller
	{
		static $_inst;
        static $_old_location_id;
		function __construct()
		{
			parent::__construct();

			add_action('init',array($this,'_register_taxonomy'));


            add_action('save_post', array($this, '_update_min_price_location_by_service'),99);
            add_action('wpbooking_save_metabox_section', array($this, '_update_min_price_location_by_service'),99);
            add_action('wpbooking_before_save_metabox_section', array($this, '_update_old_location_id'),99);
            add_action('wpbooking_after_add_availability', array($this, '_update_min_price_location_by_service'),99);

            add_action('create_term',array($this,'_update_min_price_location'),99,3);
            add_action('edit_term',array($this,'_update_min_price_location'),99,3);
		}

		function _register_taxonomy()
		{
			$labels = array(
				'name'              => _x( 'Locations', 'taxonomy general name','wpbooking' ),
				'singular_name'     => _x( 'Location', 'taxonomy singular name','wpbooking' ),
				'search_items'      => esc_html__( 'Search for Locations','wpbooking' ),
				'all_items'         => esc_html__( 'All Locations','wpbooking' ),
				'parent_item'       => esc_html__( 'Parent Location' ,'wpbooking'),
				'parent_item_colon' => esc_html__( 'Parent Location:' ,'wpbooking'),
				'edit_item'         => esc_html__( 'Edit Location' ,'wpbooking'),
				'update_item'       => esc_html__( 'Update Location' ,'wpbooking'),
				'add_new_item'      => esc_html__( 'Add New Location' ,'wpbooking'),
				'new_item_name'     => esc_html__( 'New Location Name' ,'wpbooking'),
				'menu_name'         => esc_html__( 'Location' ,'wpbooking'),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => TRUE,
				'show_admin_column' => true,
				'query_var'         => true,
                'meta_box_cb' => false,
				'rewrite'           => array( 'slug' => 'location' ),
			);
			$args=apply_filters('wpbooking_register_location_taxonomy',$args);

			register_taxonomy( 'wpbooking_location', array( 'wpbooking_service' ), $args );

			$hide=apply_filters('wpbooking_hide_locaton_select_box',TRUE);
			if($hide)
				WPBooking_Assets::add_css("#wpbooking_locationdiv{display:none!important}");
		}
        /**
         * Update min_price location when save service
         *
         * @since 1.3
         * @author quandq
         *
         * @param $post_id
         * @return bool
         */
        function _update_min_price_location_by_service($post_id){
            if (get_post_type($post_id) != 'wpbooking_service') return FALSE;
            $service_type  = get_post_meta( $post_id ,'service_type', true);
            $new_location_id = get_post_meta($post_id , 'location_id' , true);
            if(!empty($this->_old_location_id) and $new_location_id != $this->_old_location_id){
                $this->_update_min_price_location($this->_old_location_id,FALSE,'wpbooking_location');
            }
            if(!empty($new_location_id)){
                $this->_update_min_price_location($new_location_id,FALSE,'wpbooking_location');
            }
            /*if(!empty($this->_old_location_id) and $new_location_id != $this->_old_location_id){
                $list_location[]= $this->_old_location_id;
                $child = get_term_children( $this->_old_location_id, 'wpbooking_location' );
                $list_location = array_unique(array_merge($list_location,$child));
                $min_price_loaction_old = $this->_get_min_price_by_location_id($list_location,$service_type);
                update_tax_meta($this->_old_location_id,'min_price_'.$service_type,$min_price_loaction_old);
                $this->_update_parent_id_location($this->_old_location_id);
            }
            if(!empty($new_location_id)){
                $list_location[]= $new_location_id;
                $child = get_term_children( $new_location_id, 'wpbooking_location' );
                $list_location = array_unique(array_merge($list_location,$child));
                $min_price = $this->_get_min_price_by_location_id($list_location,$service_type);
                update_tax_meta($new_location_id,'min_price_'.$service_type,$min_price);
                $this->_update_parent_id_location($new_location_id);
            }*/
        }

        /**
         * Update min_price location when save location
         *
         * @since 1.3
         * @author quandq
         *
         * @param $term_id
         * @param bool $term_taxonomy_id
         * @param bool $taxonomy
         */
        function _update_min_price_location($term_id,$term_taxonomy_id=FALSE,$taxonomy=FALSE){
            if($taxonomy == 'wpbooking_location'){
                $service_types=WPBooking_Service_Controller::inst()->get_service_types();
                if(!empty($service_types)){
                    foreach($service_types as $service_type => $obj){
                        $list_location[]= $term_id;
                        $child = get_term_children( $term_id, 'wpbooking_location' );
                        $list_location = array_unique(array_merge($list_location,$child));
                        $min_price = $this->_get_min_price_by_location_id($list_location,$service_type);
                        update_tax_meta($term_id,'min_price_'.$service_type,$min_price);
                    }
                }
                $this->_update_parent_id_location($term_id);
            }
        }
        /**
         * Update min_price parent location
         *
         * @since 1.3
         * @author quandq
         *
         * @param $location_id
         */
        function _update_parent_id_location($location_id){
            $list_location[] = $location_id;
            $parent  = get_term_by( 'id', $location_id, 'wpbooking_location');
            if(!empty($parent->parent)){
                while ($parent->parent != '0'){
                    $term_id = $parent->parent;
                    $list_location[]= $term_id;
                    $child = get_term_children( $location_id, 'wpbooking_location' );
                    $list_location = array_unique(array_merge($list_location,$child));
                    $service_types=WPBooking_Service_Controller::inst()->get_service_types();
                    if(!empty($service_types)){
                        foreach($service_types as $service_type => $obj){
                            $min_price = $this->_get_min_price_by_location_id($list_location,$service_type);
                            update_tax_meta($term_id,'min_price_'.$service_type,$min_price);
                        }
                    }
                    $parent  = get_term_by( 'id', $term_id, 'wpbooking_location');
                }
            }
        }
        /**
         * Get min_price Location by Service
         *
         * @since 1.3
         * @author quandq
         *
         * @param $location_id
         * @param $service_type
         * @return int
         */
        function _get_min_price_by_location_id($location_id , $service_type){
            if(empty($location_id)) return 0 ;
            if(is_array($location_id)){
                $list_location = implode(',',$location_id);
            }else{
                $list_location = $location_id;
            }
            global $wpdb;
            $sql = "
            SELECT 
            MIN(CAST(mt1.meta_value as DECIMAL)) as min_price
            FROM 
            {$wpdb->prefix}posts 
            INNER JOIN {$wpdb->prefix}wpbooking_service ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}wpbooking_service.post_id and {$wpdb->prefix}wpbooking_service.service_type = '{$service_type}'
            INNER JOIN {$wpdb->prefix}postmeta as mt1 ON {$wpdb->prefix}posts.ID = mt1.post_id and mt1.meta_key = 'price'
            LEFT JOIN {$wpdb->prefix}term_relationships ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}term_relationships.object_id)
            WHERE
                1 = 1
            AND ({$wpdb->prefix}term_relationships.term_taxonomy_id IN ({$list_location}))";
            $result = $wpdb->get_row( $sql, ARRAY_A );
            if(!empty($result)){
                return $result['min_price'];
            }
            return 0;
        }

        /**
         * Get Location Old ID
         *
         * @since 1.3
         * @author quandq
         *
         * @param $post_id
         * @return bool
         */
        function _update_old_location_id($post_id){
            if (get_post_type($post_id) != 'wpbooking_service') return FALSE;
            $old_location_id = get_post_meta($post_id , 'location_id' , true);
            $this->_old_location_id = $old_location_id;
        }
		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}

	WPBooking_Admin_Location::inst();
}