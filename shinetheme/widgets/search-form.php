<?php
if(!class_exists('WPBooking_Widget_Form_Search')){
    class WPBooking_Widget_Form_Search extends WP_Widget{
        public function __construct() {
            $widget_ops = array('classname' => '', 'description' => "[WPBooking] Search Form" );
            parent::__construct(__CLASS__, __('WPBooking Search Form',"wpbooking"), $widget_ops);
        }
        /**
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance) {

			$widget_args=wp_parse_args($args,array(
				'before_widget'=>'',
				'after_widget'=>'',
				'before_title'=>'',
				'after_title'=>'',
			));
            extract($instance=wp_parse_args($instance , array('title'=>'','service_type'=>'','field_search'=>"",'before_widget'=>FALSE,'after_widget'=>FALSE)));
			$service_type=$instance['service_type'];
            $title = apply_filters( 'widget_title', empty( $title ) ? '' : $title, $instance, $this->id_base );

            $page_search = "";
            switch($service_type){
                case "room":
                    $id_page = wpbooking_get_option('service_type_room_archive_page');
                    $page_search = get_permalink($id_page);
            }
			echo $widget_args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $widget_args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $widget_args['after_title'];
			}

			$search_more_fields=array();
            ?>
            <form class="wpbooking-search-form" action="<?php echo esc_url( $page_search ) ?>" xmlns="http://www.w3.org/1999/html">
				<?php if(!get_option('permalink_structure')){
					printf("<input type='hidden' name='page_id' value='%d'>",$id_page);
				} ?>
				<div class="wpbooking-search-form-wrap" >
					<?php
					if(!empty($field_search[$service_type])){
						foreach($field_search[$service_type] as $k=>$v){
							$v=wp_parse_args($v,array(
								'in_more_filter'=>''
							));
							if($v['in_more_filter']){
								$search_more_fields[$k]=$v;
								continue;
							}
							$this->get_field_html($v,$service_type);

						}
					} ?>

					<?php if(!empty($search_more_fields)){
						?>
						<div class="wpbooking-search-form-more-wrap mb20">
							<a href="#" onclick="return false" class="btn btn-link wpbooking-show-more-fields"><?php esc_html_e('More Filters','wpbooking') ?></a>
							<div class="wpbooking-search-form-more">
								<?php
									foreach($search_more_fields as $k=>$v){
										$this->get_field_html($v,$service_type);
									}?>
								<a href="#" onclick="return false" class="btn btn-link wpbooking-hide-more-fields"><?php esc_html_e('Cancel','wpbooking') ?></a>
							</div>
						</div>
						<?php
					} ?>

					<div class="item-search search-button-wrap">
						<button class="" type="submit"><?php _e("Search",'wpbooking') ?></button>
					</div>
				</div>
            </form>
            <?php

			echo $widget_args['after_widget'];
        }

		function get_field_html($v,$service_type)
		{
			$required = "";
			if($v['required'] == "yes"){
				$required = 'required';
			}
			$value = WPBooking_Input::request($v['field_type'],'');
			switch($v['field_type']){
				case "location_id":
				case "location_suggestion":
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<?php
						$class=FALSE;
						if($v['field_type']=='location_suggestion'){
							$class='bravo-select2';
						}
						$args = array(
							'show_option_none' => __( '-- Select --' , "wpbooking"  ),
							'option_none_value' => "",
							'hierarchical'      => 1 ,
							'name'              => 'location_id' ,
							'class'             => $class ,
							'id'             => $v['field_type'] ,
							'taxonomy'          => 'wpbooking_location' ,
							'hide_empty' => 0,
						);
						$is_taxonomy = WPBooking_Input::request($v['field_type']);
						if(!empty($is_taxonomy)){
							$args['selected'] =$is_taxonomy;
						}
						wp_dropdown_categories( $args );
						?>
					</div>
					<?php
					break;
				case "taxonomy":
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<?php
						if($v['taxonomy_show'] =='dropdown'){
							$args = array(
								'show_option_none' => __( '-- Select --' , "wpbooking" ),
								'option_none_value' => "",
								'hierarchical'      => 1 ,
								'name'              => $v['field_type'].'['.$v['taxonomy'].']' ,
								'class'             => '' ,
								'id'             => $v['field_type'].'['.$v['taxonomy'].']' ,
								'taxonomy'          => $v['taxonomy'] ,
								'hide_empty' => 0,
							);
							$is_taxonomy = WPBooking_Input::request($v['field_type']);
							if(!empty($is_taxonomy[$v['taxonomy']])){
								$args['selected'] = $is_taxonomy[$v['taxonomy']];
							}
							wp_dropdown_categories( $args );
							?>
							<input type="hidden" value="<?php echo esc_attr($v['taxonomy_operator']) ?>" name="<?php echo esc_attr( "taxonomy_operator" . '[' . $v[ 'taxonomy' ] . ']' ) ?>" />
							<?php
						}else{ ?>
							<div class="row">
								<?php
								if(empty($v['taxonomy'])) continue;
								$tax=get_taxonomy($v['taxonomy']);

								if(!$tax) continue;

								$terms = get_terms(  $v['taxonomy'] , array('hide_empty' => false,) );

								if(!empty($value[$v['taxonomy']])) $value_item=$value[$v['taxonomy']];else $value_item=FALSE;

								if(!empty( $terms )) {
									foreach( $terms as $key2 => $value2 ) {
										$check ="";
										if(in_array($value2->term_id,explode(',',$value_item))){
											$check = "checked";
										}
										$class=FALSE;
										if($key2>=4){
											$class='hidden_term';
										}
										?>
										<div class="col-md-12 term-item <?php echo esc_attr($class)?>">
											<input type="checkbox" <?php echo esc_html($check) ?> class="item_taxonomy" id="<?php echo "item_".$value2->term_id ?>" value="<?php echo esc_html( $value2->term_id ) ?>">
											<label for="<?php echo "item_".$value2->term_id ?>"><?php echo esc_html( $value2->name ) ?></label>
										</div>
										<?php
										if($key2==3 and count($terms)>4){
										?>
											<div class="col-md-12">
												<label class="show-more-terms" ><b><?php printf(esc_html__('More %s ...','wpbooking'),$tax->label) ?></b></label>
											</div>
											<?php
										}
									}
								}
								?>
								<input type="hidden" value="<?php echo esc_attr($value_item) ?>" class="data_taxonomy" name="<?php echo esc_attr( $v[ 'field_type' ] . '[' . $v[ 'taxonomy' ] . ']' ) ?>" />
								<input type="hidden" value="<?php echo esc_attr($v['taxonomy_operator']) ?>" name="<?php echo esc_attr( "taxonomy_operator" . '[' . $v[ 'taxonomy' ] . ']' ) ?>" />
							</div>
						<?php } ?>
					</div>
					<?php
					break;
				case "review_rate":
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<div class="row">
							<?php
							$data = array(
								"1" => __( "1 Star" , 'wpbooking' ) ,
								"2" => __( "2 Star" , 'wpbooking' ) ,
								"3" => __( "3 Star" , 'wpbooking' ) ,
								"4" => __( "4 Star" , 'wpbooking' ) ,
								"5" => __( "5 Star" , 'wpbooking' )
							);
							if(!empty( $data )) {
								foreach( $data as $key2 => $value2 ) {
									$check ="";
									if(in_array($key2,explode(',',$value))){
										$check = "checked";
									}
									?>
									<div class="col-md-12">
										<input type="checkbox" <?php echo esc_html($check) ?> class="item_taxonomy" id="<?php echo "item_".$key2 ?>" value="<?php echo esc_html( $key2 ) ?>">
										<label for="<?php echo "item_".$key2 ?>"><?php echo esc_html( $value2 ) ?></label>
									</div>
									<?php
								}
							}
							?>
							<input type="hidden" value="<?php echo esc_attr($value) ?>" class="data_taxonomy" name="<?php echo esc_attr( $v['field_type'] ) ?>">
						</div>
					</div>
					<?php
					break;
				//
				case "check_in":
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<input class="wpbooking-date-start" type="text" <?php echo esc_html($required) ?> id="<?php echo esc_html($v['field_type']) ?>" name="<?php echo esc_html($v['field_type']) ?>" placeholder="<?php echo esc_html($v['placeholder']) ?>" value="<?php echo esc_html($value) ?>">
					</div>
					<?php
					break;
				case "check_out":
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<input class="wpbooking-date-end" type="text" <?php echo esc_html($required) ?> id="<?php echo esc_html($v['field_type']) ?>" name="<?php echo esc_html($v['field_type']) ?>" placeholder="<?php echo esc_html($v['placeholder']) ?>" value="<?php echo esc_html($value) ?>">
					</div>
					<?php
					break;
				case "customer_confirm":
				case "partner_confirm":?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<label><input <?php checked(WPBooking_Input::get($v['field_type']),1) ?>  type="checkbox" <?php echo esc_html($required) ?> id="<?php echo esc_html($v['field_type']) ?>" name="<?php echo esc_html($v['field_type']) ?>" placeholder="<?php echo esc_html($v['placeholder']) ?>" value="1"></label>
					</div>
					<?php
					break;
				case "bathroom":
				case "bed":
				case "bedroom":
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<select id="<?php echo esc_html($v['field_type']) ?>" name="<?php echo esc_html($v['field_type']) ?>">
							<?php for($i=0;$i<=20;$i++){
								printf('<option value="%s" %s >%s</option>',$i,selected(WPBooking_Input::get($v['field_type']),$i,FALSE),$i);
							} ?>
						</select>
					</div>
					<?php
					break;
				case "price":
					wp_enqueue_script('ion-range-slider');
					wp_enqueue_style('ion-range-slider');
					wp_enqueue_style('ion-range-slider-html5');


					$min_max_price=WPBooking_Service_Model::inst()->get_min_max_price(array('service_type'=>$service_type));
					$price_chart=WPBooking_Service_Model::inst()->get_price_chart(array('service_type'=>$service_type));
					$min_max_price=wp_parse_args($min_max_price,array(
						'min'=>FALSE,
						'max'=>FALSE
					));
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<div class="wpbooking-price-chart" data-chart='<?php echo json_encode($price_chart)?>'></div>
						<input type="text" data-type="double" data-min="<?php echo esc_attr($min_max_price['min']) ?>" data-max="<?php echo esc_attr($min_max_price['max']) ?>" class="wpbooking-ionrangeslider" <?php echo esc_html($required) ?> id="<?php echo esc_html($v['field_type']) ?>" name="<?php echo esc_html($v['field_type']) ?>" placeholder="<?php echo esc_html($v['placeholder']) ?>" value="<?php echo esc_html($value) ?>">
					</div>
					<?php
					break;
				default:
					?>
					<div class="item-search">
						<label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
						<input type="text" <?php echo esc_html($required) ?> id="<?php echo esc_html($v['field_type']) ?>" name="<?php echo esc_html($v['field_type']) ?>" placeholder="<?php echo esc_html($v['placeholder']) ?>" value="<?php echo esc_html($value) ?>">
					</div>
					<?php
					break;

			}
		}



        /**
         * @param array $new_instance
         * @param array $old_instance
         * @return array
         */
        public function update( $new_instance, $old_instance ) {
            if(empty($new_instance['field_search'])) $new_instance['field_search'] = "";
            else{
                $post_type = $new_instance['service_type'];
                $data = $new_instance['field_search'][$post_type];
                $new_instance['field_search']  = array();
                $new_instance['field_search'][$post_type] = $data;
            }
            return wp_parse_args($new_instance,$old_instance);
        }
        public function form( $instance ) {
            $instance = wp_parse_args((array) $instance, array( 'title' => '','service_type'=> '','field_search'=>""));
            extract($instance);
            ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('Title:',"wpbooking"); ?></strong> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
            <p>
                <label for="<?php echo $this->get_field_id('service_type'); ?>"><strong><?php _e('Service Type:'); ?></strong>
                    <?php
                    $data = WPBooking_Service::inst()->get_service_types();
                    ?>
                    <select name="<?php echo $this->get_field_name('service_type'); ?>" class="option_service_search_form" id="<?php echo $this->get_field_id('service_type'); ?>">
                        <option value=""><?php _e("-- Select --",'wpbooking') ?></option>
                        <?php
                        if(!empty($data)){
                            foreach($data as $k=>$v){
                                $select = "";
                                if($service_type == $k ){
                                    $select = "selected";
                                }
                                echo '<option '.$select.' value="'.$k.'">'.$v['label'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </label>
            </p>
            <?php $all_list_field= WPBooking_Service::inst()->_get_list_field_search();
            if(!empty($all_list_field)) {
                foreach( $all_list_field as $key => $value ) {
                    ?>
                    <div class="list_item_widget  div_content_<?php echo esc_attr($key) ?> <?php if($key != $service_type) echo "hide"; ?>">
                        <label><strong><?php _e("Search Fields:","wpbooking") ?></strong></label>
                        <div class="list-group content_list_search_form_widget">

                            <?php
                            $number = 0 ;
                            if(!empty($field_search[$key])){
                                $list = $field_search[$key];
                                foreach($list as $k=>$v){
                                    ?>
                                    <div class="list-group-item">

                                        <div class="control">
                                            <a class="btn_edit_field_search_form"><?php _e("Edit","wpbooking") ?></a> |
                                            <a class="btn_remove_field_search_form"><?php _e("Remove","wpbooking") ?></a>
                                        </div>
                                        <div class="control-hide hide">
                                            <table class="form-table wpbooking-settings">
                                                <?php
                                                $hteml_title_form = "";
                                                foreach($value as $k1=>$v1){
                                                    $default = array( 'name' => '' , 'label' => '' , 'type' => '' , 'options' => '' , 'class' => '', 'value' => '' );
                                                    $v1 = wp_parse_args( $v1 , $default );

													if(!empty($v[$v1['name']]))
                                                    $data_value = $v[$v1['name']];
													else $data_value=FALSE;

                                                    if($v1['name'] == 'title'){
                                                        $hteml_title_form = $data_value;
                                                    }
                                                    if($v1['type'] == 'text'){
                                                        ?>
                                                        <tr class="<?php echo esc_attr($v1['class']) ?> div_<?php echo esc_attr($v1['name']) ?>">
                                                            <th> <?php echo esc_html($v1['label']) ?>:  </th>
                                                            <td> <input type="text"  name="<?php echo $this->get_field_name('field_search'); ?>[<?php echo esc_attr($key) ?>][<?php echo esc_attr($number) ?>][<?php echo esc_attr($v1['name']) ?>]" class="form-control <?php echo esc_attr($v1['name']) ?>" value="<?php echo esc_html($data_value) ?>"> </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    if($v1['type'] == 'checkbox'){
                                                        ?>
                                                        <tr class="<?php echo esc_attr($v1['class']) ?> div_<?php echo esc_attr($v1['name']) ?>">
                                                            <th> <?php echo esc_html($v1['label']) ?>:  </th>
                                                            <td> <label><input type="checkbox" <?php checked(1,$data_value) ?> value="1"  name="<?php echo $this->get_field_name('field_search'); ?>[<?php echo esc_attr($key) ?>][<?php echo esc_attr($number) ?>][<?php echo esc_attr($v1['name']) ?>]" class=" <?php echo esc_attr($v1['name']) ?>"> <?php esc_html_e('Yes','wpbooking')?></label> </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    if($v1['type'] == 'dropdown'){
                                                        $options = $v1['options'];
                                                        ?>
                                                        <tr class="<?php echo esc_attr($v1['class']) ?> div_<?php echo esc_attr($v1['name']) ?>">
                                                            <th> <?php echo esc_html($v1['label']) ?>:  </th>
                                                            <td>
                                                                <select class="form-control <?php echo esc_attr($v1['name']) ?>" name="<?php echo $this->get_field_name('field_search'); ?>[<?php echo esc_attr($key) ?>][<?php echo esc_attr($number) ?>][<?php echo esc_attr($v1['name']) ?>]" >
                                                                    <?php
                                                                    if(!empty($options)){
                                                                        foreach($options as $k2=>$v2){
                                                                            $select = "";
                                                                            if($data_value == $k2 ){
                                                                                $select = "selected";
                                                                            }
                                                                            echo "<option {$select} value={$k2}>{$v2}</option>";
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } ?>
                                            </table>
                                        </div>
                                        <div class="head-title"><?php echo esc_html($hteml_title_form) ?></div>
                                    </div>
                                    <?php
                                    $number++;
                                }
                            }
                            ?>
                        </div>
                        <div class="widget-control-actions">
                            <div class="alignleft">
                                <input type="button" value="<?php  esc_html_e('Add Field','wpbooking')?>" data-number="<?php echo esc_attr($number) ?>" data-name-field-search="<?php echo $this->get_field_name('field_search'); ?>" data-post-type="<?php echo esc_attr($key) ?>" class="button button-primary left btn_add_field_search_form" id="#">
                            </div>
                            <br class="clear">
                        </div>
                    </div>
                <?php
                }
            }
            ?>

            <?php
            if(!empty($all_list_field)) {
                foreach( $all_list_field as $key => $value ) {
                    ?>
                    <div class="div_content_hide_<?php echo esc_attr($key) ?> hide">
                        <div class="list-group-item">
                            <div class="control">
                                <a class="btn_edit_field_search_form"><?php _e("Edit","wpbooking") ?></a> |
                                <a class="btn_remove_field_search_form"><?php _e("Remove","wpbooking") ?></a>
                            </div>
                            <div class="control-hide">
                                <table class="form-table wpbooking-settings">
                                    <?php foreach($value as $k=>$v){?>
                                        <?php
                                        $default = array( 'name' => '' , 'label' => '' , 'type' => '' , 'options' => '' , 'class' => '', 'value' => '' );
                                        $v = wp_parse_args( $v , $default );
                                        if($v['type'] == 'text'){
                                            ?>
                                            <tr class="<?php echo esc_attr($v['class']) ?> div_<?php echo esc_attr($v['name']) ?>">
                                                <th> <?php echo esc_html($v['label']) ?>:  </th>
                                                <td> <input type="text" placeholder=""  name="__name_field_search__[<?php echo esc_attr($key) ?>][__number__][<?php echo esc_attr($v['name']) ?>]" class="form-control <?php echo esc_attr($v['name']) ?>"> </td>
                                            </tr>
                                        <?php
                                        }
                                        if($v['type'] == 'dropdown'){
                                            $options = $v['options'];
                                            ?>
                                            <tr class="<?php echo esc_attr($v['class']) ?> div_<?php echo esc_attr($v['name']) ?>">
                                                <th> <?php echo esc_html($v['label']) ?>:  </th>
                                                <td>
                                                    <select class="form-control <?php echo esc_attr($v['name']) ?>" name="__name_field_search__[<?php echo esc_attr($key) ?>][__number__][<?php echo esc_attr($v['name']) ?>]" >
                                                        <?php
                                                        if(!empty($options)){
                                                            foreach($options as $k1=>$v1){
                                                                echo "<option value={$k1}>{$v1}</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    <?php } ?>
                                </table>
                            </div>
                            <div class="head-title"></div>
                        </div>
                    </div>
                <?php
                }
            }
            ?>
        <?php
        }
    }
    function wpbooking_widget_form_search() {
        register_widget( 'WPBooking_Widget_Form_Search' );
    }
    add_action( 'widgets_init', 'wpbooking_widget_form_search' );
}
