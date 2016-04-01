<?php
$menu_page=Traveler_Admin_Form_Build::inst()->get_menu_page();
$slug_page_menu = $menu_page['menu_slug'];
$form_id = Traveler_Input::request('form_builder_id');
?>
<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php _e( "Form Builder" , 'traveler-booking' ) ?></h2>
    <div class="msg">
        <?php echo traveler_get_admin_message(); ?>
    </div>
</div>
<form method="post" action="" id="form-settings-admin">
    <?php wp_nonce_field('traveler_booking_action','traveler_booking_save_layout') ?>
    <div class="wrap">
        <br class="clear">
        <div class="traveler-container-fluid">
            <div class="traveler-row form-build-v2">
                <div class="traveler-col-md-12 head-form">
                    <h3 class=""><span><?php _e("Form Content",'traveler-booking') ?></span></h3>
                </div>
                <div class="traveler-col-md-12">
                    <table class="traveler-select-layout">
                        <tr class="">
                            <th scope="row">
                                <label><?php _e("Select Form:",'traveler-booking') ?></label>
                            </th>
                            <td>
                                <?php
                                $list_layout = Traveler_Admin_Form_Build::inst()->_get_list_layout();
                                ?>
                                <select name="traveler-layout-id" class="form-control min-width-200" id="traveler-layout-id">
                                    <option value=""><?php _e("-- Select Form --",'traveler-booking') ?></option>
                                    <?php
                                    if(!empty($list_layout)){
                                        $group = '';
                                        foreach($list_layout as $key=>$value):
                                            if($group != $key)
                                                echo '<optgroup label=" ' . $key . ' ">';
                                            if(!empty( $value )) {
                                                foreach( $value as $k => $v ) {
                                                    $check = "";
                                                    if($form_id == $v[ 'id' ] ){
                                                        $check = 'selected';
                                                    }
                                                    echo '<option '.$check.' value="' . $v[ 'id' ] . '">' . $v[ 'name' ] . '</option>';
                                                }
                                            }
                                            if($group != $key) {
                                                echo '</optgroup>';
                                                $group = $key;
                                            }
                                        endforeach;
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <a hidden="#" data-url="<?php echo  add_query_arg(array("page"=>$slug_page_menu,"form_builder_id"=>'__form__id'),admin_url("admin.php")) ?>"  class="button url_layout" ><?php _e("Select","traveler-booking") ?></a>

                            </td>
                            <td class="text-right w100_">
                                <?php if(!empty($form_id)) {?>
                                    <a href="<?php echo  add_query_arg(array("page"=>$slug_page_menu),admin_url("admin.php")) ?>"  class="button button-primary" ><?php _e("Add New","traveler-booking") ?></a>
                                    <a href="<?php echo  add_query_arg(array("page"=>$slug_page_menu,'del_layout'=>$form_id),admin_url("admin.php")) ?>" data-msg="<?php _e("Press OK to delete layout, Cancel to leave",'traveler-booking') ?>"  class="button btn_del_layout" ><?php _e("Delete","traveler-booking") ?></a>
                                <?php }?>
                            </td>
                        </tr>
                    </table>
                </div>


                <div class="traveler-col-md-7">
                    <table class="traveler-title-layout">
                        <tr class="">
                            <th scope="row">
                                <label for="dropdown"><?php _e("Form Title:",'traveler-booking') ?></label>
                                <input class="" type="text" name="traveler-title" value="<?php if(!empty($form_id)) echo get_the_title($form_id) ?>">
                            </th>
                            <th>
                                <?php
                                $type_layout = "";
                                if(!empty($form_id)){
                                    $type_layout = get_post_meta($form_id,'type_layout',true);
                                }
                                $list_type = Traveler_Admin_Form_Build::inst()->_get_list_type_layout();
                                ?>
                                <label for="dropdown"><?php _e("Form Type:",'traveler-booking') ?></label>
                                <select name="traveler-layout-type" class="form-control min-width-200">
                                    <?php if(!empty($list_type)){
                                        foreach($list_type as $k=>$v){
                                            $check = "";
                                            if($type_layout == $v ){
                                                $check = 'selected';
                                            }
                                            echo '<option '.$check.' value="'.$v.'">'.$v.'</option>';
                                        }
                                    } ?>
                                </select>
                            </th>
                        </tr>
                    </table>
                </div>
                <div class="traveler-col-md-7">
                    <div class="form-content">
                        <?php
                        $content = '';
                        if(!empty($form_id)){
                            $content_post = get_post($form_id);
                            $content = $content_post->post_content;
                        }
                        wp_editor(stripslashes($content),'traveler-content-build'); ?>
                    </div>
                </div>
                <div class="traveler-col-md-5">
                    <div class="select-control">
                        <?php $list_flied = Traveler_Admin_Form_Build::inst()->traveler_get_all_field(); ?>
                        <label for="traveler_field_form_build" class="control-label"><?php _e("Add Field",'traveler-booking') ?>:</label>
                        <select name="traveler_field_form_build" class="form-control" id="traveler_field_form_build">
                            <option selected value=""><?php _e("-- Select Flied --",'traveler-booking') ?></option>
                            <?php
                            if(!empty($list_flied)){
                                foreach($list_flied as $k=>$v){
                                    echo '<option value="'.$v['name'].'">'.$v['title'].'</option>';
                                }
                            }
                            ?>
                        </select>
                        <i class="desc"><?php _e("Select shortcode and configs",'traveler-booking') ?></i>
                    </div>
                    <div class="control-hidden hidden" >
                        <?php
                        if(!empty($list_flied)){
                            foreach($list_flied as $k=>$v){
                                ?>
                                <div class="hidden-item" data-name-shortcode="<?php echo esc_attr($v['name']) ?>" data-title="<?php echo esc_attr($v['title']) ?>" id="item-<?php echo esc_attr($v['name']) ?>">
                                    <?php if(!empty($v['options'])){
                                        foreach($v['options'] as $key => $value){
                                            $default = array( "type"             => "" ,
                                                              "title"            => "" ,
                                                              "name"             => "" ,
                                                              "desc"      => "" ,
                                                              'edit_field_class' => '' ,
                                                              'options' => '' ,
                                                              'value'            => '' );
                                            $value = wp_parse_args( $value , $default );

                                            $path='fields/form-build/input-'.$value['type'];
                                            echo traveler_admin_load_view($path,array('data'=>$value,'parent'=>$v['name']));
                                        }
                                    } ?>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="select-control div-content-control">
                        <div class="head"></div>
                        <hr>
                        <div class="content-control">
                            <div class="traveler-row content-flied-control"></div>
                        </div>
                        <hr>
                        <div>
                            <div class="traveler-row">
                                <div class="traveler-col-md-12">
                                    <div class="traveler-build-group ">
                                        <label class="control-label"><?php _e("Copy and paste this shortcode into the form at left side",'traveler-booking') ?></label>
                                        <input type="text"   name="" id="traveler-shortcode-flied" readonly="readonly" onclick="this.focus();this.select()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="traveler-col-md-12">
                    <div class="save-control">
                        <input type="submit" name="traveler_booking_btn_save_layout" class="btn button button-primary" value="<?php _e("Save Form",'traveler-booking') ?>">
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
