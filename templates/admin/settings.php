<?php
    defined( 'ABSPATH' ) or exit();
?>
<div class="wrap">
    <h1><?php _e('Opciones', 'fulbito') ?></h1>
    <hr>
    <h3><?php _e('Tabla de posiciones', 'fulbito') ?></h3>
    <form method="post" action="">
        <p class="description">
            <?php _e('Funci&oacute;n beta', 'fulbito') ?>.<br>
            (<?php _e('Utilizar esto cuando se cambia el resultado de un partido viejo', 'fulbito') ?>)
        </p>
        <input type="hidden" name="ft_action" value="ft_regenerar_tabla" />
        <?php wp_nonce_field( 'ft_regenerar_tabla'); ?>
        <?php submit_button( __('Regenerar tabla de posiciones', 'fulbito') ); ?>
    </form>
</div>
