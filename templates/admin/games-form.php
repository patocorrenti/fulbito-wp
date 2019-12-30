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
                <input type="date" name="fecha" value="<?php echo $game->fecha ?>"/>
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
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php foreach($players as $player): ?>
                            <p>
                                <label for="<?php echo 'participa_'.$player->id ?>">
                                    <input  id="<?php echo 'participa_'.$player->id ?>"
                                            name="participantes[]"
                                            value="<?php echo $player->id ?>"
                                            type="checkbox"
                                            data-favorito="<?php echo $player->favorito; ?>"
                                            data-nombre="<?php echo $player->nombre; ?>"
                                            data-promedio="<?php echo $player->promedio;?>"
                                            <?php if($player->participa): ?>
                                                checked
                                                data-equipo="<?php echo $player->equipo; // esto es para carga inicial de equipos via JS ?>"
                                            <?php endif; ?>

                                    >
                                    <?php echo($player->nombre); ?>
                                </label>
                            </p>
                        <?php endforeach; ?>
                    </div>
                    <p class="description">
                        <?php _e('Participantes', 'fulbito') ?>:
                        <span id="participantes_total">0</span>
                    </p>
                    <button class="button button-secondary" disabled id="boton_promediar_simple">
                        <?php _e('Mezclar Promediando (algoritmo simple)', 'fulbito') ?>:
                    </button><br>
                    <button class="button button-secondary" disabled id="boton_promediar">
                        <?php _e('Mezclar Promediando (algoritmo pro)', 'fulbito') ?>:
                    </button><br>
                    <button class="button button-secondary" disabled id="boton_mezclar">
                        <?php _e('Mezclar Random', 'fulbito') ?>:
                    </button>
                </fieldset>
            </td>
            <td width="33%" style="vertical-align: top;">
                <h3>
                    <?php _e('Equipos', 'fulbito') ?>
                </h3>
                <label for="resultado">
                    <?php _e('Resultado', 'fulbito') ?>
                </label>
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
                <fieldset id="equipos">
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
            </td>
            <td width="33%" style="vertical-align: top;">
                <h3>
                    <?php _e('Suspendidos', 'fulbito') ?>
                </h3>
                <fieldset id="suspendidos">
                    <?php foreach($players as $player): ?>
                        <?php if($player->participa): ?>
                            <p data-id="<?php echo $player->id; ?>">
                                <label for="suspendido_<?php echo $player->id;?>">
                                    <input  type="checkbox"
                                            name="suspendido[<?php echo $player->id;?>]"
                                            value="<?php echo $player->id; ?>"
                                            id="suspendido_<?php echo $player->id;?>"
                                            <?php if($player->suspendido) echo 'checked'; ?>
                                    >
                                    <?php echo $player->nombre; ?>
                                </label>
                            </p>
                        <?php endif; ?>
                    <?php endforeach;?>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
