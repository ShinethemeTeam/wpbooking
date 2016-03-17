<?php $value = traveler_get_option($data['id'],$data['std']);
$custom_settings = $data['value'];
$id_list_item = $data['id'];
?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?> traveler-setting-list-item ">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td class="">
        <ul class="data_content_list_item">
            <?php
            $i=0;
            if(!empty($value)){
                foreach($value as $k=>$v){

                    ?>
                    <li class="number_list_<?php echo esc_html($i) ?> ">
                        <div class="list-content">
                            <div class="list-title">
                                <?php echo esc_html($v['title']) ?>
                            </div>
                            <div class="button-section">
                                <a title="Edit" class="button left-item btn_list_item_edit" href="javascript:void(0);">
                                    <span class="icon ot-icon-pencil"></span><?php _e("Edit",'traveler-booking') ?>
                                </a>
                                <a title="Delete" class=" button button-secondary light right-item btn_list_item_del" href="javascript:void(0);">
                                    <span class="icon ot-icon-trash-o"></span><?php _e("Delete",'traveler-booking') ?>
                                </a>
                            </div>
                            <div class="traveler-setting-setting-body">
                                <div class="list-item">
                                    <table class="form-table traveler-settings">
                                        <tbody>
                                        <tr class="">
                                            <th scope="row">
                                                <label for=""><?php _e("Title",'traveler-booking') ?>:</label>
                                            </th>
                                            <td>
                                                <input type="text" class="form-control form-control-admin min-width-500 list_item_title" value="<?php echo esc_html($v['title']) ?>" name="traveler_booking_list_item[<?php echo $id_list_item ?>][<?php echo esc_attr($i) ?>][title]" placeholder="title">
                                            </td>
                                        </tr>
                                        <?php
                                        if(!empty($custom_settings)){
                                            foreach($custom_settings as $k2=>$v2){
                                                $id = $v2['id'];
                                                $cusstom_value = "";
                                                if(!empty($v[$id])){
                                                    $cusstom_value = $v[$id];
                                                }
                                                $default = array( 'id' => '' , 'label' => '' , 'desc' => '' , 'type' => '' , 'std' => '', 'taxonomy' => '' ,'element_list_item' => true ,'custom_name' => 'traveler_booking_list_item['.$id_list_item.']['.$i.']['.$id.']', 'custom_value' =>$cusstom_value);
                                                $v2 = wp_parse_args( $v2 , $default );
                                                $path='fields/'.$v2['type'];
                                                echo $xx =  traveler_admin_load_view($path,array('data'=>$v2,'slug_page_menu'=>$slug_page_menu));
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </li>
            <?php
                    $i++;
                }
            }
            ?>
        </ul>
        <input type="hidden" class="traveler_booking_number_last_list_item" value="<?php echo esc_html($i) ?>">
        <button type="button" class="button button-primary btn_add_new_list_item" ><?php _e("Add New",'traveler-booking') ?></button>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
        <div class="content_list_item_hide">
            <li class="number_list___number_list__">
                <div class="list-content">
                    <div class="list-title">

                    </div>
                    <div class="button-section">
                        <a title="Edit" class=" button left-item btn_list_item_edit" href="javascript:void(0);">
                            <span class="icon ot-icon-pencil"></span><?php _e("Edit",'traveler-booking') ?>
                        </a>
                        <a title="Delete" class=" button button-secondary light right-item btn_list_item_del" href="javascript:void(0);">
                            <span class="icon ot-icon-trash-o"></span><?php _e("Delete",'traveler-booking') ?>
                        </a>
                    </div>
                    <div class="traveler-setting-setting-body">
                        <div class="list-item">
                            <table class="form-table traveler-settings">
                                <tbody>
                                <tr class="">
                                    <th scope="row">
                                        <label for=""><?php _e("Title",'traveler-booking') ?>:</label>
                                    </th>
                                    <td>
                                        <input type="text" class="form-control form-control-admin min-width-500 list_item_title" name="traveler_booking_list_item[<?php echo $id_list_item ?>][__number_list__][title]" placeholder="title">
                                    </td>
                                </tr>
                                <?php
                                if(!empty($custom_settings)){
                                    foreach($custom_settings as $k2=>$v2){
                                        $id = $v2['id'];
                                        $default = array( 'id' => '' , 'label' => '' , 'desc' => '' , 'type' => '' , 'std' => '', 'taxonomy' => '' , 'element_list_item' => true ,'custom_name' => 'traveler_booking_list_item['.$id_list_item.'][__number_list__]['.$id.']', 'custom_value' =>"");
                                        $v2 = wp_parse_args( $v2 , $default );
                                        $path='fields/'.$v2['type'];
                                        echo $xx =  traveler_admin_load_view($path,array('data'=>$v2,'slug_page_menu'=>$slug_page_menu));
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </li>
        </div>
    </td>
</tr>
<script>
    jQuery(document).ready(function($) {
        $(".btn_add_new_list_item").click(function () {
            var container = $(this).parent();
            var content_html = container.find(".content_list_item_hide").html();
            var number_list = container.find('.traveler_booking_number_last_list_item').val();
            content_html = content_html.replace(/__number_list__/g, number_list);



            container.find('.data_content_list_item').append(content_html);


            container.find('.traveler-setting-setting-body').slideUp(500);
            container.find('.number_list_'+number_list+' .traveler-setting-setting-body').slideDown(500);

            number_list = Number(number_list) + 1;
            container.find('.traveler_booking_number_last_list_item').val(number_list);
        });

        $(document).on('click', '.btn_list_item_del', function(event) {
            var container = $(this).parent().parent().parent();
            container.remove();
        });
        $(document).on('click', '.btn_list_item_edit', function(event) {
            var container_full = $(this).parent().parent().parent().parent();
            var container = $(this).parent().parent().parent();
            container_full.find('.traveler-setting-setting-body').slideUp(500);
            $check = container.find('.traveler-setting-setting-body').css('display');
            if($check == "none"){
                container.find('.traveler-setting-setting-body').slideDown(500);
            }else{
                container.find('.traveler-setting-setting-body').slideUp(500);
            }
        });
        $(document).on('click', '.list_item_title', function(event) {
            $(this).keyup(function(){
                var $value = $(this).val();
                var container = $(this).parent().parent().parent().parent().parent().parent().parent();
                console.log(container);
                container.find('.list-title').html($value);
            });
        });



        /*$('.list_item_title').each(function(){
            $(this).keyup(function(){


                var $value = $(this).val();
                var container = $(this).parent().parent().parent().parent().parent().parent().parent();
                console.log(container);
                container.find('.list-title').html($value);

            });
        })*/

    });
</script>