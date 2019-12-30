<?php
    defined( 'ABSPATH' ) or exit();

    $players = $templateArgs['players'];
?>
<div class="wrap">
    <h1><?php _e('Configuraci&oacute;n', 'fulbito') ?></h1>
    <hr>
    <h3>
        <?php _e('Jugadores', 'fulbito') ?>
        <a href="#" class="page-title-action">
            <?php _e('A&ntilde;adir nuevo', 'fulbito') ?>
        </a>
    </h3>
    <form method="post" action="">
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th scope="col">
                        <?php _e('ID', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Nombre', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Email', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Nacimiento', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Favorito', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Lesi&oacute;n', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Activo', 'fulbito') ?>
                    </th>
                </tr>
            </thead>
            <tbody id="tabla_jugadores">
                <?php foreach($players as $player): ?>
                    <tr class="editar_fila">
                        <td>
                            <span>
                                <?php echo $player->id ?>
                            </span>
                        </td>
                        <td class="editable">
                            <input
                                type="text"
                                name="jugadores[<?php echo $player->id; ?>][nombre]"
                                value="<?php echo($player->nombre); ?>"
                                required
                            />
                        </td>
                        <td class="editable">
                            <input
                                type="email"
                                name="jugadores[<?php echo $player->id; ?>][email]"
                                value="<?php echo($player->email); ?>"
                            />
                        </td>
                        <td class="editable">
                            <input
                                type="date"
                                name="jugadores[<?php echo $player->id; ?>][nacimiento]"
                                value="<?php echo($player->nacimiento); ?>"
                            />
                        </td>
                        <td class="editable">
                            <select name="jugadores[<?php echo $player->id; ?>][favorito]">
                                <option value="1" <?php if($player->favorito) echo 'selected'; ?> >
                                    <?php _e('Si', 'fulbito') ?>
                                </option>
                                <option value="0" <?php if(!$player->favorito) echo 'selected'; ?> >
                                    <?php _e('No', 'fulbito') ?>
                                </option>
                            </select>
                        </td>
                        <td class="editable">
                            <select name="jugadores[<?php echo $player->id; ?>][lesion]">
                                <option value="1" <?php if($player->lesion) echo 'selected'; ?> >
                                    <?php _e('Si', 'fulbito') ?>
                                </option>
                                <option value="0" <?php if(!$player->lesion) echo 'selected'; ?> >
                                    <?php _e('No', 'fulbito') ?>
                                </option>
                            </select>
                        </td>
                        <td class="editable">
                            <select name="jugadores[<?php echo $player->id; ?>][activo]">
                                <option value="1" <?php if($player->activo) echo 'selected'; ?>>
                                    <?php _e('Si', 'fulbito') ?>
                                </option>
                                <option value="0" <?php if(!$player->activo) echo 'selected'; ?>>
                                    <?php _e('No', 'fulbito') ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <input type="hidden" name="ft_action" value="ft_edit_players" />
        <?php wp_nonce_field( 'ft_edit_players'); ?>
        <?php submit_button( __('Guardar cambios', 'fulbito') ); ?>
    </form>
    <h3><?php _e('Tabla de posiciones', 'fulbito') ?></h3>
    <form method="post" action="">
        <p class="description">
            (<?php _e('Utilizar esto cuando se cambia el resultado de un partido viejo', 'fulbito') ?>)
        </p>
        <input type="hidden" name="ft_action" value="ft_regenerar_tabla" />
        <?php wp_nonce_field( 'ft_regenerar_tabla'); ?>
        <?php submit_button( __('Regenerar tabla de posiciones', 'fulbito') ); ?>
    </form>
</div>
