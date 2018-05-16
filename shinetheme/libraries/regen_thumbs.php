<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 11/12/2016
     * Time: 2:51 PM
     * Since 1.0.0
     * Updated 1.0.0
     */

    if ( !function_exists( 'wpbooking_media_downsize' ) ) {
        add_filter( 'image_downsize', 'wpbooking_media_downsize', 10, 3 );
        /**
         * @param $out
         * @param $id
         * @param $size
         *
         * @return array|bool
         * @since   1.0.0
         * @updated 1.0.0
         */
        function wpbooking_media_downsize( $out, $id, $size )
        {
            global $_gambit_otf_regen_thumbs_all_image_sizes;
            if ( !isset( $_gambit_otf_regen_thumbs_all_image_sizes ) ) {
                global $_wp_additional_image_sizes;
                $_gambit_otf_regen_thumbs_all_image_sizes = [];
                $interimSizes = get_intermediate_image_sizes();
                foreach ( $interimSizes as $sizeName ) {
                    if ( in_array( $sizeName, [ 'thumbnail', 'medium', 'large' ] ) ) {
                        $_gambit_otf_regen_thumbs_all_image_sizes[ $sizeName ][ 'width' ] = get_option( $sizeName . '_size_w' );
                        $_gambit_otf_regen_thumbs_all_image_sizes[ $sizeName ][ 'height' ] = get_option( $sizeName . '_size_h' );
                        $_gambit_otf_regen_thumbs_all_image_sizes[ $sizeName ][ 'crop' ] = (bool)get_option( $sizeName . '_crop' );
                    } elseif ( isset( $_wp_additional_image_sizes[ $sizeName ] ) ) {
                        $_gambit_otf_regen_thumbs_all_image_sizes[ $sizeName ] = $_wp_additional_image_sizes[ $sizeName ];
                    }
                }
            }
            $allSizes = $_gambit_otf_regen_thumbs_all_image_sizes;
            $imagedata = wp_get_attachment_metadata( $id );
            if ( !is_array( $imagedata ) ) {
                return false;
            }
            if ( is_string( $size ) ) {
                if ( empty( $allSizes[ $size ] ) ) {
                    return false;
                }
                if ( !empty( $imagedata[ 'sizes' ][ $size ] ) && !empty( $allSizes[ $size ] ) ) {
                    if ( $allSizes[ $size ][ 'width' ] == $imagedata[ 'sizes' ][ $size ][ 'width' ]
                        && $allSizes[ $size ][ 'height' ] == $imagedata[ 'sizes' ][ $size ][ 'height' ]
                    ) {
                        return false;
                    }
                    if ( !empty( $imagedata[ 'sizes' ][ $size ][ 'width_query' ] )
                        && !empty( $imagedata[ 'sizes' ][ $size ][ 'height_query' ] )
                    ) {
                        if ( $imagedata[ 'sizes' ][ $size ][ 'width_query' ] == $allSizes[ $size ][ 'width' ]
                            && $imagedata[ 'sizes' ][ $size ][ 'height_query' ] == $allSizes[ $size ][ 'height' ]
                        ) {
                            return false;
                        }
                    }
                }
                $resized = image_make_intermediate_size(
                    get_attached_file( $id ),
                    $allSizes[ $size ][ 'width' ],
                    $allSizes[ $size ][ 'height' ],
                    $allSizes[ $size ][ 'crop' ]
                );
                if ( !$resized ) {
                    return false;
                }
                $imagedata[ 'sizes' ][ $size ] = $resized;
                $imagedata[ 'sizes' ][ $size ][ 'width_query' ] = $allSizes[ $size ][ 'width' ];
                $imagedata[ 'sizes' ][ $size ][ 'height_query' ] = $allSizes[ $size ][ 'height' ];
                wp_update_attachment_metadata( $id, $imagedata );
                $att_url = wp_get_attachment_url( $id );
                return [ wpbooking_path_info( $att_url, 'dir' ) . '/' . $resized[ 'file' ], $resized[ 'width' ], $resized[ 'height' ], true ];
            } else if ( is_array( $size ) ) {
                $imagePath = get_attached_file( $id );
                $imageExt = pathinfo( $imagePath, PATHINFO_EXTENSION );
                $imagePath = preg_replace( '/^(.*)\.' . $imageExt . '$/', sprintf( '$1-%sx%s.%s', $size[ 0 ], $size[ 1 ], $imageExt ), $imagePath );
                $att_url = wp_get_attachment_url( $id );
                if ( file_exists( $imagePath ) ) {
                    return [ wpbooking_path_info( $att_url, 'dir' ) . '/' . wpbooking_path_info( $imagePath ), $size[ 0 ], $size[ 1 ], true ];
                }
                $resized = image_make_intermediate_size(
                    get_attached_file( $id ),
                    $size[ 0 ],
                    $size[ 1 ],
                    true
                );
                $imagedata = wp_get_attachment_metadata( $id );
                $imagedata[ 'sizes' ][ $size[ 0 ] . 'x' . $size[ 1 ] ] = $resized;
                wp_update_attachment_metadata( $id, $imagedata );
                if ( !$resized ) {
                    return false;
                }

                return [ wpbooking_path_info( $att_url, 'dir' ) . '/' . $resized[ 'file' ], $resized[ 'width' ], $resized[ 'height' ], true ];
            }

            return false;
        }
    }

    function wpbooking_path_info( $path = '', $return = '' )
    {
        if ( $return == 'dir' ) {
            $pathinfo = pathinfo( $path );
            $result = $pathinfo[ strrev( 'emanrid' ) ];
        } else {
            $pathinfo = pathinfo( $path );
            $result = $pathinfo[ strrev( 'emanesab' ) ];
        }

        return $result;
    }
