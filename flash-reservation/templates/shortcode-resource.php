<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$resource_id = $id ?? 0;
if ( ! $resource_id ) {
    echo '<div>Aucune ressource sélectionnée</div>';
    return;
}
$resource = get_post( $resource_id );
if ( ! $resource ) {
    echo '<div>Ressource introuvable</div>';
    return;
}
$meta = get_post_meta( $resource_id );
?>
<div class="fr-resource">
    <h2><?php echo esc_html( $resource->post_title ); ?></h2>
    <div class="fr-resource-content"><?php echo wp_kses_post( wpautop( $resource->post_content ) ); ?></div>
    <form class="fr-booking-form" method="post" action="<?php echo esc_url( rest_url( 'flashres/v1/bookings' ) ); ?>">
        <input type="hidden" name="resource_id" value="<?php echo esc_attr( $resource_id ); ?>" />
        <label>Date de début: <input type="date" name="start" required /></label>
        <label>Date de fin: <input type="date" name="end" /></label>
        <label>Nombre de personnes: <input type="number" name="guests" value="1" min="1" /></label>
        <label>Nom: <input type="text" name="customer[name]" required /></label>
        <label>Email: <input type="email" name="customer[email]" required /></label>
        <button type="submit">Réserver</button>
    </form>
</div>
