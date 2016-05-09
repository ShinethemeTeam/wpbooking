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

			add_action('wp_ajax_traveler_calendar_bulk_edit', array( $this, 'traveler_calendar_bulk_edit') );
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
							'message' => __('The checkin or checkout field is invalid.', 'wpbooking')
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
							'message' => __('Added successful.', 'wpbooking')
						) ); 
						die;
					}else{
						echo json_encode( array(
							'status' => 0,
							'message' => __('Have an error when add new item.', 'wpbooking')
						) );
						die;
					}

				}
			}
		}

		public function traveler_calendar_bulk_edit(){
			if(  wp_verify_nonce( $_POST['security'], 'traveler-nonce-field' ) ){
				$post_id = (int) Traveler_Input::post('post_id', 0);
				$post_encrypt = Traveler_Input::post('post_encrypt', '');

				if( $post_id > 0 && traveler_encrypt_compare( $post_id, $post_encrypt ) ){

					if( isset( $_POST['all_days'] ) && !empty( $_POST['all_days'] ) ){

						$data = Traveler_Input::post('data', '');
						$all_days = Traveler_Input::post('all_days','');
						$posts_per_page = (int) Traveler_Input::post('posts_per_page','');
						$current_page = (int) Traveler_Input::post('current_page','');
						$total = (int) Traveler_Input::post('total','');

						if( $current_page > ceil( $total / $posts_per_page ) ){

							echo json_encode( array(
								'status' => 1,
								'message' => __('Added successful.', 'wpbooking')
							) );
							die;
						}else{
							$return = $this->insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id, $post_encrypt ); 
							echo json_encode( $return );
							die;
						}
					}

					$day_of_week = Traveler_Input::post('day-of-week', '');
					$day_of_month = Traveler_Input::post('day-of-month', '');

					$array_month = array(
						'January' => '1',
						'February' => '2',
						'March' => '3',
						'April' => '4',
						'May' => '5',
						'June' => '6',
						'July' => '7',
						'August' => '8',
						'September' => '9',
						'October' => '10',
						'November' => '11',
						'December' => '12',
					);

					$months = Traveler_Input::post('months', '');

					$years = Traveler_Input::post('years', '');

					$price = Traveler_Input::post('price_bulk', '');

					if( !is_numeric( $price) ){
						echo json_encode( array(
							'status' => 0,
							'message' => __('The price field is not a number.', 'wpbooking')
						) );
						die;
					}
					$price = (float) $price;
					
					$status = Traveler_Input::post('status', 'available');

					$group_day = Traveler_Input::post('group_day', '');

					$base_id = (int) traveler_origin_id( $post_id, 'traveler_service', true );

					/*	Start, End is a timestamp */
					$all_years = array();
					$all_months = array();
					$all_days = array();
					$link = '';

					if( !empty( $years ) ){

						sort( $years ,1 );

						foreach( $years as $year ){
							$all_years[] = $year;
						}

						if( !empty( $months ) ){
							
							foreach( $months as $month ){
								foreach( $all_years as $year ){
									$all_months[] = $month.' '.$year;
								}
							}

							if( !empty( $day_of_week) && !empty( $day_of_month) ){
								// Each day in month
								foreach( $day_of_month as $day ){
									// Each day in week
									foreach( $day_of_week as $day_week ){
										// Each month year
										foreach( $all_months as $month ){
											$time = strtotime( $day. ' '. $month );

											if( date('l', $time ) == $day_of_week ){
												$all_days[] = $time;
											}

										}	
									}							
								}
								foreach( $day_of_month as $day ){
									foreach( $all_months as $month ){
										$day = str_pad( $day, 2, '0', STR_PAD_LEFT );
										$all_days[] = strtotime( $day.' '.$month );
									}
								}
							}elseif( empty( $day_of_week ) && empty( $day_of_month ) ){
								foreach( $all_months as $month ){
									for( $i = strtotime('first day of '. $month ); $i <= strtotime('last day of '. $month ); $i = strtotime('+1 day', $i) ){
										$all_days[] = $i;
									}
								}
							}elseif( empty( $day_of_week ) && !empty( $day_of_month ) ){

								foreach( $day_of_month as $day ){
									foreach( $all_months as $month ){
										$month_tmp = trim( $month );
										$month_tmp = explode( ' ', $month );

										$num_day = cal_days_in_month(CAL_GREGORIAN, $array_month[ $month_tmp[0] ], $month_tmp[1] );

										if( $day <= $num_day ){
											$all_days[] = strtotime( $day.' '.$month );
										}
									}
								}
							}elseif( !empty( $day_of_week ) && empty( $day_of_month ) ){
								foreach( $day_of_week as $day ){
									foreach( $all_months as $month ){
										for( $i = strtotime('first '. $day .' of '.$month ); $i <= strtotime('last '. $day .' of '.$month ); $i = strtotime( '+1 week', $i ) ){
											$all_days[] = $i;
										}
									}
								}
							}


							if( !empty( $all_days ) ){
								$posts_per_page = 10;
								$total = count( $all_days );

								$current_page = 1;

								$data = array(
									'post_id' => $post_id,
									'base_id' => $base_id,
									'status' => $status,
									'group_day' => $group_day,
									'price' => $price,
								);

								$return = $this->insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id, $post_encrypt ); 

								echo json_encode( $return );
								die;
							}
						}else{
							echo json_encode( array(
								'status' => 0,
								'message' => __('The months field is required.', 'wpbooking')
							) );
							die;
						}
						
					}else{
						echo json_encode( array(
							'status' => 0,
							'message' => __('The years field is required.', 'wpbooking')
						) );
						die;
					}

				}
			}
		}

		public function insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days , $post_id, $post_encrypt ){
			global $wpdb;
			$table = $wpdb->prefix. 'traveler_availability';

			$start = ($current_page - 1 ) * $posts_per_page;

			$end = ($current_page -1 ) * $posts_per_page + $posts_per_page - 1;

			if( $end > $total - 1 ) $end = $total - 1;

			for( $i = $start; $i <= $end; $i ++ ){

				$data['start'] = $all_days[ $i ];
				$data['end'] = $all_days[ $i ];

				/*	Delete old item */
				$result = $this->traveler_get_availability( $data['base_id'], $all_days[ $i ], $all_days[ $i ] );

				$split = $this->traveler_split_availability( $result, $all_days[ $i ], $all_days[ $i ] );

				if( isset( $split['delete'] ) && !empty( $split['delete'] ) ){
					foreach( $split['delete'] as $item ){
						$this->traveler_delete_availability( $item['id'] );
					}
				}
				/*	.End */


				$this->traveler_insert_availability( $data['post_id'], $data['base_id'], $data['start'], $data['end'], $data['price'], $data['status'], $data['group_day']);
			}
			

			$next_page = (int) $current_page + 1;

			$progress = ($current_page / $total ) * 100;

			$return = array(
				'all_days' => $all_days,
				'current_page' => $next_page,
				'posts_per_page' => $posts_per_page,
				'total' => $total,
				'status' => 2,
				'data' => $data,
				'progress' => $progress,
				'post_id' => $post_id,
				'post_encrypt' => $post_encrypt
			);

			return $return;
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