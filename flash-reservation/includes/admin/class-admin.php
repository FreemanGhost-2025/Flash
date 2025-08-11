<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FR_Admin {
    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_assets' ] );
    }

    public static function admin_menu() {
        add_menu_page( 'Flash Reservation', 'Flash Reservation', 'manage_options', 'fr-reservation', [ __CLASS__, 'admin_page' ], 'dashicons-calendar-alt', 56 );
    }

    public static function admin_assets( $hook ) {
        // enqueue only on our plugin page
        if ( strpos( $hook, 'fr-reservation' ) === false ) return;
        wp_enqueue_script( 'fr-admin-calendar', FR_PLUGIN_URL . 'assets/js/admin-calendar.js', [ 'jquery' ], FR_VERSION, true );
        wp_enqueue_style( 'fr-admin-css', FR_PLUGIN_URL . 'assets/css/admin.css', [], FR_VERSION );
    }

    public static function admin_page() {
        ?>
        <div class="wrap">
            <h1>Calendrier des réservations</h1>
            <div id="fr-calendar">Loading calendar...</div>
        </div>
        <?php
    }
}