<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FR_CPT {
    public static function register_cpts() {
        // Resources CPT
        $labels = [
            'name' => __( 'Ressources', 'flash-reservation' ),
            'singular_name' => __( 'Ressource', 'flash-reservation' ),
        ];
        register_post_type( 'fr_resource', [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'supports' => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
            'show_in_rest' => true,
            'rewrite' => [ 'slug' => 'resource' ],
        ] );

        // Booking CPT
        $labels2 = [
            'name' => __( 'Réservations', 'flash-reservation' ),
            'singular_name' => __( 'Réservation', 'flash-reservation' ),
        ];
        register_post_type( 'fr_booking', [
            'labels' => $labels2,
            'public' => false,
            'show_ui' => true,
            'supports' => [ 'title' ],
            'show_in_rest' => true,
        ] );
    }
}