<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php _e("Settings",'st_membership') ?></h2>
</div>
<?php
$custom_settings = Traveler_Admin_Setting::inst()->_init_settings();
var_dump($custom_settings);
?>
<div class="wrap">
    <?php $is_tab = Traveler_Input::request('tab'); ?>
    <h2 class="nav-tab-wrapper">
        <?php if(!empty($custom_settings)){
            $i=0;
            foreach($custom_settings as $k=>$v){
                if(empty($is_tab) and $i == 0){
                    $is_tab = $k;
                }
                ?>
                <a class="nav-tab <?php if($is_tab == $k) echo "nav-tab-active"; ?>" href="<?php echo add_query_arg(array("page"=>"st_membership_page_settings","tab"=>$k),admin_url("admin.php")) ?>"><?php echo esc_html($v['name']) ?></a>
                <?php
                $i++;
            }
        } ?>
    </h2>
</div>
<div class="wrap">
    <ul class="subsubsub">
        <?php
        $is_section = Traveler_Input::request('section');
        if(!empty($custom_settings[$is_tab]) and !empty($custom_settings[$is_tab]['sections'])){
            $i=0;
            $sections=apply_filters('st_settings_'.$is_tab.'_sections',$custom_settings[$is_tab]['sections']);
            foreach($sections as $k=>$v){
                if(empty($is_section) and $i == 0){
                    $is_section = $v['id'];
                }
                $url = add_query_arg(array("page"=>"st_membership_page_settings","tab"=>$is_tab,'section'=>$v['id']),admin_url("admin.php"));
                $is_class = "";
                if($is_section == $v['id']) $is_class = "current";
                echo '<li><a class="'.$is_class.'" href="'.$url.'">'.$v['label'].'</a> | </li>';
                $i++;
            }
        }
        ?>
    </ul>
    <br class="clear">
    <div class="content-field">
        <form method="post" action="" id="form-settings-admin">
            <?php wp_nonce_field('shb_action','shb_save_field') ?>
            <input type="hidden" name="traveler_booking_save_settings" value="true" >
            <?php
            if(!empty($custom_settings[$is_tab]) and !empty($custom_settings[$is_tab]['sections'][$is_section]['fields'])){
                $fields=apply_filters('st_settings_'.$is_tab.'_'.$is_section.'_fields',$custom_settings[$is_tab]['sections'][$is_section]['fields']);
                foreach($fields as $k=>$v){
                    $default = array(
                        'id'      => '' ,
                        'label'   => '' ,
                        'desc'    => '' ,
                        'type'    => '' ,
                        'std'     =>''
                    );
                    $v = wp_parse_args( $v , $default );
                    $path='admin/fields/'.$v['type'];
                    $field_file=apply_filters('st_setting_field_type_'.$v['type'].'_path',$path);
                    echo traveler_admin_load_view($field_file,array('data'=>$v));
                }
            }
            ?>
            <input type="submit" class="btn button" value="<?php _e("Save") ?>">
        </form>
    </div>
</div>