<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( class_exists( '\Elementor\Widget_Base' ) ) {
    class FR_Elementor_Widget_Resource extends \Elementor\Widget_Base {
        public function get_name() { return 'fr_resource'; }
        public function get_title() { return 'FR Resource (Flash Reservation)'; }
        public function get_icon() { return 'eicon-calendar'; }
        public function get_categories() { return [ 'general' ]; }
        protected function register_controls() {
            $this->start_controls_section( 'content_section', [ 'label' => __( 'Settings', 'flash-reservation' ) ] );
            $this->add_control( 'resource_id', [ 'label' => __( 'Resource ID', 'flash-reservation' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
            $this->end_controls_section();
        }
        protected function render() {
            $settings = $this->get_settings_for_display();
            echo do_shortcode( '[fr_resource id="' . esc_attr( $settings['resource_id'] ) . '"]' );
        }
    }
}