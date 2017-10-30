<?php
    if ( !class_exists( 'WPBooking_Comment_Model' ) && class_exists( 'WPBooking_Model' ) ) {
        class WPBooking_Comment_Model extends WPBooking_Model
        {
            static $_inst;

            function __construct()
            {
                $this->table_name          = 'comments';
                $this->ignore_create_table = true;
                parent::__construct();
            }


            /**
             * Get Avg Rate for Specific Post
             *
             * @since  1.0
             * @author dungdt
             *
             * @param bool|FALSE $post_id
             *
             * @return bool
             */
            function get_avg_review( $post_id = false )
            {
                global $wpdb;
                if ( !$post_id ) return false;

                $row = $this->select( 'avg(' . $wpdb->commentmeta . '.meta_value) as avg_rate' )
                    ->join( 'commentmeta', 'commentmeta.comment_id=comments.comment_ID' )
                    ->where( $wpdb->commentmeta . '.meta_key', 'wb_review' )
                    ->where( 'comment_post_ID', $post_id )
                    ->where( 'comment_approved', 1 )
                    ->get()->row();

                return !empty( $row[ 'avg_rate' ] ) ? $row[ 'avg_rate' ] : false;
            }

            /**
             * Get Number of Reply for a Review
             *
             * @author dungdt
             * @since  1.0
             *
             * @param $review_id
             *
             * @return mixed
             */
            function count_child( $review_id )
            {

                $res = $this->select( 'count(comment_ID) as total' )->where( 'comment_parent', $review_id )->get( 1 )->row();

                return $res[ 'total' ];
            }

            function count_parent( $post_id = false )
            {

                if ( !$post_id ) return false;

                $row = $this->select( 'count(comment_ID) as total' )
                    ->where( [
                        'comment_post_ID'  => $post_id,
                        'comment_parent'   => 0,
                        'comment_approved' => 1
                    ] )->get()->row();

                return !empty( $row[ 'total' ] ) ? $row[ 'total' ] : false;
            }

            static function inst()
            {
                if ( !self::$_inst ) self::$_inst = new self();

                return self::$_inst;
            }


        }

        WPBooking_Comment_Model::inst();

    }