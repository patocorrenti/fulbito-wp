<?php
    defined( 'ABSPATH' ) or exit();

    $game_query = $templateArgs['game_query'];
    $players = $templateArgs['players'];
    $game = $templateArgs['game'];
?>
<?php
// Totals
$registered = 0;
$onTeam  = 0;
if( is_array( $players ) ){
    foreach($players as $player){
        if( $player->participa) $registered++;
        if( $player->equipo ) $onTeam++;
    }
}
?>
<div class="fulbito subscription">
<?php while( $game_query->have_posts() ): $game_query->the_post();?>

    <!-- DATE -->
    <?php if ($game->fecha) : ?>
        <h2 class="date">
            <?php esc_html_e(date_i18n(get_option('date_format'), strtotime($game->fecha))) ?>
        </h2>
    <?php endif ?>

    <!-- FORM -->
    <?php if( $registered < 10 ): ?>
        <form action="" method="post" class="subscription-form">
            <input type="hidden" name="validancia" value="">
            <input type="hidden" name="partido" value="<?php esc_attr_e(get_the_ID()); ?>" >
            <label for="jugador">Jugador:</label>
            <select name="jugador" id="jugador">
                <?php foreach( $players as $player ): if( !$player->participa ): ?>
                    <option value="<?php esc_attr_e($player->id); ?>">
                        <?php esc_html_e($player->nombre); ?>
                    </option>
                <?php endif; endforeach; ?>
            </select>
            <br>
            <?php wp_nonce_field( 'ft_subscribe_player') ?>
            <input type="submit" name="inscribir" value="<?php esc_attr_e('Inscribir', 'fulbito') ?>">
        </form>
    <?php else: ?>
        <p class="closed">
            <?php esc_html_e('La inscripci&oacute;n cerr&oacute;, te quedaste afuera.', 'fulbito') ?>
        </p>
    <?php endif;?>

    <!-- COMPLETED -->
    <?php if( $registered === 10 ):?>
        <?php if( $onTeam === 10 ): ?>
            <h4 class="teams-title"><?php esc_html_e('Equipos', 'fulbito') ?></h4>
            <table class="team_table">
                <thead class="header">
                    <tr>
                        <th width="50%">
                            <?php esc_html_e('Equipo A (Blanco)', 'fulbito') ?>
                        </th>
                        <th width="50%">
                            <?php esc_html_e('Equipo B (Coco)', 'fulbito') ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php $promediosEquipo = array();?>
                            <ul class="team_detail">
                                <?php foreach ($players as $player): ?>
                                    <?php if($player->equipo == 1 && $player->participa ): ?>
                                        <?php $promediosEquipo[0][] = $player->promedio; ?>
                                        <li>
                                            <?php esc_html_e($player->nombre); ?>
                                            (<?php esc_html_e(($player->promedio) ? $player->promedio : 'ns') ;?>)
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach ?>
                            </ul>
                            <?php
                            $prom_equipo = 0;
                            $promediosEquipo[0] = array_filter($promediosEquipo[0]);
                            if( count($promediosEquipo[0]) ):
                                $prom_equipo = array_sum($promediosEquipo[0])/count($promediosEquipo[0]);
                            ?>
                                <p class="team_average">
                                    <?php esc_html_e(number_format((float)$prom_equipo, 2, '.', '')); ?>
                                </p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <ul class="team_detail">
                                <?php foreach ($players as $player):  ?>
                                    <?php if($player->equipo == 2 && $player->participa ): ?>
                                        <?php $promediosEquipo[1][] = $player->promedio; ?>
                                        <li>
                                            <?php esc_html_e($player->nombre); ?>
                                            (<?php esc_html_e(($player->promedio) ? $player->promedio : 'ns') ;?>)
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach ?>
                            </ul>
                            <?php
                            $prom_equipo = 0;
                            $promediosEquipo[1] = array_filter($promediosEquipo[1]);
                            if( count($promediosEquipo[1]) ):
                                $prom_equipo = array_sum($promediosEquipo[1])/count($promediosEquipo[1]);
                            ?>
                                <p class="team_average">
                                    <?php esc_html_e(number_format((float)$prom_equipo, 2, '.', '')); ?>
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no_teams">
               <?php esc_html_e('No hay equipos todav&iacute;a.', 'fulbito') ?>
            </p>
        <?php endif;?>
    <?php endif;?>

    <!-- SUBSCRIPTORS LIST -->
    <?php if( $registered && $onTeam !== 10 ) : ?>
        <h4 class="subscriptors-title"><?php esc_html_e('Inscriptos', 'fulbito') ?> (<?php esc_html_e($registered);?>)</h4>
        <ul class="subscriptors-list">
            <?php foreach($players as $player): if( $player->participa ):  ?>
                <li>
                    <?php esc_html_e($player->nombre); ?>
                    <?php if($player->lesion): ?>
                        <i class="fa fa-wheelchair" aria-hidden="true"></i>
                    <?php elseif($player->favorito): ?>
                        <i class="fa fa-star" aria-hidden="true"></i>
                    <?php endif; ?>
                    (<?php esc_html_e($player->promedio) ?>)
                </li>
            <?php endif; endforeach;?>
        </ul>
    <?php elseif( !$registered ) : ?>
        <p class="no_subscriptors">
            <?php esc_html_e('No hay inscriptos todav&iacute;a', 'fulbito') ?>
        </p>
    <?php endif; ?>
<?php endwhile;?>
</div>
