<?php
/**
 * Plugin Name: Flash Reservation
 * Plugin URI: https://github.com/FreemanGhost-2025/Flash
 * Description: Plugin de réservation (bus, apparts, vols, événements). Intègre shortcodes et widgets Elementor. Fournit CPTs, endpoints REST, admin calendar et stubs paiement.
 * Version:     0.1.1
 * Author:      Ghost
 * Text Domain: flash-reservation
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * License: GPL2
 * Author URI: https://github.com/FreemanGhost-2025
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/FreemanGhost-2025/Flash
 * GitHub Branch: main
 */

// Sécurité : empêcher accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enregistre les Custom Post Types
 */
function flash_reservation_register_cpts() {

    // Liste des CPTs
    $cpts = [
        'bus' => [
            'singular' => 'Bus',
            'plural'   => 'Réservations Bus',
            'menu_icon'=> 'dashicons-bus', // Dashicons ne contient pas bus, on pourra personnaliser plus tard
        ],
        'appartement' => [
            'singular' => 'Appartement',
            'plural'   => 'Appartements Meublés',
            'menu_icon'=> 'dashicons-admin-home',
        ],
        'vol' => [
            'singular' => 'Vol',
            'plural'   => 'Billets d\'avion',
            'menu_icon'=> 'dashicons-airplane',
        ],
        'evenement' => [
            'singular' => 'Événement',
            'plural'   => 'Événements à venir',
            'menu_icon'=> 'dashicons-calendar-alt',
        ],
    ];

    foreach ( $cpts as $slug => $data ) {

        $labels = [
            'name'               => $data['plural'],
            'singular_name'      => $data['singular'],
            'add_new'            => 'Ajouter',
            'add_new_item'       => 'Ajouter un ' . $data['singular'],
            'edit_item'          => 'Modifier ' . $data['singular'],
            'new_item'           => 'Nouveau ' . $data['singular'],
            'view_item'          => 'Voir ' . $data['singular'],
            'view_items'         => 'Voir ' . $data['plural'],
            'search_items'       => 'Rechercher ' . $data['plural'],
            'not_found'          => 'Aucun ' . strtolower($data['singular']) . ' trouvé',
            'not_found_in_trash' => 'Aucun ' . strtolower($data['singular']) . ' dans la corbeille',
            'all_items'          => 'Tous les ' . $data['plural'],
            'archives'           => 'Archives des ' . $data['plural'],
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'menu_icon'          => $data['menu_icon'],
            'supports'           => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'has_archive'        => true,
            'rewrite'            => ['slug' => $slug],
            'show_in_rest'       => true, // Pour Gutenberg et Elementor
        ];

        register_post_type( $slug, $args );
    }
}
add_action( 'init', 'flash_reservation_register_cpts' );




if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'FR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FR_VERSION', '0.1.0' );

require_once FR_PLUGIN_DIR . 'includes/class-cpt.php';
require_once FR_PLUGIN_DIR . 'includes/class-booking-manager.php';
require_once FR_PLUGIN_DIR . 'includes/class-rest.php';
require_once FR_PLUGIN_DIR . 'includes/admin/class-admin.php';
require_once FR_PLUGIN_DIR . 'includes/payments/class-stripe.php';

class Flash_Reservation_Plugin {
    public static function init() {
        add_action( 'init', [ __CLASS__, 'init_hooks' ] );
        add_action( 'plugins_loaded', [ __CLASS__, 'maybe_register_elementor' ] );
    }

    public static function init_hooks() {
        FR_CPT::register_cpts();
        FR_Booking_Manager::init();
        FR_REST::register_routes();
        FR_Admin::init();

        // shortcode for Elementor / classic builder
        add_shortcode( 'fr_resource', [ __CLASS__, 'shortcode_resource' ] );

        // activation/deactivation hooks are registered below
    }

    public static function shortcode_resource( $atts ) {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'fr_resource' );
        $id = intval( $atts['id'] );
        ob_start();
        $path = FR_PLUGIN_DIR . 'templates/shortcode-resource.php';
        if ( file_exists( $path ) ) {
            include $path;
        } else {
            echo '<div>Resource template not found</div>';
        }
        return ob_get_clean();
    }

    public static function maybe_register_elementor() {
        // Register a very small Elementor widget if Elementor is present
        if ( defined( 'ELEMENTOR_PATH' ) ) {
            add_action( 'elementor/widgets/register', function( $widgets_manager ) {
                require_once FR_PLUGIN_DIR . 'includes/elementor/class-fr-elementor-widget.php';
                $widgets_manager->register( new \FR_Elementor_Widget_Resource() );
            } );
        }
    }
}

register_activation_hook( __FILE__, function() {
    FR_CPT::register_cpts();
    // Create custom tables in future: placeholder
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );

Flash_Reservation_Plugin::init();


