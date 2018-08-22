<?php
    if ( !function_exists( 'wpbooking_admin_load_view' ) ) {
        function wpbooking_admin_load_view( $view, $data = [] )
        {
            $file = WPBooking()->get_dir( 'shinetheme/views/admin/' . $view . '.php' );

            $file = apply_filters( 'wpbooking_admin_load_view_' . $view, $file, $view, $data );
            $file = apply_filters( 'wpbooking_admin_load_view', $file, $view, $data );

            if ( file_exists( $file ) ) {

                extract( $data );
                ob_start();
                include( $file );

                return @ob_get_clean();
            }
        }
    }
    if ( !function_exists( 'wpbooking_load_view' ) ) {
        function wpbooking_load_view( $view, $data = [] )
        {
            $file = locate_template( [
                'wpbooking/frontend/' . $view . '.php'
            ], false );

            if ( !file_exists( $file ) ) {

                $file = WPBooking()->get_dir( 'shinetheme/views/frontend/' . $view . '.php' );
            }

            $file = apply_filters( 'wpbooking_load_view_' . $view, $file, $view, $data );
            $file = apply_filters( 'wpbooking_load_view', $file, $view, $data );

            if ( file_exists( $file ) ) {

                if ( is_array( $data ) ) {
                    extract( $data );
                }

                ob_start();
                include( $file );
                $html = @ob_get_clean();

                return balanceTags( $html );
            }
        }
    }
    if ( !function_exists( 'wpbooking_view_path' ) ) {
        function wpbooking_view_path( $view )
        {
            // Try to find overided file in theme_name/wpbooking/file-name.php
            $file = locate_template( [
                'wpbooking/' . $view . '.php'
            ], false );

            if ( !file_exists( $file ) ) {

                $file = WPBooking()->get_dir( 'shinetheme/views/frontend/' . $view . '.php' );
            }


            $file = apply_filters( 'wpbooking_load_view_' . $view, $file, $view );

            if ( file_exists( $file ) ) {

                return $file;
            }

            return false;
        }
    }

    if ( !function_exists( 'wpbooking_get_admin_message' ) ) {
        function wpbooking_get_admin_message( $clear_message = true )
        {
            $message = WPBooking()->get_admin_message( $clear_message );

            if ( $message ) {
                $type = $message[ 'type' ];
                switch ( $type ) {
                    case "error":
                        $type = 'error';
                        break;

                    case "success":
                        $type = 'updated';
                        break;
                    default:
                        $type = 'notice-warning';
                        break;
                }

                return sprintf( '<div class="notice %s" >%s</div>', $type, $message[ 'content' ] );
            }

            return false;
        }
    }
    if ( !function_exists( 'wpbooking_get_message' ) ) {
        function wpbooking_get_message( $clear_message = true )
        {
            $message = WPBooking()->get_message( $clear_message );

            if ( $message ) {
                $type = $message[ 'type' ];
                switch ( $type ) {
                    case "error":
                        $type = 'danger';
                        break;

                }

                return sprintf( '<div class="alert alert-%s" >%s</div>', $type, $message[ 'content' ] );
            }

            return false;
        }
    }
    if ( !function_exists( 'wpbooking_set_admin_message' ) ) {
        function wpbooking_set_admin_message( $message, $type = 'information' )
        {
            WPBooking()->set_admin_message( $message, $type );
        }
    }
    if ( !function_exists( 'wpbooking_set_message' ) ) {
        function wpbooking_set_message( $message, $type = 'information' )
        {
            WPBooking()->set_message( $message, $type );
        }
    }

    if ( !function_exists( 'wpbooking_encrypt' ) ) {
        function wpbooking_encrypt( $string = '' )
        {
            return md5( md5( WPBooking_Config::inst()->item( 'encrypt_key' ) ) . md5( $string ) );
        }
    }
    if ( !function_exists( 'wpbooking_encrypt_compare' ) ) {
        function wpbooking_encrypt_compare( $string = '', $encrypt = '' )
        {
            $string = md5( md5( WPBooking_Config::inst()->item( 'encrypt_key' ) ) . md5( $string ) );

            if ( $string == $encrypt ) {
                return true;
            }

            return false;
        }
    }
    if ( !function_exists( 'wpbooking_all_langs' ) ) {
        function wpbooking_all_langs( $not_current = false, $return_all = false )
        {
            $langs = [];
            if ( wpbooking_is_wpml() ) {
                $languages = apply_filters( 'wpml_active_languages', null, [ 'skip_missing' => 0, 'orderby' => 'custom' ] );
                if ( is_array( $languages ) && !empty( $languages ) ) {
                    foreach ( $languages as $lang ) {
                        if ( $not_current && $lang[ 'language_code' ] == wpbooking_current_lang() ) {
                            continue;
                        } else {
                            if ( $return_all ) {
                                $langs[] = $lang;
                            } else {
                                $langs[] = $lang[ 'language_code' ];
                            }
                        }
                    }
                }
            }

            return $langs;
        }
    }

    if ( !function_exists( 'wpbooking_default_lang' ) ) {
        function wpbooking_default_lang()
        {
            $lang = '';
            if ( wpbooking_is_wpml() ) {
                global $sitepress;
                $lang = $sitepress->get_default_language();
            }

            return $lang;
        }
    }
    if ( !function_exists( 'wpbooking_current_lang' ) ) {
        function wpbooking_current_lang()
        {
            $lang = '';
            if ( wpbooking_is_wpml() ) {
                global $sitepress;
                $lang = $sitepress->get_current_language();
            }

            return $lang;
        }
    }
    if ( !function_exists( 'wpbooking_is_wpml' ) ) {
        function wpbooking_is_wpml()
        {
            if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
                return true;
            }

            return false;
        }
    }
    if ( !function_exists( 'wpbooking_switch_lang' ) ) {
        function wpbooking_switch_lang( $lang )
        {
            if ( wpbooking_is_wpml() ) {
                global $sitepress;
                $sitepress->switch_lang( $lang, true );
            }
        }
    }
    if ( !function_exists( 'wpbooking_origin_id' ) ) {
        /**
         * Get Origin Post ID in case of use WPML plugin
         *
         * @since  1.0
         * @author haint
         *
         * @param $post_id
         *
         * @return int|NULL
         */
        function wpbooking_origin_id( $post_id, $service_type = 'post' )
        {
            if ( wpbooking_is_wpml() ) {
                global $sitepress;
                $a = apply_filters( 'wpml_object_id', $post_id, $service_type, true, $sitepress->get_default_language() );

                return $a;
            } else {
                return $post_id;
            }
        }
    }

    if ( !function_exists( 'wpbooking_current_url' ) ) {
        function wpbooking_current_url()
        {
            return esc_url( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) );
        }
    }

    if ( !function_exists( 'wpbooking_post_translated' ) ) {
        function wpbooking_post_translated( $post_id, $service_type = 'post', $lang = '' )
        {
            if ( wpbooking_is_wpml() ) {
                if ( empty( $lang ) ) {
                    $lang = ICL_LANGUAGE_CODE;
                }
                global $sitepress;
                $a = apply_filters( 'wpml_object_id', $post_id, $service_type, true, $lang );

                return $a;
            }

            return $post_id;
        }
    }

    if ( !function_exists( 'wpbooking_is_location' ) ) {
        function wpbooking_is_location( $location_id )
        {
            $location = get_term_by( 'id', $location_id, 'wpbooking_location' );
            if ( !$location ) {
                return false;
            }

            return true;
        }
    }
    if ( !function_exists( 'wpbooking_decode_base64' ) ) {
        /**
         * @param $data
         *
         * @return mixed|string
         * @since   1.0.0
         * @updated 1.0.0
         */
        function wpbooking_decode_base64( $data, $decode = false )
        {
            $data = strtr( $data, '-_,', '+/=' );
            $data = base64_decode( $data );
            if ( $decode ) {
                $data = json_decode( $data );
            }
            $is_serialized = maybe_unserialize( $data );
            if ( !$is_serialized ) {
                return $data;
            } else {
                return $is_serialized;
            }
        }
    }


    if ( !function_exists( 'wpbooking_get_location' ) ) {
        function wpbooking_get_location( $id, $get = 'name' )
        {
            $location = get_term_by( 'id', $id, 'wpbooking_location' );
            $value    = '';
            switch ( $get ) {
                case 'name':
                    $value = $location->name;
                    break;
                case 'slug':
                    $value = $location->slug;
                    break;
            }

            return $value;
        }
    }


    if ( !function_exists( 'wpbooking_show_tree_terms' ) ) {
        function wpbooking_show_tree_terms( array &$terms, array &$returns, $parent = 0, $deep = 0 )
        {
            if ( count( $terms ) == 0 ) {
                return $returns;
            }

            $list_tmp = [];
            foreach ( $terms as $i => $term ) {
                if ( $term->parent == $parent ) {
                    $list_tmp[] = $term;
                    unset( $terms[ $i ] );
                }
            }

            $deep += 15;


            if ( $list_tmp ) {
                foreach ( $list_tmp as $child ) {
                    $returns[] = [
                        'id'          => $child->term_id,
                        'name'        => $child->name,
                        'deep'        => $deep,
                        'parent_name' => wpbooking_get_term( 'term_id', $term->parent, 'wpbooking_location', 'name', $child->name )
                    ];
                    wpbooking_show_tree_terms( $terms, $returns, $child->term_id, $deep );

                }
            }
        }
    }
    if ( !function_exists( 'wpbooking_get_term' ) ) {
        function wpbooking_get_term( $field, $value, $term, $field_return, $default )
        {
            $term = get_term_by( $field, $value, $term );

            if ( !empty( $term ) ) {
                return $term->$field_return;
            }

            return $default;
        }
    }
    if ( !function_exists( 'wpbooking_timestamp_diff_day' ) ) {
        function wpbooking_timestamp_diff_day( $date1, $date2 )
        {
            $total_time = $date2 - $date1;

            $day = floor( $total_time / ( 3600 * 24 ) );

            return $day;
        }
    }
    if ( !function_exists( 'wpbooking_get_translated_string' ) ) {
        function wpbooking_get_translated_string( $string, $name = false )
        {

            if ( !$name ) $name = $string;

            do_action( 'wpml_register_single_string', 'wpbooking', $name, $string );

            return $string;
        }
    }
    if ( !function_exists( 'wpbooking_get_term_meta' ) ) {
        function wpbooking_get_term_meta( $term_id, $meta_key )
        {
            return get_term_meta( $term_id, $meta_key, true );
        }
    }
    if ( !function_exists( 'wpbooking_icon_class_handler' ) ) {
        function wpbooking_icon_class_handler( $class )
        {

            if ( substr( $class, 0, 3 ) == 'fa-' ) $class = 'fa ' . $class;

            return $class;
        }
    }

    if ( !function_exists( 'wpbooking_query' ) ) {
        function wpbooking_query( $query_id = 'default', $arg, $service_type = false )
        {

            do_action( 'wpbooking_before_wb_query_start', $query_id, $service_type );

            WPBooking_Query_Inject::inst()->inject();

            $arg = apply_filters( 'wpbooking_wb_query_arg', $arg, $query_id, $service_type );

            $query = new WP_Query( $arg );

            WPBooking_Query_Inject::inst()->clear();

            do_action( 'wpbooking_after_wb_query_clear', $query_id, $service_type );

            return $query;
        }
    }

    if ( !function_exists( 'wpbooking_date_diff' ) ) {
        function wpbooking_date_diff( $start_timestamp, $end_timestamp )
        {
            $dStart = new DateTime();
            $dStart->setTimestamp( $start_timestamp );
            $dEnd = new DateTime();
            $dEnd->setTimestamp( $end_timestamp );

            return $dStart->diff( $dEnd )->days;
        }
    }


    if ( !function_exists( 'wpbooking_cutnchar' ) ) {
        function wpbooking_cutnchar( $str, $n )
        {
            if ( strlen( $str ) < $n ) return $str;
            $html = substr( $str, 0, $n );
            $html = substr( $html, 0, strrpos( $html, ' ' ) );

            return $html . '...';
        }
    }
    if ( !function_exists( "wpbooking_get_country_list" ) ) {
        function wpbooking_get_country_list()
        {
            $countryList = [
                "AF" => "Afghanistan",
                "AL" => "Albania",
                "DZ" => "Algeria",
                "AS" => "American Samoa",
                "AD" => "Andorra",
                "AO" => "Angola",
                "AI" => "Anguilla",
                "AQ" => "Antarctica",
                "AG" => "Antigua and Barbuda",
                "AR" => "Argentina",
                "AM" => "Armenia",
                "AW" => "Aruba",
                "AU" => "Australia",
                "AT" => "Austria",
                "AZ" => "Azerbaijan",
                "BS" => "Bahamas",
                "BH" => "Bahrain",
                "BD" => "Bangladesh",
                "BB" => "Barbados",
                "BY" => "Belarus",
                "BE" => "Belgium",
                "BZ" => "Belize",
                "BJ" => "Benin",
                "BM" => "Bermuda",
                "BT" => "Bhutan",
                "BO" => "Bolivia",
                "BA" => "Bosnia and Herzegovina",
                "BW" => "Botswana",
                "BV" => "Bouvet Island",
                "BR" => "Brazil",
                "BQ" => "British Antarctic Territory",
                "IO" => "British Indian Ocean Territory",
                "VG" => "British Virgin Islands",
                "BN" => "Brunei",
                "BG" => "Bulgaria",
                "BF" => "Burkina Faso",
                "BI" => "Burundi",
                "KH" => "Cambodia",
                "CM" => "Cameroon",
                "CA" => "Canada",
                "CT" => "Canton and Enderbury Islands",
                "CV" => "Cape Verde",
                "KY" => "Cayman Islands",
                "CF" => "Central African Republic",
                "TD" => "Chad",
                "CL" => "Chile",
                "CN" => "China",
                "CX" => "Christmas Island",
                "CC" => "Cocos [Keeling] Islands",
                "CO" => "Colombia",
                "KM" => "Comoros",
                "CG" => "Congo - Brazzaville",
                "CD" => "Congo - Kinshasa",
                "CK" => "Cook Islands",
                "CR" => "Costa Rica",
                "HR" => "Croatia",
                "CU" => "Cuba",
                "CY" => "Cyprus",
                "CZ" => "Czech Republic",
                "CI" => "Côte d’Ivoire",
                "DK" => "Denmark",
                "DJ" => "Djibouti",
                "DM" => "Dominica",
                "DO" => "Dominican Republic",
                "NQ" => "Dronning Maud Land",
                "DD" => "East Germany",
                "EC" => "Ecuador",
                "EG" => "Egypt",
                "SV" => "El Salvador",
                "GQ" => "Equatorial Guinea",
                "ER" => "Eritrea",
                "EE" => "Estonia",
                "ET" => "Ethiopia",
                "FK" => "Falkland Islands",
                "FO" => "Faroe Islands",
                "FJ" => "Fiji",
                "FI" => "Finland",
                "FR" => "France",
                "GF" => "French Guiana",
                "PF" => "French Polynesia",
                "TF" => "French Southern Territories",
                "FQ" => "French Southern and Antarctic Territories",
                "GA" => "Gabon",
                "GM" => "Gambia",
                "GE" => "Georgia",
                "DE" => "Germany",
                "GH" => "Ghana",
                "GI" => "Gibraltar",
                "GR" => "Greece",
                "GL" => "Greenland",
                "GD" => "Grenada",
                "GP" => "Guadeloupe",
                "GU" => "Guam",
                "GT" => "Guatemala",
                "GG" => "Guernsey",
                "GN" => "Guinea",
                "GW" => "Guinea-Bissau",
                "GY" => "Guyana",
                "HT" => "Haiti",
                "HM" => "Heard Island and McDonald Islands",
                "HN" => "Honduras",
                "HK" => "Hong Kong SAR China",
                "HU" => "Hungary",
                "IS" => "Iceland",
                "IN" => "India",
                "ID" => "Indonesia",
                "IR" => "Iran",
                "IQ" => "Iraq",
                "IE" => "Ireland",
                "IM" => "Isle of Man",
                "IL" => "Israel",
                "IT" => "Italy",
                "JM" => "Jamaica",
                "JP" => "Japan",
                "JE" => "Jersey",
                "JT" => "Johnston Island",
                "JO" => "Jordan",
                "KZ" => "Kazakhstan",
                "KE" => "Kenya",
                "KI" => "Kiribati",
                "KW" => "Kuwait",
                "KG" => "Kyrgyzstan",
                "LA" => "Laos",
                "LV" => "Latvia",
                "LB" => "Lebanon",
                "LS" => "Lesotho",
                "LR" => "Liberia",
                "LY" => "Libya",
                "LI" => "Liechtenstein",
                "LT" => "Lithuania",
                "LU" => "Luxembourg",
                "MO" => "Macau SAR China",
                "MK" => "Macedonia",
                "MG" => "Madagascar",
                "MW" => "Malawi",
                "MY" => "Malaysia",
                "MV" => "Maldives",
                "ML" => "Mali",
                "MT" => "Malta",
                "MH" => "Marshall Islands",
                "MQ" => "Martinique",
                "MR" => "Mauritania",
                "MU" => "Mauritius",
                "YT" => "Mayotte",
                "FX" => "Metropolitan France",
                "MX" => "Mexico",
                "FM" => "Micronesia",
                "MI" => "Midway Islands",
                "MD" => "Moldova",
                "MC" => "Monaco",
                "MN" => "Mongolia",
                "ME" => "Montenegro",
                "MS" => "Montserrat",
                "MA" => "Morocco",
                "MZ" => "Mozambique",
                "MM" => "Myanmar [Burma]",
                "NA" => "Namibia",
                "NR" => "Nauru",
                "NP" => "Nepal",
                "NL" => "Netherlands",
                "AN" => "Netherlands Antilles",
                "NT" => "Neutral Zone",
                "NC" => "New Caledonia",
                "NZ" => "New Zealand",
                "NI" => "Nicaragua",
                "NE" => "Niger",
                "NG" => "Nigeria",
                "NU" => "Niue",
                "NF" => "Norfolk Island",
                "KP" => "North Korea",
                "VD" => "North Vietnam",
                "MP" => "Northern Mariana Islands",
                "NO" => "Norway",
                "OM" => "Oman",
                "PC" => "Pacific Islands Trust Territory",
                "PK" => "Pakistan",
                "PW" => "Palau",
                "PS" => "Palestinian Territories",
                "PA" => "Panama",
                "PZ" => "Panama Canal Zone",
                "PG" => "Papua New Guinea",
                "PY" => "Paraguay",
                "YD" => "People's Democratic Republic of Yemen",
                "PE" => "Peru",
                "PH" => "Philippines",
                "PN" => "Pitcairn Islands",
                "PL" => "Poland",
                "PT" => "Portugal",
                "PR" => "Puerto Rico",
                "QA" => "Qatar",
                "RO" => "Romania",
                "RU" => "Russia",
                "RW" => "Rwanda",
                "RE" => "Réunion",
                "BL" => "Saint Barthélemy",
                "SH" => "Saint Helena",
                "KN" => "Saint Kitts and Nevis",
                "LC" => "Saint Lucia",
                "MF" => "Saint Martin",
                "PM" => "Saint Pierre and Miquelon",
                "VC" => "Saint Vincent and the Grenadines",
                "WS" => "Samoa",
                "SM" => "San Marino",
                "SA" => "Saudi Arabia",
                "SN" => "Senegal",
                "RS" => "Serbia",
                "CS" => "Serbia and Montenegro",
                "SC" => "Seychelles",
                "SL" => "Sierra Leone",
                "SG" => "Singapore",
                "SK" => "Slovakia",
                "SI" => "Slovenia",
                "SB" => "Solomon Islands",
                "SO" => "Somalia",
                "ZA" => "South Africa",
                "GS" => "South Georgia and the South Sandwich Islands",
                "KR" => "South Korea",
                "ES" => "Spain",
                "LK" => "Sri Lanka",
                "SD" => "Sudan",
                "SR" => "Suriname",
                "SJ" => "Svalbard and Jan Mayen",
                "SZ" => "Swaziland",
                "SE" => "Sweden",
                "CH" => "Switzerland",
                "SY" => "Syria",
                "ST" => "São Tomé and Príncipe",
                "TW" => "Taiwan",
                "TJ" => "Tajikistan",
                "TZ" => "Tanzania",
                "TH" => "Thailand",
                "TL" => "Timor-Leste",
                "TG" => "Togo",
                "TK" => "Tokelau",
                "TO" => "Tonga",
                "TT" => "Trinidad and Tobago",
                "TN" => "Tunisia",
                "TR" => "Turkey",
                "TM" => "Turkmenistan",
                "TC" => "Turks and Caicos Islands",
                "TV" => "Tuvalu",
                "UM" => "U.S. Minor Outlying Islands",
                "PU" => "U.S. Miscellaneous Pacific Islands",
                "VI" => "U.S. Virgin Islands",
                "UG" => "Uganda",
                "UA" => "Ukraine",
                "SU" => "Union of Soviet Socialist Republics",
                "AE" => "United Arab Emirates",
                "GB" => "United Kingdom",
                "US" => "United States",
                "ZZ" => "Unknown or Invalid Region",
                "UY" => "Uruguay",
                "UZ" => "Uzbekistan",
                "VU" => "Vanuatu",
                "VA" => "Vatican City",
                "VE" => "Venezuela",
                "VN" => "Vietnam",
                "WK" => "Wake Island",
                "WF" => "Wallis and Futuna",
                "EH" => "Western Sahara",
                "YE" => "Yemen",
                "ZM" => "Zambia",
                "ZW" => "Zimbabwe",
                "AX" => "Åland Islands",
            ];

            return $countryList;
        }
    }

    if ( !function_exists( 'wpbooking_is_any_register' ) ) {
        function wpbooking_is_any_register()
        {
            $can_register = get_option( 'users_can_register' );
            if ( $can_register == '1' ) {
                return true;
            }

            return false;
        }
    }

    if ( !function_exists( 'wpbooking_months_dropdown' ) ) {
        function wpbooking_get_months_dropdown_html( $post_type )
        {
            global $wpdb, $wp_locale;

            $extra_checks = "AND post_status != 'auto-draft'";
            if ( !isset( $_GET[ 'post_status' ] ) || 'cancel' !== $_GET[ 'post_status' ] ) {
                $extra_checks .= " AND post_status != 'cancel'";
            } elseif ( isset( $_GET[ 'post_status' ] ) ) {
                $extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET[ 'post_status' ] );
            }

            $months = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
			FROM $wpdb->posts
			WHERE post_type = %s
			$extra_checks
			ORDER BY post_date DESC
		", $post_type ) );

            $month_count = count( $months );

            if ( !$month_count || ( 1 == $month_count && 0 == $months[ 0 ]->month ) )
                return;

            $m = isset( $_GET[ 'm' ] ) ? (int)$_GET[ 'm' ] : 0;
            ?>
            <select name="m" id="filter-by-date">
                <option<?php selected( $m, 0 ); ?> value=""><?php echo esc_html__( 'All dates', 'wp-booking-management-system' ); ?></option>
                <?php
                    foreach ( $months as $arc_row ) {
                        if ( 0 == $arc_row->year )
                            continue;

                        $month = zeroise( $arc_row->month, 2 );
                        $year  = $arc_row->year;

                        printf( "<option %s value='%s'>%s</option>\n", selected( $m, $year . $month, false ), esc_attr( $arc_row->year . $month ), sprintf( esc_html__( '%1$s %2$d', 'wp-booking-management-system' ), $wp_locale->get_month( $month ), $year ) );
                    }
                ?>
            </select>
            <?php
        }
    }

    if ( !function_exists( 'wpbooking_covert_to_one' ) ) {
        function wpbooking_covert_to_one( $res )
        {
            if ( $res == 0 ) return (int)$res + 1;

            return $res;
        }
    }

    if ( !function_exists( 'wpbooking_get_layout_archive' ) ) {
        function wpbooking_get_layout_archive()
        {
            $default = 'list';
            $default = apply_filters( 'wpbooking_default_layout_archive', $default );
            $layout  = WPBooking_Input::get( 'layout', $default );

            return $layout;
        }
    }
    if ( !function_exists( 'wpbooking_get_date_format_js' ) ) {
        function wpbooking_get_date_format_js()
        {
            $format = get_option( 'date_format', 'F j, Y' );
            $format = str_replace( 'F', 'MM', $format );
            $format = str_replace( 'd', 'DD', $format );
            $format = str_replace( 'j', 'D', $format );
            $format = str_replace( 'S', 'D', $format );
            $format = str_replace( 'm', 'MM', $format );
            $format = str_replace( 'M', 'M', $format );
            $format = str_replace( 'Y', 'YYYY', $format );
            $format = str_replace( 'y', 'YYYY', $format );

            return $format;
        }
    }
    if ( !function_exists( 'wpbooking_date_format' ) ) {
        /**
         * @return mixed|void
         */
        function wpbooking_date_format()
        {
            $format = get_option( 'date_format', 'F j, Y' );
            $format = str_replace( 'F', 'm', $format );
            $format = str_replace( 'S', 'd', $format );
            $format = str_replace( 'n', 'M', $format );
            $format = str_replace( 'M', 'm', $format );
            $format = str_replace( 'y', 'Y', $format );

            return $format;
        }
    }