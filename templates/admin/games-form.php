<?php
    defined( 'ABSPATH' ) or exit();

    $game = $templateArgs['game'];
    $players = $templateArgs['players'];
?>
<table id="partido_fields" width="100%">
    <tbody>
        <tr>
            <td colspan="3">
                <h3>
                    <?php _e('Fecha', 'fulbito') ?>
                </h3>
                <input type="date" name="fecha" value="<?= esc_attr($game->fecha) ?>"/>
            </td>
        </tr>
        <tr>
            <td width="33%" style="vertical-align: top;">
                <input type="hidden" name="testing" value="4">
                <h3>
                    <?php _e('Jugadores', 'fulbito') ?>
                </h3>
                <fieldset id="participantes">
                    <button class="button button-secondary" id="seleccionar_favoritos">
                        <?php _e('Seleccionar favoritos', 'fulbito') ?>
                    </button>
                    <div style="max-height: 550px; overflow-y: auto;">
                        <?php foreach($players as $player): ?>
                            <p>
                                <label for="<?= esc_attr('participa_'.$player->id) ?>">
                                    <input 
                                        id="<?= esc_attr('participa_'.$player->id) ?>"
                                        name="participantes[]"
                                        value="<?= esc_attr($player->id) ?>"
                                        type="checkbox"
                                        data-favorito="<?= esc_attr($player->favorito) ?>"
                                        data-nombre="<?= esc_attr($player->nombre) ?>"
                                        data-promedio="<?= esc_attr($player->promedio) ?>"
                                        <?php if($player->participa): ?>
                                            checked
                                            data-equipo="<?= esc_attr($player->equipo) //Initial team load via JS ?>"
                                        <?php endif; ?>
                                    >
                                    <?= esc_html($player->nombre) ?>
                                </label>
                            </p>
                        <?php endforeach; ?>
                    </div>
                    <p class="description">
                        <?php _e('Participantes', 'fulbito') ?>:
                        <span id="participantes_total">0</span>
                    </p>
                </fieldset>
            </td>
            <td width="33%" style="vertical-align: top;">
                <fieldset id="equipos">
                    <h3>
                        <?php _e('Equipos', 'fulbito') ?>
                    </h3>
                    <h2>
                        <?php _e('Equipo A (Blanco)', 'fulbito') ?>
                        <ol id="equipo_a_combo">
                            <li><select name="jugadoresEquipoA[]" disabled></select></li>
                            <li><select name="jugadoresEquipoA[]" disabled></select></li>
                            <li><select name="jugadoresEquipoA[]" disabled></select></li>
                            <li><select name="jugadoresEquipoA[]" disabled></select></li>
                            <li><select name="jugadoresEquipoA[]" disabled></select></li>
                        </ol>
                    </h2>
                    <h2>
                        <?php _e('Equipo B (Coco)', 'fulbito') ?>
                        <ol id="equipo_b_combo">
                            <li><select name="jugadoresEquipoB[]" disabled></select></li>
                            <li><select name="jugadoresEquipoB[]" disabled></select></li>
                            <li><select name="jugadoresEquipoB[]" disabled></select></li>
                            <li><select name="jugadoresEquipoB[]" disabled></select></li>
                            <li><select name="jugadoresEquipoB[]" disabled></select></li>
                        </ol>
                    </h2>
                </fieldset>
                <fieldset>
                    <h3>
                        <?php _e('Autoarmado', 'fulbito') ?>
                    </h3>
                    <button class="button button-primary" disabled id="boton_promediar" style="margin-bottom: 5px;">
                        <?php _e('Promediando (RECOMENDADO)', 'fulbito') ?>
                    </button><br>
                    <button class="button button-secondary" disabled id="boton_promediar_simple" style="margin-bottom: 5px;">
                        <?php _e('Promediando (algoritmo simple)', 'fulbito') ?>
                    </button><br>
                    <button class="button button-secondary" disabled id="boton_mezclar" style="margin-bottom: 5px;">
                        <?php _e('Random', 'fulbito') ?>
                    </button>
                </fieldset>
            </td>
            <td width="33%" style="vertical-align: top;">
                <fieldset>
                    <h3>
                        <?php _e('Resultado', 'fulbito') ?>
                    </h3>
                    <select name="resultado" id="resultado">
                        <option value="0" <?php if($game->resultado == 0) echo 'selected'; ?> >
                            <?php _e('Sin jugar', 'fulbito') ?>
                        </option>
                        <option value="3" <?php if($game->resultado == 3) echo 'selected'; ?> >
                            <?php _e('Empate', 'fulbito') ?>
                        </option>
                        <option value="1" <?php if($game->resultado == 1) echo 'selected'; ?> >
                            <?php _e('Gan&oacute; Equipo A (Blanco)', 'fulbito') ?>
                        </option>
                        <option value="2" <?php if($game->resultado == 2) echo 'selected'; ?> >
                            <?php _e('Gan&oacute; Equipo B (Coco)', 'fulbito') ?>
                        </option>
                    </select>
                </fieldset>
                <h3>
                    <?php _e('Suspendidos', 'fulbito') ?>
                </h3>
                <fieldset id="suspendidos">
                    <?php foreach($players as $player): ?>
                        <?php if($player->participa): ?>
                            <p data-id="<?= esc_attr($player->id) ?>">
                                <label for="suspendido_<?= esc_attr($player->id) ?>">
                                    <input 
                                        type="checkbox"
                                        name="suspendido[<?= esc_attr($player->id) ?>]"
                                        value="<?= esc_attr($player->id) ?>"
                                        id="suspendido_<?= esc_attr($player->id) ?>"
                                        <?php if($player->suspendido) echo 'checked' ?>
                                    >
                                    <?= esc_html($player->nombre) ?>
                                </label>
                            </p>
                        <?php endif; ?>
                    <?php endforeach;?>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
 <?php wp_nonce_field( 'ft_game_metadata', 'ftnonce'); ?>
