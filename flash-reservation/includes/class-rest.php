<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FR_REST {
    public static function register_routes() {
        register_rest_route( 'flashres/v1', '/resources', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ __CLASS__, 'rest_get_resources' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( 'flashres/v1', '/resources/(?P<id>\d+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ __CLASS__, 'rest_get_resource' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( 'flashres/v1', '/bookings', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ __CLASS__, 'rest_create_booking' ],
            'permission_callback' => '__return_true',
        ] );

        // webhook for stripe (example)
        register_rest_route( 'flashres/v1', '/webhook/stripe', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ 'FR_Stripe', 'handle_webhook' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public static function rest_get_resources( $request ) {
        $args = [ 'post_type' => 'fr_resource', 'posts_per_page' => 20 ];
        $posts = get_posts( $args );
        $data = [];
        foreach ( $posts as $p ) {
            $data[] = [
                'id' => $p->ID,
                'title' => $p->post_title,
                'content' => $p->post_content,
                'meta' => get_post_meta( $p->ID ),
            ];
        }
        return rest_ensure_response( $data );
    }

    public static function rest_get_resource( $request ) {
        $id = intval( $request['id'] );
        $p = get_post( $id );
        if ( ! $p || $p->post_type !== 'fr_resource' ) return new WP_Error( 'not_found', 'Ressource non trouvée', [ 'status' => 404 ] );
        $data = [
            'id' => $p->ID,
            'title' => $p->post_title,
            'content' => $p->post_content,
            'meta' => get_post_meta( $p->ID ),
        ];
        return rest_ensure_response( $data );
    }

    public static function rest_create_booking( $request ) {
        $params = $request->get_json_params();
        // Basic validation
        if ( empty( $params['resource_id'] ) || empty( $params['start'] ) ) {
            return new WP_Error( 'invalid_data', 'Données incomplètes', [ 'status' => 400 ] );
        }

        $available = FR_Booking_Manager::check_availability( intval( $params['resource_id'] ), sanitize_text_field( $params['start'] ), sanitize_text_field( $params['end'] ?? $params['start'] ) );
        if ( ! $available ) return new WP_Error( 'unavailable', 'Ressource non disponible', [ 'status' => 409 ] );

        $booking_id = FR_Booking_Manager::create_booking( $params );
        if ( is_wp_error( $booking_id ) ) return $booking_id;

        return rest_ensure_response( [ 'booking_id' => $booking_id ] );
    }
}