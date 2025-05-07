<?php
    defined( 'ABSPATH' ) or exit();

    $game = $templateArgs['game'];
    $players = $templateArgs['players'];
?>
<div class="fulbito teams list">
<?php if ($game->fecha) : ?>
    <p class="date">
        <?php esc_html_e(date_i18n(get_option('date_format'), strtotime($game->fecha))) ?>
    </p>
<?php endif ?>
<?php if ($game->resultado) : ?>
<table width="100%" class="team_table">
    <thead class="header">
        <tr>
            <?php if( $game->resultado == 1) : ?>
                <th><?php _e('Ganador', 'fulbito') ?></th>
                <th></th>
            <?php elseif( $game->resultado == 2 ) : ?>
                <th></th>
                <th><?php _e('Ganador', 'fulbito') ?></th>
            <?php elseif( $game->resultado == 3 ) : ?>
                <th colspan="2"><?php _e('Empate', 'fulbito') ?></th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="50%">
                <ul class="team_detail">
                <?php foreach ($players as $player): ?>
                    <?php if($player->equipo == 1 && $player->participa ): ?>
                    <li>
                        <?php esc_html_e($player->nombre); ?>
                        <?= ($player->promedio) ? '('.esc_html($player->promedio).')' : ''; ?>
                        <?php if($player->suspendido): ?>
                            <i class="fas fa-ban"></i>
                        <?php endif;?>
                    </li>
                    <?php endif; ?>
                <?php endforeach ?>
                </ul>
            </td>
            <td width="50%">
                <ul class="team_detail">
                    <?php foreach ($players as $player): ?>
                        <?php if($player->equipo == 2 && $player->participa ): ?>
                        <li>
                            <?php esc_html_e($player->nombre); ?>
                            <?= ($player->promedio) ? '('.esc_html($player->promedio).')' : ''; ?>
                            <?php if($player->suspendido): ?>
                                <i class="fas fa-ban"></i>
                            <?php endif;?>
                        </li>
                        <?php endif; ?>
                    <?php endforeach ?>
                </ul>
            </td>
        </tr>
    </tbody>
</table>
<?php else : ?>
    <p class="not_played">
        <?php _e('Este partido a&uacute;n no se jug&oacute;', 'fulbito') ?>
    </p>
<?php endif; ?>
</div>
