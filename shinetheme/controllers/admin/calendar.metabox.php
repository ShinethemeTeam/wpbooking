<?php 
/**
*@since 1.0.0
**/
if( !class_exists('Traveler_Calendar_Metabox') ){
	class Traveler_Calendar_Metabox extends Traveler_Controller{
		public function __construct(){
			parent::__construct();

			add_action('wp_ajax_traveler_load_availability', array( $this, 'traveler_load_availability') );
			add_action('wp_ajax_traveler_add_availability', array( $this, 'traveler_add_availability') );
		}

		public function traveler_load_availability(){
			if(  wp_verify_nonce( $_POST['security'], 'traveler-nonce-field' ) ){
				$post_id = (int) Traveler_Input::post('post_id','');
				$post_encrypt = (int) Traveler_Input::post('post_encrypt','');

				if( $post_id > 0 || traveler_encrypt_compare( $post_id, $post_encrypt ) ){

					$base_id = (int) traveler_origin_id( $post_id, 'traveler_service', true );

					$check_in = (int) Traveler_Input::post('start', '');
					$check_out = (int) Traveler_Input::post('end', '');

					
					$return = $this->traveler_get_availability( $base_id, $check_in, $check_out );

					echo json_encode( $return );
					die;

				}
			}
		}

		public function traveler_add_availability(){
			if(  wp_verify_nonce( $_POST['security'], 'traveler-nonce-field' ) ){
				$post_id = (int) Traveler_Input::post('post-id', 0);
				$post_encrypt = (int) Traveler_Input::post('post-encrypt', '');

				if( $post_id > 0 || traveler_encrypt_compare( $post_id, $post_encrypt ) ){

					$check_in = strtotime( Traveler_Input::post('check_in','') ) ;
					$check_out = strtotime( Traveler_Input::post('check_out','') );
					if( !$check_in || !$check_out ){
						echo json_encode( array(
							'status' => 0,
							'message' => __('The checkin or checkout field is invalid.', 'traveler-booking')
						) );
						die;
					}

					$price = (float) Traveler_Input::post('price', '');

					$status = Traveler_Input::post('status', '');

					$group_day = Traveler_Input::post('group_day','');

					/* Get origin post id if use WPML */
					$base_id = (int) traveler_origin_id( $post_id, 'traveler_service', true );

					/* Get all item between check in - out */

					$result = $this->traveler_get_availability( $base_id, $check_in, $check_out );

					$split = $this->traveler_split_availability( $result, $check_in, $check_out );

					if( isset( $split['delete'] ) && !empty( $split['delete'] ) ){
						foreach( $split['delete'] as $item ){
							$this->traveler_delete_availability( $item['id'] );
						}
					}

					if( isset( $split['insert'] ) && !empty( $split['insert'] ) ){
						foreach( $split['insert'] as $item ){
							$this->traveler_insert_availability( $item['post_id'], $item['base_id'], $item['start'], $item['end'], $item['price'], $item['status'], $item['group_day']);
						}
					}
					$new_item = $this->traveler_insert_availability( $post_id, $base_id, $check_in, $check_out, $price, $status, $group_day );

					if( $new_item > 0 ){
						echo json_encode( array(
							'status' => 1,
							'message' => __('Added successful.', 'traveler-booking')
						) ); 
						die;
					}else{
						echo json_encode( array(
							'status' => 0,
							'message' => __('Have an error when add new item.', 'traveler-booking')
						) );
						die;
					}

				}
			}
		}

		public function traveler_delete_availability( $id = '' ){

			global $wpdb;

			$table = $wpdb->prefix. 'traveler_availability';

			$wpdb->delete(
				$table,
				array(
					'id' => $id
				)
			);

		}

		public function traveler_insert_availability( $post_id = '', $base_id = '', $check_in = '', $check_out = '', $price = '', $status = '', $group_day = '' ){
			global $wpdb;

			$table = $wpdb->prefix. 'traveler_availability';
			if( $group_day == 'group' ){
				$wpdb->insert(
					$table,
					array(
						'post_id'   => $post_id,
						'base_id'   => $base_id,
						'start'     => $check_in,
						'end'       => $check_out,
						'price'     => $price,
						'status'    => $status,
						'group_day' => $group_day
					)
				);
			}else{
				for( $i = $check_in; $i <= $check_out; $i = strtotime('+1 day', $i) ){
					$wpdb->insert(
						$table,
						array(
							'post_id'   => $post_id,
							'base_id'   => $base_id,
							'start'     => $i,
							'end'       => $i,
							'price'     => $price,
							'status'    => $status,
							'group_day' => $group_day
						)
					);
				}
			}
			

			return (int) $wpdb->insert_id;
		}

		public function traveler_get_availability( $base_id = '', $check_in = '', $check_out = ''){
			global $wpdb;

			$table = $wpdb->prefix. 'traveler_availability';

			$sql = "SELECT * FROM {$table} WHERE base_id = {$base_id} AND ( ( CAST( `start` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED) AND CAST( `start` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) OR ( CAST( `end` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED ) AND ( CAST( `end` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) ) )";

			$result = $wpdb->get_results( $sql, ARRAY_A );

			$return = array();

			if( !empty( $result ) ){
				foreach( $result as $item ){
					$return[] = array(
						'id' => $item['id'],
						'post_id' => $item['post_id'],
						'base_id' => $item['base_id'],
						'start' => date( 'Y-m-d', $item['start'] ),
						'end' => date('Y-m-d', strtotime( '+1 day', $item['end'] ) ),
						'price' => (float) $item['price'],
						'status' => $item['status'],
						'group_day' => $item['group_day'],
					);
				}
			}

			return $return;
		}

		public function traveler_split_availability( $result = array(), $check_in = '', $check_out = ''){
			$return = array();	

			if( !empty( $result ) ){
				foreach( $result as $item ){
					$check_in = (int) $check_in;
					$check_out = (int) $check_out;

					$start = strtotime( $item['start'] );
					$end = strtotime( '-1 day', strtotime( $item['end'] ) );

					if( $start < $check_in && $end >= $check_in ){
						$return['insert'][] = array(
							'post_id' => $item['post_id'],
							'base_id' => $item['base_id'],
							'start' => strtotime( $item['start'] ),
							'end' => strtotime( '-1 day', $check_in ),
							'price' => (float) $item['price'],
							'status' => $item['status'],
							'group_day' => $item['group_day'],
						);
					}

					if( $start <= $check_out && $end > $check_out ){
						$return['insert'][] = array(
							'post_id' => $item['post_id'],
							'base_id' => $item['base_id'],
							'start' => strtotime( '+1 day', $check_out ),
							'end' => strtotime( '-1 day', strtotime( $item['end'] ) ),
							'price' => (float) $item['price'],
							'status' => $item['status'],
							'group_day' => $item['group_day'],
						);
					}

					$return['delete'][] = array(
						'id' => $item['id']
					);
				}
			}

			return $return;
		}

	}
	new Traveler_Calendar_Metabox();
}