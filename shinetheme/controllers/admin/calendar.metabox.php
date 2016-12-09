<?php 
/**
*@since 1.0.0
**/
if( !class_exists('WPBooking_Calendar_Metabox') ){
	class WPBooking_Calendar_Metabox extends WPBooking_Controller{
		static $_inst;

		public function __construct(){
			parent::__construct();	

			add_action('wp_ajax_wpbooking_load_availability', array( $this, '_load_availability') );

			add_action('wp_ajax_wpbooking_add_availability', array( $this, '_add_availability') );

			add_action('wp_ajax_wpbooking_calendar_bulk_edit', array( $this, '_calendar_bulk_edit') );

			// Ajax Save Property For
			add_action('wp_ajax_wpbooking_save_property_available_for',array($this,'_save_property_available_for'));
		}

		/**
		 * @since 1.0
		 * @author haint
		 *
		 *
		 */
		public function _load_availability(){
			$post_id = (int) WPBooking_Input::post('post_id','');

			// Validate Permission
			$post=get_post($post_id);
			if(!$post or ($post->post_author!=get_current_user_id() and !current_user_can('manage_options'))){
				return FALSE;
			}

			if(  wp_verify_nonce( $_POST['security'], 'wpbooking-nonce-field' ) ){

				$post_encrypt = (int) WPBooking_Input::post('post_encrypt','');

				if( $post_id > 0 || wpbooking_encrypt_compare( $post_id, $post_encrypt ) ){

					$base_id = (int) wpbooking_origin_id( $post_id, 'wpbooking_service', true );

					$check_in = WPBooking_Input::post('start', '');
					$check_out = WPBooking_Input::post('end', '');
					$check_in=strtotime($check_in);
					$check_out=strtotime($check_out);

					if($check_in<strtotime('today')){$check_in=strtotime('today');}

					$return = $this->get_availability( $base_id, $check_in, $check_out );


					// Other day, in case Specific Periods Available

					$all_days=array();

					$begin = new DateTime( ); $begin->setTimestamp($check_in);
					$end = new DateTime( );$end->setTimestamp($check_out);

					$interval = DateInterval::createFromDateString('1 day');
					$period = new DatePeriod($begin, $interval, $end);

					foreach ( $period as $dt )
					{
						$all_days[$dt->format('Y-m-d')]=array(
							'start'=>$dt->format('Y-m-d'),
							'end'=>$dt->format('Y-m-d'),
							//'rendering'=>'background',
							//'backgroundColor'=>'#dce0e0'
							'status'=>'available',
							'can_check_in'=>1,
							'can_check_out'=>1,

						);
						if(get_post_meta($post_id,'property_available_for',true)=='specific_periods'){
							$all_days[$dt->format('Y-m-d')]['status']='wb-disable';
						}else{
							$all_days[$dt->format('Y-m-d')]['price_text']=WPBooking_Currency::format_money(get_post_meta($post_id,'base_price',true));
						}

					}

					// Foreach Data
					if(!empty($return)){
						foreach($return as $day){
							if(array_key_exists($day['start'],$all_days)){
								unset($all_days[$day['start']]);
							}
						}
					}

					// Now append the exsits
					if(!empty($all_days)){
						foreach($all_days as $day){
							$return[]=$day;
						}
					}


					$data=array(
						'data'=>$return
					);

					echo json_encode( $data );
					die;

				}
			}
		}

		public function _add_availability(){

			$post_id = (int) WPBooking_Input::post('post-id',0);

			// Validate Permission
			$post=get_post($post_id);
			if(!$post or ($post->post_author!=get_current_user_id() and !current_user_can('manage_options')) ){
				echo json_encode(array(
					'status'=>0,
					'message'=>esc_html__('You do not have permission to do it','wpbooking')
				));
				die;
			}

			if(  wp_verify_nonce( $_POST['security'], 'wpbooking-nonce-field' ) ){
				$post_encrypt = (int) WPBooking_Input::post('post-encrypt', '');

				if( $post_id > 0 || wpbooking_encrypt_compare( $post_id, $post_encrypt ) ){

					$check_in = strtotime( WPBooking_Input::post('check_in','') ) ;
					$check_out = strtotime( WPBooking_Input::post('check_out','') );
					if( !$check_in || !$check_out ){
						echo json_encode( array(
							'status' => 0,
							'message' => __('The checkin or checkout field is invalid.', 'wpbooking')
						) );
						die;
					}

					$price = (float) WPBooking_Input::post('price', '');

					$status = WPBooking_Input::post('status', '');

					$group_day = WPBooking_Input::post('group_day','');
					$can_check_in=WPBooking_Input::post('can_check_in');
					$can_check_out=WPBooking_Input::post('can_check_out');
					$weekly=WPBooking_Input::post('weekly');
					$monthly=WPBooking_Input::post('monthly');

					$calendar_minimum=WPBooking_Input::post('calendar_minimum');
					$calendar_maximum=WPBooking_Input::post('calendar_maximum');
					$calendar_price=WPBooking_Input::post('calendar_price');
					$calendar_adult_minimum=WPBooking_Input::post('calendar_adult_minimum');
					$calendar_adult_price=WPBooking_Input::post('calendar_adult_price');
					$calendar_child_minimum=WPBooking_Input::post('calendar_child_minimum');
					$calendar_child_price=WPBooking_Input::post('calendar_child_price');
					$calendar_infant_minimum=WPBooking_Input::post('calendar_infant_minimum');
					$calendar_infant_price=WPBooking_Input::post('calendar_infant_price');

					/* Get origin post id if use WPML */
					$base_id = (int) wpbooking_origin_id( $post_id, 'wpbooking_service');

					/* Get all item between check in - out */

					$result = $this->get_availability( $base_id, $check_in, $check_out );

					$split = $this->split_availability( $result, $check_in, $check_out );

					if( isset( $split['delete'] ) && !empty( $split['delete'] ) ){
						foreach( $split['delete'] as $item ){
							$this->wpbooking_delete_availability( $item['id'] );
						}
					}

					if( isset( $split['insert'] ) && !empty( $split['insert'] ) ){
						foreach( $split['insert'] as $item ){
							$this->wpbooking_insert_availability( $item['post_id'], $item['base_id'], $item['start'], $item['end'], $item['price'], $item['status'], $item['group_day'],$weekly,$monthly,$can_check_in,$can_check_out,$item['calendar_minimum'],$item['calendar_maximum'],$item['calendar_price'],$item['adult_minimum'],$item['adult_price'],$item['child_minimum'],$item['child_price'],$item['infant_minimum'],$item['infant_price'] );
						}
					}
					$new_item = $this->wpbooking_insert_availability( $post_id, $base_id, $check_in, $check_out, $price, $status, $group_day,$weekly,$monthly,$can_check_in,$can_check_out,$calendar_minimum,$calendar_maximum,$calendar_price,$calendar_adult_minimum,$calendar_adult_price,$calendar_child_minimum,$calendar_child_price,$calendar_infant_minimum,$calendar_infant_price );

					if( $new_item > 0 ){
						echo json_encode( array(
							'status' => 1,
							'message' => __('Added successfully.', 'wpbooking')
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

		public function _calendar_bulk_edit(){

			$post_id = (int) WPBooking_Input::post('post_id',0);

			// Validate Permission
			$post=get_post($post_id);
			if(!$post or $post->post_author!=get_current_user_id() or !current_user_can('manage_options')){
				echo json_encode(array(
					'status'=>0,
					'message'=>esc_html__('You do not have permission to do it','wpbooking')
				));
				die;
			}

			if(  wp_verify_nonce( $_POST['security'], 'wpbooking-nonce-field' ) ){
				$post_id = (int) WPBooking_Input::post('post_id', 0);
				$post_encrypt = WPBooking_Input::post('post_encrypt', '');

				if( $post_id > 0 && wpbooking_encrypt_compare( $post_id, $post_encrypt ) ){

					if( isset( $_POST['all_days'] ) && !empty( $_POST['all_days'] ) ){

						$data = WPBooking_Input::post('data', '');
						$all_days = WPBooking_Input::post('all_days','');
						$posts_per_page = (int) WPBooking_Input::post('posts_per_page','');
						$current_page = (int) WPBooking_Input::post('current_page','');
						$total = (int) WPBooking_Input::post('total','');

						if( $current_page > ceil( $total / $posts_per_page ) ){

							echo json_encode( array(
								'status' => 1,
								'message' => __('Added successfully.', 'wpbooking')
							) );
							die;
						}else{
							$return = $this->insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id, $post_encrypt ); 
							echo json_encode( $return );
							die;
						}
					}

					$day_of_week = WPBooking_Input::post('day-of-week', '');
					$day_of_month = WPBooking_Input::post('day-of-month', '');

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

					$months = WPBooking_Input::post('months', '');

					$years = WPBooking_Input::post('years', '');

					$price = WPBooking_Input::post('price_bulk', '');

					if( !is_numeric( $price) ){
						echo json_encode( array(
							'status' => 0,
							'message' => __('The price field is not a number.', 'wpbooking')
						) );
						die;
					}
					$price = (float) $price;
					
					$status = WPBooking_Input::post('status', 'available');

					$group_day = WPBooking_Input::post('group_day', '');

					$base_id = (int) wpbooking_origin_id( $post_id, 'wpbooking_service', true );

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
			$table = $wpdb->prefix. 'wpbooking_availability';

			$start = ($current_page - 1 ) * $posts_per_page;

			$end = ($current_page -1 ) * $posts_per_page + $posts_per_page - 1;

			if( $end > $total - 1 ) $end = $total - 1;

			for( $i = $start; $i <= $end; $i ++ ){

				$data['start'] = $all_days[ $i ];
				$data['end'] = $all_days[ $i ];

				/*	Delete old item */
				$result = $this->get_availability( $data['base_id'], $all_days[ $i ], $all_days[ $i ] );

				$split = $this->split_availability( $result, $all_days[ $i ], $all_days[ $i ] );

				if( isset( $split['delete'] ) && !empty( $split['delete'] ) ){
					foreach( $split['delete'] as $item ){
						$this->wpbooking_delete_availability( $item['id'] );
					}
				}
				/*	.End */


				$this->wpbooking_insert_availability( $data['post_id'], $data['base_id'], $data['start'], $data['end'], $data['price'], $data['status'], $data['group_day']);
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

		public function wpbooking_delete_availability( $id = '' ){

			global $wpdb;

			$table = $wpdb->prefix. 'wpbooking_availability';

			$wpdb->delete(
				$table,
				array(
					'id' => $id
				)
			);

		}

		public function wpbooking_insert_availability( $post_id = '', $base_id = '', $check_in = '', $check_out = '', $price = '', $status = '', $group_day = '',$weekly=FALSE,$monthly=FALSE,$can_check_in=1,$can_check_out=1,$calendar_minimum = '',$calendar_maximum = '',$calendar_price = '',$adult_minimum = '',$adult_price='',$child_minimum='',$child_price='',$infant_minimum='',$infant_price=''){
			global $wpdb;

			$table = $wpdb->prefix. 'wpbooking_availability';
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
						'group_day' => $group_day,
						'monthly'=>$monthly,
						'weekly'=>$weekly,
						'can_check_in'=>$can_check_in,
						'can_check_out'=>$can_check_out,
						'calendar_minimum'=>$calendar_minimum,
                        'calendar_maximum' => $calendar_maximum,
                        'calendar_price' => $calendar_price,
                        'adult_minimum' => $adult_minimum,
                        'adult_price' => $adult_price,
                        'child_minimum' => $child_minimum,
                        'child_price' => $child_price,
                        'infant_minimum' => $infant_minimum,
                        'infant_price' => $infant_price,
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
							'group_day' => $group_day,
							'monthly'=>$monthly,
							'weekly'=>$weekly,
							'can_check_in'=>$can_check_in,
							'can_check_out'=>$can_check_out,
                            'calendar_minimum'=>$calendar_minimum,
                            'calendar_maximum' => $calendar_maximum,
                            'calendar_price' => $calendar_price,
                            'adult_minimum' => $adult_minimum,
                            'adult_price' => $adult_price,
                            'child_minimum' => $child_minimum,
                            'child_price' => $child_price,
                            'infant_minimum' => $infant_minimum,
                            'infant_price' => $infant_price,
						)
					);
				}
			}

			return (int) $wpdb->insert_id;
		}

		public function get_availability( $base_id = '', $check_in = '', $check_out = ''){
			global $wpdb;

			$table = $wpdb->prefix. 'wpbooking_availability';

            $where=apply_filters('wpbooking_get_availability_where_clause',false,$base_id);

			$sql = "SELECT * FROM {$table} WHERE base_id = {$base_id} AND ( ( CAST( `start` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED) AND CAST( `start` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) OR ( CAST( `end` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED ) AND ( CAST( `end` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) ) ) ".$where;

			$result = $wpdb->get_results( $sql, ARRAY_A );

            $service_type = get_post_meta( $base_id ,'service_type', true);

			$return = array();

			if( !empty( $result ) ){
				foreach( $result as $item ){
					$item_array= array(
						'id' => $item['id'],
						'post_id' => $item['post_id'],
						'base_id' => $item['base_id'],
						'start' => date( 'Y-m-d', $item['start'] ),
						'end' => date('Y-m-d', strtotime( '+1 day', $item['end'] ) ),
						'price' => (float) $item['price'],
						'price_text' => ($service_type == 'tour')?WPBooking_Currency::format_money($item['calendar_price']):WPBooking_Currency::format_money($item['price']),
						'weekly' => (float) $item['weekly'],
						'monthly' => (float) $item['monthly'],
						'status' => $item['status'],
						'group_day' => $item['group_day'],
						'can_check_in' => $item['can_check_in'],
						'can_check_out' => $item['can_check_out'],
                        'calendar_minimum'=>$item['calendar_minimum'],
                        'calendar_maximum' => $item['calendar_maximum'],
                        'calendar_price' => $item['calendar_price'],
                        'adult_minimum' => $item['adult_minimum'],
                        'adult_price' => $item['adult_price'],
                        'child_minimum' => $item['child_minimum'],
                        'child_price' => $item['child_price'],
                        'infant_minimum' => $item['infant_minimum'],
                        'infant_price' => $item['infant_price'],
                        'pricing_type' => get_post_meta($item['post_id'],'pricing_type', true),
					);
//					if($item['status']=='not_available'){
//						$item_array['rendering']='background';
//						$item_array['backgroundColor']='#dce0e0';
//					}

					$return[] =$item_array;
				}
			}

			return $return;
		}

		public function split_availability( $result = array(), $check_in = '', $check_out = ''){
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
							'price_init' => (float) $item['price_init'],
							'price_person' => (float) $item['price_person'],
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
                            'price_init' => (float) $item['price_init'],
                            'price_person' => (float) $item['price_person'],
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

		/**
		 * Ajax Handler for save property available for
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function _save_property_available_for()
		{
			$post_id=WPBooking_Input::post('post_id');
			if(!$post_id) return FALSE;

			$property_available_for=WPBooking_Input::post('property_available_for');
			update_post_meta($post_id,'property_available_for',$property_available_for);
			WPBooking_Service_Model::inst()->where('post_id',$post_id)->update(array(
				'property_available_for'=>$property_available_for
			));

			echo json_encode(array('status'=>1));
			die;
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}

	WPBooking_Calendar_Metabox::inst();
}