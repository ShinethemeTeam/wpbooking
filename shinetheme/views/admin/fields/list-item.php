<?php $value     = wpbooking_get_option( $data[ 'id' ] , $data[ 'std' ] );
$custom_settings = $data[ 'value' ];
$id_list_item    = $data[ 'id' ];
?>
<tr class="wpbooking-setting-<?php echo esc_html( $data[ 'id' ] ) ?> wpbooking-setting-list-item ">
    <th scope="row">
        <label for="<?php echo esc_html( $data[ 'id' ] ) ?>"><?php echo esc_html( $data[ 'label' ] ) ?>:</label>
    </th>
    <td class="">
        <table class="wpbooking-list-item-wrap" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th class="td td-left"><span class="dashicons dashicons-admin-tools"></span></th>
                <th class="td td-center"><?php echo esc_html__( 'Title' , 'wp-booking-management-system' ) ?></th>
                <th class="td td-right"><?php echo esc_html__( 'Actions' , 'wp-booking-management-system' ) ?></th>
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
                                <div class="wpbooking-setting-setting-body">
                                    <div class="list-item">
                                        <table class="form-table wpbooking-settings">
                                            <tbody>
                                            <tr class="">
                                                <th scope="row">
                                                    <label for=""><?php echo esc_html__( "Title" , 'wp-booking-management-system' ) ?>:</label>
                                                </th>
                                                <td>
                                                    <input type="text"
                                                           class="form-control  min-width-500 list_item_title"
                                                           value="<?php echo esc_html( $v[ 'title' ] ) ?>"
                                                           name="wpbooking_list_item[<?php echo esc_attr($id_list_item) ?>][<?php echo esc_attr( $i ) ?>][title]"
                                                           placeholder="title">
                                                </td>
                                            </tr>
                                            <?php
                                            if(!empty( $custom_settings )) {
                                                foreach( $custom_settings as $k2 => $v2 ) {
                                                    $id            = $v2[ 'id' ];
                                                    $cusstom_value = "";
                                                    if(!empty($v2['std'])){
                                                        $cusstom_value = $v2['std'];
                                                    }
                                                    if(!empty( $v[ $id ] )) {
                                                        $cusstom_value = $v[ $id ];
                                                    }
                                                    $default = array(
                                                        'id'                => '' ,
                                                        'label'             => '' ,
                                                        'desc'              => '' ,
                                                        'type'              => '' ,
                                                        'std'               => '' ,
                                                        'taxonomy'          => '' ,
                                                        'element_list_item' => true ,
                                                        'custom_name'       => 'wpbooking_list_item[' . $id_list_item . '][' . $i . '][' . $id . ']' ,
                                                        'custom_value'      => $cusstom_value
                                                    );
                                                    $v2      = wp_parse_args( $v2 , $default );
                                                    $path    = 'fields/' . $v2[ 'type' ];
                                                    echo wpbooking_admin_load_view( $path , array(
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
                                <a title="<?php echo esc_html__( 'Edit' , 'wp-booking-management-system' ) ?>"
                                   class="button button-primary btn_list_item_edit" href="javascript:void(0);">
                                    <span class="fa fa-pencil"></span>
                                </a>
                                <a title="<?php echo esc_html__( 'Delete' , 'wp-booking-management-system' ) ?>"
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
                        <div class="wpbooking-setting-setting-body">
                            <div class="list-item">
                                <table class="form-table wpbooking-settings">
                                    <tbody>
                                    <tr class="">
                                        <th scope="row">
                                            <label for=""><?php echo esc_html__( "Title" , 'wp-booking-management-system' ) ?>:</label>
                                        </th>
                                        <td>
                                            <input type="text" class="form-control  min-width-500 list_item_title"
                                                   name="wpbooking_list_item[<?php echo esc_attr($id_list_item) ?>][__number_list__][title]"
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
                                                              'custom_name'       => 'wpbooking_list_item[' . $id_list_item . '][__number_list__][' . $id . ']' ,
                                                              'custom_value'      => ""
                                            );
                                            $v2      = wp_parse_args( $v2 , $default );
                                            $path    = 'fields/' . $v2[ 'type' ];
                                            echo wpbooking_admin_load_view( $path , array( 'data'           => $v2 ,
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
                        <a title="<?php echo esc_html__( 'Edit' , 'wp-booking-management-system' ) ?>"
                           class="button button-primary btn_list_item_edit" href="javascript:void(0);">
                            <span class="fa fa-pencil"></span>
                        </a>
                        <a title="<?php echo esc_html__( 'Delete' , 'wp-booking-management-system' ) ?>"
                           class="button button-secondary light right-item btn_list_item_del"
                           href="javascript:void(0);">
                            <span class="fa fa-trash-o"></span>
                        </a>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
        <input type="hidden" class="wpbooking_number_last_list_item" value="<?php echo esc_html( $i ) ?>">
        <button type="button"
                class="button button-primary btn_add_new_list_item"><?php echo esc_html__( "Add New" , 'wp-booking-management-system' ) ?></button>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
    </td>
</tr>
