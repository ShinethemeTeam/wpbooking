<?php $value     = traveler_get_option( $data[ 'id' ] , $data[ 'std' ] );
$custom_settings = $data[ 'value' ];
$id_list_item    = $data[ 'id' ];
?>
<tr class="traveler-setting-<?php echo esc_html( $data[ 'id' ] ) ?> traveler-setting-list-item ">
    <th scope="row">
        <label for="<?php echo esc_html( $data[ 'id' ] ) ?>"><?php echo esc_html( $data[ 'label' ] ) ?>:</label>
    </th>
    <td class="">
        <table class="traveler-list-item-wrap" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th class="td td-left"><span class="dashicons dashicons-admin-tools"></span></th>
                <th class="td td-center"><?php _e( 'Title' , 'traveler-booking' ) ?></th>
                <th class="td td-right"><?php _e( 'Actions' , 'traveler-booking' ) ?></th>
            </tr>
            </thead>
            <tbody class="data_content_list_item">
            <?php
            $i = 0;
            if(!empty( $value )) {
                foreach( $value as $k => $v ) {
                    ?>
                    <tr class="number_list_<?php echo esc_html( $i ) ?> ">
                        <td class="td td-left"><span class="dashicons dashicons-menu"></span></td>
                        <td class="td td-center">
                            <div class="list-content">
                                <div class="list-title">
                                    <?php echo esc_html( $v[ 'title' ] ) ?>
                                </div>
                                <div class="traveler-setting-setting-body">
                                    <div class="list-item">
                                        <table class="form-table traveler-settings">
                                            <tbody>
                                            <tr class="">
                                                <th scope="row">
                                                    <label for=""><?php _e( "Title" , 'traveler-booking' ) ?>:</label>
                                                </th>
                                                <td>
                                                    <input type="text"
                                                           class="form-control  min-width-500 list_item_title"
                                                           value="<?php echo esc_html( $v[ 'title' ] ) ?>"
                                                           name="traveler_booking_list_item[<?php echo esc_attr($id_list_item) ?>][<?php echo esc_attr( $i ) ?>][title]"
                                                           placeholder="title">
                                                </td>
                                            </tr>
                                            <?php
                                            if(!empty( $custom_settings )) {
                                                foreach( $custom_settings as $k2 => $v2 ) {
                                                    $id            = $v2[ 'id' ];
                                                    $cusstom_value = "";
                                                    if(!empty( $v[ $id ] )) {
                                                        $cusstom_value = $v[ $id ];
                                                    }
                                                    if(!empty($v2['std'])){
                                                        $cusstom_value = $v2['std'];
                                                    }
                                                    $default = array(
                                                        'id'                => '' ,
                                                        'label'             => '' ,
                                                        'desc'              => '' ,
                                                        'type'              => '' ,
                                                        'std'               => '' ,
                                                        'taxonomy'          => '' ,
                                                        'element_list_item' => true ,
                                                        'custom_name'       => 'traveler_booking_list_item[' . $id_list_item . '][' . $i . '][' . $id . ']' ,
                                                        'custom_value'      => $cusstom_value
                                                    );
                                                    $v2      = wp_parse_args( $v2 , $default );
                                                    $path    = 'fields/' . $v2[ 'type' ];
                                                    echo $xx = traveler_admin_load_view( $path , array(
                                                        'data'           => $v2 ,
                                                        'slug_page_menu' => $slug_page_menu
                                                    ) );
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="td td-right">
                            <div class="button-section">
                                <a title="<?php _e( 'Edit' , 'traveler-booking' ) ?>"
                                   class="button button-primary btn_list_item_edit" href="javascript:void(0);">
                                    <span class="fa fa-pencil"></span>
                                </a>
                                <a title="<?php _e( 'Delete' , 'traveler-booking' ) ?>"
                                   class="button button-secondary light right-item btn_list_item_del"
                                   href="javascript:void(0);">
                                    <span class="fa fa-trash-o"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?>
            </tbody>
            <tfoot class="content_list_item_hide">
            <tr class="number_list___number_list__">
                <td class="td td-left"><span class="dashicons dashicons-menu"></span></td>
                <td class="td td-center">
                    <div class="list-content">
                        <div class="list-title"></div>
                        <div class="traveler-setting-setting-body">
                            <div class="list-item">
                                <table class="form-table traveler-settings">
                                    <tbody>
                                    <tr class="">
                                        <th scope="row">
                                            <label for=""><?php _e( "Title" , 'traveler-booking' ) ?>:</label>
                                        </th>
                                        <td>
                                            <input type="text" class="form-control  min-width-500 list_item_title"
                                                   name="traveler_booking_list_item[<?php echo $id_list_item ?>][__number_list__][title]"
                                                   placeholder="title">
                                        </td>
                                    </tr>
                                    <?php
                                    if(!empty( $custom_settings )) {
                                        foreach( $custom_settings as $k2 => $v2 ) {
                                            $id      = $v2[ 'id' ];
                                            $default = array( 'id'                => '' ,
                                                              'label'             => '' ,
                                                              'desc'              => '' ,
                                                              'type'              => '' ,
                                                              'std'               => '' ,
                                                              'taxonomy'          => '' ,
                                                              'element_list_item' => true ,
                                                              'custom_name'       => 'traveler_booking_list_item[' . $id_list_item . '][__number_list__][' . $id . ']' ,
                                                              'custom_value'      => ""
                                            );
                                            $v2      = wp_parse_args( $v2 , $default );
                                            $path    = 'fields/' . $v2[ 'type' ];
                                            echo $xx = traveler_admin_load_view( $path , array( 'data'           => $v2 ,
                                                                                                'slug_page_menu' => $slug_page_menu
                                            ) );
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="td td-right">
                    <div class="button-section">
                        <a title="<?php _e( 'Edit' , 'traveler-booking' ) ?>"
                           class="button button-primary btn_list_item_edit" href="javascript:void(0);">
                            <span class="fa fa-pencil"></span>
                        </a>
                        <a title="<?php _e( 'Delete' , 'traveler-booking' ) ?>"
                           class="button button-secondary light right-item btn_list_item_del"
                           href="javascript:void(0);">
                            <span class="fa fa-trash-o"></span>
                        </a>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
        <input type="hidden" class="traveler_booking_number_last_list_item" value="<?php echo esc_html( $i ) ?>">
        <button type="button"
                class="button button-primary btn_add_new_list_item"><?php _e( "Add New" , 'traveler-booking' ) ?></button>
        <i class="traveler-desc"><?php echo balanceTags( $data[ 'desc' ] ) ?></i>
    </td>
</tr>
