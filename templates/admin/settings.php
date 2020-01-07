<?php
    defined( 'ABSPATH' ) or exit();
?>
<div class="wrap">
    <h1><?php _e('Ajustes', 'fulbito') ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'fulbito_settings' ); ?>
        <?php do_settings_sections( 'fulbito_settings' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('API') ?></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span><?php _e('Activar API', 'fulbito') ?></span>
                        </legend>
                        <label for="enable_api">
                            <input
                                type="checkbox"
                                id="enable_api"
                                name="enable_api"
                                <?php echo (get_option('enable_api') === 'on') ? 'checked' : '' ?>
                            >
                            <?php _e('Activar API', 'fulbito') ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    <hr>
    <h3><?php _e('Funciones beta', 'fulbito') ?></h3>
    <form method="post" action="">
        <input type="hidden" name="ft_action" value="ft_regenerar_tabla" />
        <p class="description">
            <?php _e('Utilizar esto cuando se cambia el resultado de un partido viejo', 'fulbito') ?>
        </p>
        <?php wp_nonce_field( 'ft_regenerar_tabla'); ?>
        <?php submit_button( __('Regenerar tabla de posiciones', 'fulbito') ); ?>
    </form>
</div>
