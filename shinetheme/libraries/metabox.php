<?php
/**
 * @since 1.0.0
 * Add metabox
 **/
if (!class_exists('WPBooking_Metabox')) {
    class WPBooking_Metabox
    {

        static $_inst;

        private $metabox;

        public function __construct()
        {
            add_action('admin_enqueue_scripts', array($this, '_add_scripts'));


            add_action('wpbooking_save_metabox_section', array($this, 'wpbooking_save_list_item'), 20, 3);
            add_action('wpbooking_save_metabox_section', array($this, 'wpbooking_save_gmap'), 20, 3);
            add_action('wpbooking_save_metabox_section', array($this, 'wpbooking_save_location'), 20, 3);
            add_action('wpbooking_save_metabox_section', array($this, 'wpbooking_save_taxonomies'), 20, 3);

            add_action('admin_footer',array($this,'_add_js_template'));

            /**
             * Ajax Handler Save Metabox Section
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_ajax_wpbooking_save_metabox_section',array($this,'_save_metabox_section'));

            /**
             * Ajax Delete Permanently Attachment
             *
             * @since 1.0
             *
             */
            add_action('wp_ajax_wpbooking_delete_attachment',array($this,'_delete_p_attachment'));

        }

        /**
         * Ajax Handler Save Metabox Section
         *
         * @since 1.0
         * @author dungdt
         */
        function _save_metabox_section()
        {

            $res=array('status'=>0);

            $section=WPBooking_Input::post('wb_meta_section');
            if($section){
                check_ajax_referer("wpbooking_meta_section_".$section,'wb_security');
                $service_type=WPBooking_Input::post('wb_service_type');
                $service_type_object=WPBooking_Service_Controller::inst()->get_service_type($service_type);

                $post_id=WPBooking_Input::post('wb_post_id');
                $post_type=get_post_type($post_id);

                if($service_type and is_object($service_type_object)){

                    /* check permissions */
                    $permission=true;
                    if ('page' == $post_type) {
                        if (!current_user_can('edit_page', $post_id))
                            $permission=false;
                    } else {
                        if (!current_user_can('edit_post', $post_id))
                            $permission=false;
                    }


                    if(!$permission){
                        $res['message']=esc_html__('You don not have permission to do that','wpbooking');
                    }else{

                        // Change Service Type
                        update_post_meta($post_id,'service_type',$service_type);

                        $metabox=$service_type_object->get_metabox();

                        if(isset($metabox[$section])){
                            $this->do_save_metabox($post_id,$metabox[$section]['fields'],$section);
                        }

                        $res['status']=1;
                    }

                }else{
                    $res['message']=esc_html__('Please specific Service Type','wpbooking');
                }
            }

            echo json_encode($res);
            wp_die();

        }

        /**
         * Delete Permanently Attachment
         *
         * @author tienhd
         * @version 1.0
         */
        function _delete_p_attachment(){
            $res = array('status' => 0);
            $img_id = WPBooking_Input::post('img_id');
            if(!empty($img_id)){
                if(wp_delete_attachment($img_id,true) == false){
                    $res['status'] = 0;
                }else{
                    $res['status'] = 1;
                }
            }
            echo json_encode($res);
            wp_die();
        }

        function _do_save_section($post_data){

        }

        function _add_js_template()
        {
            $post_id=get_the_ID();
            $service_types=WPBooking_Service_Controller::inst()->get_service_types();
            if(!empty(($service_types))){
                foreach ($service_types as $type_id=>$type){
                    $sections=$type->get_metabox();
                    if(empty($sections)) continue;
                    ?>
                    <script type="text/html" id="tmpl-wpbooking-metabox-<?php echo esc_html($type_id) ?>">
                        <div class="wpbooking-tabs">
                        <ul class="st-metabox-nav">
                            <?php
                            foreach ((array)$sections as $key => $field):
                                $class = '';
                                $data_class = '';
                                if (!empty($field['condition'])) {
                                    $class .= ' wpbooking-condition ';
                                    $data_class .= ' data-condition=' . $field['condition'] . ' ';
                                }
                                ?>
                                <li class=""><a
                                        class="<?php echo esc_attr($class) ?>" <?php echo esc_attr($data_class) ?>
                                        href="#<?php echo 'st-metabox-tab-item-' . esc_html($key); ?>"><?php echo($field['label']); ?></a>
                                </li>
                            <?php  endforeach; ?>
                        </ul>

                        <?php
                        foreach ($sections as $key => $section):

                                $class = '';
                                $data_class = '';
                                if (!empty($section['condition'])) {
                                    $class .= ' wpbooking-condition ';
                                    $data_class .= ' data-condition=' . $section['condition'] . ' ';
                                }
                                ?>
                                <div id="<?php echo 'st-metabox-tab-item-' . esc_html($key); ?>" class="st-metabox-tabs-content ">
                                    <div class="st-metabox-tab-content-wrap <?php echo esc_attr($class) ?> row" <?php echo esc_attr($data_class) ?> >
                                        <input type="hidden" name="wb_meta_section" value="<?php echo esc_attr($key) ?>">
                                        <input type="hidden" name="wb_security" value="<?php echo wp_create_nonce( "wpbooking_meta_section_".$key ) ?>">
                                        <input type="hidden" name="wb_service_type" value="<?php echo esc_attr($type_id) ?>">
                                        <input type="hidden" name="wb_post_id" value="<?php echo esc_attr($post_id) ?>">

                                        <?php

                                        $fields=$section['fields'];

                                        foreach ((array)$fields as $field_id=> $field):

                                            if (empty($field['type'])) continue;

                                                $default = array(
                                                    'id'          => '',
                                                    'label'       => '',
                                                    'type'        => '',
                                                    'desc'        => '',
                                                    'std'         => '',
                                                    'class'       => '',
                                                    'location'    => FALSE,
                                                    'map_lat'     => '',
                                                    'map_long'    => '',
                                                    'map_zoom'    => 13,
                                                    'server_type' => '',
                                                    'width'       => ''
                                                );

                                                $field = wp_parse_args($field, $default);

                                                $class_extra = FALSE;
                                                if ($field['location'] == 'hndle-tag') {
                                                    $class_extra = 'wpbooking-hndle-tag-input';
                                                }
                                                $file = 'metabox-fields/' . $field['type'];
                                                //var_dump($file);

                                                $field_html = apply_filters('wpbooking_metabox_field_html_' . $field['type'], FALSE, $field, $post_id);
                                                if ($field_html) echo $field_html;
                                                else
                                                    echo wpbooking_admin_load_view($file, array('data' => $field, 'class_extra' => $class_extra, 'post_id' => $post_id));

                                                ?>
                                            <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php
                            endforeach; ?>
                        </div>

                    </script>
                    <?php
                }
            }
        }

        function generate_metabox_section($section){

        }

        public function _add_scripts()
        {
            wp_enqueue_media();
            global $wp_styles, $wp_scripts;

            $scripts = $wp_scripts->queue;

            if (!in_array('gmap3.js', $scripts)) {

                wp_enqueue_script('google-map-js','//maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key=AIzaSyA1l5FlclOzqDpkx5jSH5WBcC0XFkqmYOY',array('jquery'),null,true);

                wp_enqueue_script('gmap3.js ', wpbooking_admin_assets_url('js/gmap3.min.js'), array('jquery'), null, TRUE);
            }
        }

        /**
         * Get Registered Metabox
         *
         * @author dungdt
         * @since 1.0
         *
         */
        public function get_metabox()
        {
            return $this->metabox;
        }

        public function register_meta_box($metabox = array())
        {

            $this->metabox = $this->_pre_handle_metabox($metabox);

            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        }

        /**
         * Loop and Hook to allow 3rd plugin add metabox
         *
         * @param $metabox
         * @return mixed
         *
         * @author dungdt
         * @since 1.0
         */
        private function _pre_handle_metabox($metabox)
        {
            if (!empty($metabox['fields']) and !empty($metabox['id'])) {
                $fields = array();
                foreach ($metabox['fields'] as $key => $value) {
                    $fields[] = $value;
                    if (!empty($value['id']))
                        $fields = apply_filters('wpbooking_metabox_after_' . $metabox['id'] . '_field_' . $value['id'], $fields, $value);
                }

                $metabox['fields'] = $fields;
            }

            return $metabox;
        }

        public function add_meta_boxes()
        {
            foreach ((array)$this->metabox['pages'] as $page) {
                add_meta_box($this->metabox['id'], $this->metabox['title'], array($this, 'build_metabox'), $page, $this->metabox['context'], $this->metabox['priority']);
            }
        }

        public function build_metabox($post, $metabox)
        {
            ?>
            <div class="st-metabox-wrapper">
                <div id="<?php echo 'st-metabox-tabs-' . $this->metabox['id']; ?>" class="st-metabox-tabs">

                    <div class="st-metabox-tab-content-wrap  row">
                        <?php
                        // Service Type fields
                        $service_type_field = array(
                            'post_id' => get_the_ID(),
                            'id'      => 'service_type',
                            'label'   => esc_html__('Service Type', 'wpbooking'),
                            'width'   => '',
                            'desc'    => ''
                        );
                        $field_html = apply_filters('wpbooking_metabox_field_html_service-type-select', FALSE, $service_type_field, get_the_ID());
                        if ($field_html) echo $field_html;
                        else
                            echo wpbooking_admin_load_view('metabox-fields/service-type-select', array('data' => $service_type_field, 'post_id' => get_the_ID()));
                        ?>
                    </div>
                    <div class="wpbooking-metabox-template">

                    </div>
                </div>
            </div>
            <?php
        }


        /**
         * Start Save Metabox for specific Section
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $post_id INT Post ID
         * @param $sections array List Fields of one Sections
         * @param $section_id int ID of Section
         *
         *
         */
        public function do_save_metabox($post_id,$sections,$section_id)
        {
            if (empty($sections)) return;

            foreach ($sections as $field) {
                if (empty($field['id'])) continue;

                if ($field['type'] == 'list-item') {
                    continue;
                }
                $old = get_post_meta($post_id, $field['id'], TRUE);
                $new = '';

                /* there is data to validate */
                if (isset($_POST[$field['id']])) {

                    /* set up new data with validated data */
                    $new = $_POST[$field['id']];

                }



                // Property Size

                switch ($field['type']) {
                    case "property_size":
                        if (!empty($field['unit_id'])) update_post_meta($post_id, $field['unit_id'], WPBooking_Input::post($field['unit_id']));
                        if (isset($new) && $new !== $old) {
                            update_post_meta($post_id, $field['id'], $new);

                        } else if ('' == $new && $old) {
                            delete_post_meta($post_id, $field['id'], $old);
                        }
                        break;
                    case "address":
                        $array = array('zip_code', 'address', 'apt_unit', 'location_id');
                        foreach ($array as $name) {
                            if (isset($_POST[$name])) {

                                update_post_meta($post_id, $name, WPBooking_Input::post($name));
                            }
                        }
                        if (isset($new) && $new !== $old) {
                            update_post_meta($post_id, $field['id'], $new);

                        } else if ('' == $new && $old) {
                            delete_post_meta($post_id, $field['id'], $old);
                        }
                        break;
                    case "extra_services":
                        if (!empty($new)) {
                            foreach ($new as $new_key => $new_item) {
                                if (!empty($new_item)) {
                                    foreach ($new_item as $key => $value) {
                                        if (empty($value['is_selected'])) unset($new[$new_key][$key]);
                                    }
                                }
                            }
                        }
                        update_post_meta($post_id, $field['id'], $new);
                        break;
                    case "taxonomy_room_select":
                        $this->wpbooking_save_taxonomy_room($post_id,$field['id']);
                        break;
                    case "taxonomy_fee_select":
                        $this->wpbooking_save_taxonomy_fee($post_id,$field['id'],$field);

                        break;
                    case "calendar":

                        $old = get_post_meta($post_id, 'price-default', TRUE);
                        if (isset($_POST['price-default'])) {

                            $new = $_POST['price-default'];
                        }
                        if (isset($new) && $new !== $old) {
                            update_post_meta($post_id, 'price-default', $new);
                        } else if ('' == $new && $old) {
                            delete_post_meta($post_id, 'price-default', $old);
                        }
                        break;

                    case "bed_options":
                        $this->wpbooking_save_bed_options($post_id,$field['id'],$field);
                        break;
                    case "living_options":
                        $this->wpbooking_save_living_options($post_id,$field['id'],$field);
                        break;
                    case "room_size":
                        $data_multi=WPBooking_Input::post($field['id']);
                        if(!empty($data_multi)){
                            foreach($data_multi as $k=>$v){
                                update_post_meta($k,'room_size',$v);
                            }
                        }
                        break;
                    default :
                        if (isset($new) && $new !== $old) {
                            update_post_meta($post_id, $field['id'], $new);

                        } else if ('' == $new && $old) {
                            delete_post_meta($post_id, $field['id'], $old);
                        }
                        break;
                }

                // Fields to Save
                if(!empty($field['fields'])){
                    foreach($field['fields'] as $f){
                        if(isset($_POST[$f]))
                        update_post_meta($post_id,$f,$_POST[$f]);
                    }
                }
            }

            do_action('wpbooking_save_metabox_section', $post_id,$section_id, $sections);
        }

        function wpbooking_save_taxonomy_fee($post_id,$field_id,$field)
        {
            $data=WPBooking_Input::post($field_id);

            $post_terms=array();

            if(!empty($data)){
                foreach($data as $term_id=>$item){
                    if(!empty($item['selected'])){
                        $post_terms[]=$term_id;
                    }else{
                        unset($data[$term_id]);
                    }

                }
            }

            if(!empty($post_terms) and !empty($field['taxonomy'])){
                wp_set_object_terms( $post_id, $post_terms, $field['taxonomy'] );
            }
            update_post_meta($post_id,$field_id,$data);
        }

        function wpbooking_save_bed_options($post_id,$field_id,$field)
        {

            $data_single = WPBooking_Input::post($field_id.'_single_');
            $data_single_guests = WPBooking_Input::post($field_id.'_single_num_guests');
            $data_single_bathroom = WPBooking_Input::post($field_id.'_single_private_bathroom');
            $list = array();
            if(!empty($data_single['bed_type'])){
                foreach($data_single['bed_type'] as $k=>$v){
                    $list[] = array(
                        'bed_type'=>$v,
                        'number'=>$data_single['number'][$k],
                    );

                }
            }
            update_post_meta($post_id,$field_id.'_single_',$list);
            update_post_meta($post_id,$field_id.'_single_num_guests',$data_single_guests);
            update_post_meta($post_id,$field_id.'_single_private_bathroom',$data_single_bathroom);


            $data_multi=WPBooking_Input::post($field_id.'_multi_');
            if(!empty($data_multi['__number_room__'])){
                unset($data_multi['__number_room__']);
                $data_multi = array_values($data_multi);


                foreach($data_multi as $k=>$v){
                    $list = array();
                    $lisst_bed_type = $v['bed_type']['bed_type'];
                    foreach($lisst_bed_type as $k2=>$v2){
                        $list[] = array(
                            'bed_type'=>$v2,
                            'number' => $v['bed_type']['number'][$k2]
                        );
                    }
                    $data_multi[$k]['bed_type'] = $list;
                }

            }
            update_post_meta($post_id,$field_id.'_multi_',$data_multi);
        }
        function wpbooking_save_living_options($post_id,$field_id,$field)
        {
            $data_multi=WPBooking_Input::post($field_id);
            if(!empty($data_multi['__number_living__'])){
                unset($data_multi['__number_living__']);
                $data_multi = array_values($data_multi);
            }
            update_post_meta($post_id,$field_id,$data_multi);

        }

        /**
         * Save Taxonomy Room Metabox
         *
         * @since 1.0
         * @author quandq
         *
         * @param $post_id
         * @param $field_id
         */
        public function wpbooking_save_taxonomy_room($post_id,$field_id)
        {
            $list = WPBooking_Input::post($field_id);
            $list_base = WPBooking_Input::post($field_id.'_base');
            $terms = array();
            $terms_meta = array();
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $key_term = '';
                    $term = '';
                    foreach($v as $key=>$value){
                        if($key != "post_id"){
                            $terms[] = $value;
                            $term = $value;
                            $key_term = $key;
                            if(!empty($list_base[$key])){
                                $list_base[$key] = true;
                            }
                        }
                        if($key == "post_id"){
                            foreach($value as $key_2 => $value2){
                                $terms_meta[$value2][$key_term][] = $term;
                            }
                        }
                    }
                }
            }
            $list_room = WPBooking_Accommodation_Service_Type::inst()->_get_room_by_hotel($post_id);
            if(!empty($list_base)){
                foreach($list_base as $k=>$v){
                    if($v == 'true'){
                        wp_set_post_terms($post_id, $terms, $k);
                        foreach($list_room as $key_room => $value_room){
                            if(!empty($terms_meta)){
                                foreach($terms_meta as $k2=>$v2){
                                    $key = key($v2);
                                    $data_meta = get_post_meta($k2,'taxonomy_room',true);
                                    if(empty($data_meta)) $data_meta = array();
                                    $data_meta[$key] = $v2[$key];
                                    update_post_meta($k2,'taxonomy_room',$data_meta);
                                }
                            }else{
                                foreach($list_room as $k2=>$v2){
                                    $data_meta = get_post_meta($v2['ID'],'taxonomy_room',true);
                                    if(empty($data_meta)) $data_meta = array();
                                    $data_meta[$k] = '';
                                    update_post_meta($v2['ID'],'taxonomy_room',$data_meta);
                                }
                            }
                        }
                    }else{
                        wp_set_post_terms($post_id, array(0), $k);
                        foreach($list_room as $k2=>$v2){
                            $data_meta = get_post_meta($v2['ID'],'taxonomy_room',true);
                            if(empty($data_meta)) $data_meta = array();
                            $data_meta[$k] = '';
                            update_post_meta($v2['ID'],'taxonomy_room',$data_meta);
                        }
                    }
                }
            }
        }

        public function wpbooking_save_gmap($post_id, $post_object)
        {
            if (isset($_POST['map_lat']) && isset($_POST['map_long']) && isset($_POST['map_zoom'])) {
                $map_lat = (float)WPBooking_Input::post('map_lat', 0);
                $map_long = (float)WPBooking_Input::post('map_long', 0);
                $map_zoom = (int)WPBooking_Input::post('map_zoom', 0);

                update_post_meta($post_id, 'map_lat', $map_lat);
                update_post_meta($post_id, 'map_long', $map_long);
                update_post_meta($post_id, 'map_zoom', $map_zoom);
            }

            return $post_id;

        }

        /**
         * Save Location Metabox
         *
         * @since 1.0
         * @author haint
         *
         * @contributor dungdt
         *
         * @param $post_id
         * @param $section_id
         * @param $fields
         * @return mixed
         */
        public function wpbooking_save_location($post_id, $section_id,$fields)
        {
            foreach ($fields as $field) {
                if ($field['type'] == 'address' and isset($_POST['location_od'])) {

                    $new = WPBooking_Input::post('location_id', '');
                    if ($new) {
                        wp_set_post_terms($post_id, array($new), 'wpbooking_location');
                    } else {

                        wp_set_post_terms($post_id, array(0), 'wpbooking_location');
                    }

                }
            }

            return $post_id;
        }

        /**
         * Save Taxonomy Metabox
         *
         * @since 1.0
         * @author haint
         *
         * @contributor dungdt
         *
         * @param $post_id
         * @param $section_id
         * @param $fields
         * @return mixed
         */
        public function wpbooking_save_taxonomies($post_id, $section_id,$fields)
        {
            foreach ($fields as $field) {
                if ($field['type'] == 'taxonomies') {

                    $terms = WPBooking_Input::post($field['id'], '');


                    $service = get_post_meta($post_id, 'service_type', TRUE);
                    if (!$service) $service = 'room';

                    $term_service = get_option('wpbooking_taxonomies', array());
                    if (!empty($term_service) && is_array($term_service)) {
                        foreach ($term_service as $key => $term) {
                            if (in_array($service, $term['service_type'])) {
                                wp_set_post_terms($post_id, array(0), $key);
                            }
                        }
                    }

                    if (!empty($terms) && is_array($terms)) {
                        foreach ($terms as $key => $val) {
                            if (!empty($val) && is_array($val)) {
                                wp_set_post_terms($post_id, $val, $key);
                            } else {
                                wp_set_post_terms($post_id, array(0), $key);
                            }
                        }
                    }
                }
            }

            return $post_id;
        }

        public function wpbooking_save_list_item($post_id, $post_object,$fields)
        {
            foreach ($fields as $field) {

                if ($field['type'] == 'list-item') {
                    if (isset($_POST[$field['id']]) && is_array($_POST[$field['id']])) {
                        $new_list = array();
                        $list = $_POST[$field['id']];

                        $i = 0;
                        for ($j = 0; $j < count($list['title']) - 1; $j++) {
                            foreach ($list as $key1 => $val1) {
                                $new_list[$i][$key1] = $list[$key1][$i];
                            }
                            $i++;
                        }

                        update_post_meta($post_id, $field['id'], $new_list);
                    } else {
                        continue;
                    }
                }
            }

            return $post_id;
        }

        static function inst()
        {
            if (!self::$_inst) {
                self::$_inst = new self();
            }

            return self::$_inst;
        }


    }

    WPBooking_Metabox::inst();
}