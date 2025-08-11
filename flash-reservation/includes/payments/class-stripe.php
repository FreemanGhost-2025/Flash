<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FR_Stripe {
    public static function handle_webhook( $request ) {
        // Very small stub placeholder. In production you MUST verify signature
        $body = $request->get_body();
        // process and map to booking status
        return rest_ensure_response( [ 'received' => true ] );
    }
}