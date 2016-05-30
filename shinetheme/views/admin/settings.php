<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php _e("Settings",'wpbooking') ?></h2>
</div>
<?php
$custom_settings = WPBooking_Admin_Setting::inst()->_get_settings();
$menu_page=WPBooking_Admin_Setting::inst()->get_menu_page();
$slug_page_menu = $menu_page['menu_slug'];
?>
<div class="wrap">
    <?php $is_tab = WPBooking_Input::request('st_tab'); ?>
    <h2 class="nav-tab-wrapper">
        <?php if(!empty($custom_settings)){
            $i=0;
            foreach($custom_settings as $k=>$v){
                if(empty($is_tab) and $i == 0){
                    $is_tab = $k;
                }
                ?>
                <a class="nav-tab <?php if($is_tab == $k) echo "nav-tab-active"; ?>" href="<?php echo add_query_arg(array("page"=>$slug_page_menu,"st_tab"=>$k),admin_url("admin.php")) ?>"><?php echo esc_html($v['name']) ?></a>
                <?php
                $i++;
            }
        } ?>
    </h2>
</div>
<div class="wrap">
    <ul class="subsubsub">
        <?php
        $is_section = WPBooking_Input::request('st_section');
        $title_page_active = "";
        if(!empty($custom_settings[$is_tab]) and !empty($custom_settings[$is_tab]['sections'])){
            $i=0;
            $sections=apply_filters('st_settings_'.$is_tab.'_sections',$custom_settings[$is_tab]['sections']);
            foreach($sections as $k=>$v){
                if(empty($is_section) and $i == 0){
                    $is_section = $v['id'];
                }
                $url = add_query_arg(array("page"=>$slug_page_menu,"st_tab"=>$is_tab,'st_section'=>$v['id']),admin_url("admin.php"));
                $is_class = "";
                if($is_section == $v['id']) {
                    $is_class = "current";
                    $title_page_active = $v['label'];
                }
                echo '<li><a class="'.$is_class.'" href="'.$url.'">'.$v['label'].'</a>  </li>';
                if( ( $i+ 1) < count($sections)) echo "|";
                echo '</li>';
                $i++;
            }
        }
        ?>
    </ul>
    <br class="clear">
   <!-- <h3><?php /*echo esc_html($title_page_active) */?></h3>-->
    <div class="content-field">
        <form method="post" action="" id="form-settings-admin">
            <?php wp_nonce_field('wpbooking_action','wpbooking_save_settings_field') ?>
            <input type="hidden" name="wpbooking_save_settings" value="true" >
            <table class="form-table wpbooking-settings ">
                <tbody>
                    <?php
                    if(!empty($custom_settings[$is_tab]) and !empty($custom_settings[$is_tab]['sections'][$is_section]['fields'])){
                        $fields=apply_filters('wpbooking_settings_'.$is_tab.'_'.$is_section.'_fields',$custom_settings[$is_tab]['sections'][$is_section]['fields']);
                        foreach($fields as $k=>$v){
                            $default = array( 'id' => '' , 'label' => '' , 'desc' => '' , 'type' => '' , 'std' => '', 'taxonomy' => '' );
                            $v = wp_parse_args( $v , $default );
                            $path='fields/'.$v['type'];
                            $field_file=apply_filters('wpbooking_field_type_'.$v['type'].'_path',$path);
                            $html =  wpbooking_admin_load_view($field_file,array('data'=>$v,'slug_page_menu'=>$slug_page_menu));
                            echo apply_filters('wpbooking_field_type_'.$v['type'].'_html',$html);
                        }
                    }
                    ?>

                </tbody>
            </table>
            <input type="submit" class="btn button button-primary" value="<?php _e("Save Settings",'wpbooking') ?>">
        </form>
    </div>
</div>