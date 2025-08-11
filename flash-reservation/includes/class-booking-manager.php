<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FR_Booking_Manager {
    public static function init() {
        // nothing heavy yet
    }

    public static function create_booking( $data ) {
        // $data must contain: resource_id, start, end, guests, customer (array)
        $resource_id = intval( $data['resource_id'] ?? 0 );
        if ( $resource_id <= 0 ) {
            return new WP_Error( 'invalid_resource', 'Resource invalid', [ 'status' => 400 ] );
        }

        // TODO: implement availability check

        $title = sprintf( 'Booking #%s for resource %d', wp_generate_password( 6, false, false ), $resource_id );
        $post_id = wp_insert_post( [
            'post_type' => 'fr_booking',
            'post_title' => $title,
            'post_status' => 'publish',
        ] );
        if ( is_wp_error( $post_id ) ) return $post_id;

        update_post_meta( $post_id, '_resource_id', $resource_id );
        update_post_meta( $post_id, '_start', sanitize_text_field( $data['start'] ?? '' ) );
        update_post_meta( $post_id, '_end', sanitize_text_field( $data['end'] ?? '' ) );
        update_post_meta( $post_id, '_guests', intval( $data['guests'] ?? 1 ) );
        update_post_meta( $post_id, '_status', 'pending' );
        update_post_meta( $post_id, '_customer', wp_slash( json_encode( $data['customer'] ?? [] ) ) );

        do_action( 'fr_booking_created', $post_id, $data );

        return $post_id;
    }

    public static function check_availability( $resource_id, $start, $end ) {
        // Minimal naive implementation: check bookings that overlap
        $args = [
            'post_type' => 'fr_booking',
            'post_status' => 'publish',
            'meta_query' => [
                [ 'key' => '_resource_id', 'value' => $resource_id, 'compare' => '=' ],
                [ 'relation' => 'AND',
                    [ 'key' => '_start', 'value' => $start, 'compare' => '<=' ],
                    [ 'key' => '_end', 'value' => $end, 'compare' => '>=' ],
                ],
            ],
        ];
        $q = get_posts( $args );
        return empty( $q );
    }
}
