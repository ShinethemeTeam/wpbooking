<?php
if(!class_exists('traveler_widget_form_search')){
    class traveler_widget_form_search extends WP_Widget{
        public function __construct() {
            $widget_ops = array('classname' => '', 'description' => "" );
            parent::__construct('traveler_widget_form_search', __('Traveler Search Form',"traveler-booking"), $widget_ops);
        }
        /**
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance) {
            extract(wp_parse_args($instance , array('title'=>'','service_type'=>'','field_search'=>"")));
            $title = apply_filters( 'widget_title', empty( $title ) ? '' : $title, $instance, $this->id_base );

            $page_search = "";
            switch($service_type){
                case "room":
                    $id_page = traveler_get_option('service_type_room_archive_page');
                    $page_search = get_permalink($id_page);
            }

            ?>
            <form class="traveler-search-form" action="<?php echo esc_url( $page_search ) ?>"
                  xmlns="http://www.w3.org/1999/html">
                <aside class="widget widget_calendar" id="calendar-2">
                    <div class="calendar_wrap" id="calendar_wrap">
                        <h3><?php echo esc_html( $title ) ?></h3>
                        <?php
                        if(!empty($field_search[$service_type])){
                            foreach($field_search[$service_type] as $k=>$v){
                                $required = "";
                                if($v['required'] == "yes"){
                                    $required = 'required';
                                }
                                $value = Traveler_Input::request($v['field_type'],'');
                                switch($v['field_type']){
                                    case "location_id":
                                        ?>
                                        <div class="item-search">
                                            <label for="<?php echo esc_html($v['field_type']) ?>"><?php echo esc_html($v['title']) ?></label>
                                            <?php
                                            $args = array(
                                                'show_option_none' => __( '-- Select --' , ST_TEXTDOMAIN ),
                                                'option_none_value' => "",
                                                'hierarchical'      => 1 ,
                                                'name'              => $v['field_type'] ,
                                                'class'             => '' ,
                                                'id'             => $v['field_type'] ,
                                                'taxonomy'          => 'traveler_location' ,
                                                'hide_empty' => 0,
                                            );
                                            $is_taxonomy = Traveler_Input::request($v['field_type']);
                                            if(!empty($is_taxonomy)){
                                                $args['selected'] =$is_taxonomy;
                                            }
                                            wp_dropdown_categories( $args );
                                            ?>
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
                                }
                            }
                        } ?>

                        <div class="item-search">
                            <button class="" type="submit"><?php _e("Search",'traveler-booking') ?></button
                        </div>
                    </div>
                </aside>

            </form>
            <?php
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
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('Title:',"traveler-booking"); ?></strong> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
            <p>
                <label for="<?php echo $this->get_field_id('service_type'); ?>"><strong><?php _e('Service Type:'); ?></strong>
                    <?php
                    $data = Traveler_Service::inst()->get_service_types();
                    ?>
                    <select name="<?php echo $this->get_field_name('service_type'); ?>" class="option_service_search_form" id="<?php echo $this->get_field_id('service_type'); ?>">
                        <option value=""><?php _e("-- Select --",'traveler-booking') ?></option>
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
            <?php $all_list_field= Traveler_Service::_get_list_field_search();
            if(!empty($all_list_field)) {
                foreach( $all_list_field as $key => $value ) {
                    ?>
                    <div class="list_item_widget  div_content_<?php echo esc_attr($key) ?> <?php if($key != $service_type) echo "hide"; ?>">
                        <label><strong><?php _e("Search Fields:","traveler-booking") ?></strong></label>
                        <div class="list-group content_list_search_form_widget">

                            <?php
                            $list = $field_search[$key];
                            $number = 0 ;
                            if(!empty($list)){
                                foreach($list as $k=>$v){
                                    ?>
                                    <div class="list-group-item">

                                        <div class="control">
                                            <a class="btn_edit_field_search_form"><?php _e("Edit","traveler-booking") ?></a> |
                                            <a class="btn_remove_field_search_form"><?php _e("Remove","traveler-booking") ?></a>
                                        </div>
                                        <div class="control-hide hide">
                                            <table class="form-table traveler-settings">
                                                <?php
                                                $hteml_title_form = "";
                                                foreach($value as $k1=>$v1){?>
                                                    <?php
                                                    $data_value = $v[$v1['name']];
                                                    if($v1['name'] == 'title'){
                                                        $hteml_title_form = $data_value;
                                                    }
                                                    if($v1['type'] == 'text'){
                                                        ?>
                                                        <tr>
                                                            <th> <?php echo esc_html($v1['label']) ?>:  </th>
                                                            <td> <input type="text" name="<?php echo $this->get_field_name('field_search'); ?>[<?php echo esc_attr($key) ?>][<?php echo esc_attr($number) ?>][<?php echo esc_attr($v1['name']) ?>]" class="form-control <?php echo esc_attr($v1['name']) ?>" value="<?php echo esc_html($data_value) ?>"> </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    if($v1['type'] == 'dropdown'){
                                                        $options = $v1['options'];
                                                        ?>
                                                        <tr>
                                                            <th> <?php echo esc_html($v1['label']) ?>:  </th>
                                                            <td>
                                                                <select class="form-control" name="<?php echo $this->get_field_name('field_search'); ?>[<?php echo esc_attr($key) ?>][<?php echo esc_attr($number) ?>][<?php echo esc_attr($v1['name']) ?>]" >
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
                                <input type="button" value="Add Field" data-number="<?php echo esc_attr($number) ?>" data-name-field-search="<?php echo $this->get_field_name('field_search'); ?>" data-post-type="<?php echo esc_attr($key) ?>" class="button button-primary left btn_add_field_search_form" id="#">
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
                                <a class="btn_edit_field_search_form"><?php _e("Edit","traveler-booking") ?></a> |
                                <a class="btn_remove_field_search_form"><?php _e("Remove","traveler-booking") ?></a>
                            </div>
                            <div class="control-hide">
                                <table class="form-table traveler-settings">
                                    <?php foreach($value as $k=>$v){?>
                                        <?php
                                        if($v['type'] == 'text'){
                                            ?>
                                            <tr>
                                                <th> <?php echo esc_html($v['label']) ?>:  </th>
                                                <td> <input type="text" placeholder=""  name="__name_field_search__[<?php echo esc_attr($key) ?>][__number__][<?php echo esc_attr($v['name']) ?>]" class="form-control <?php echo esc_attr($v['name']) ?>"> </td>
                                            </tr>
                                        <?php
                                        }
                                        if($v['type'] == 'dropdown'){
                                            $options = $v['options'];
                                            ?>
                                            <tr>
                                                <th> <?php echo esc_html($v['label']) ?>:  </th>
                                                <td>
                                                    <select class="form-control" name="__name_field_search__[<?php echo esc_attr($key) ?>][__number__][<?php echo esc_attr($v['name']) ?>]" >
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
    function traveler_widget_form_search() {
        register_widget( 'traveler_widget_form_search' );
    }
    add_action( 'widgets_init', 'traveler_widget_form_search' );
}
