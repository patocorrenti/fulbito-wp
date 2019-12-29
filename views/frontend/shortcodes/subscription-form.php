<?php
// Totals
$inscriptos = 0;
$equipados  = 0;
if( is_array( $players ) ){
    foreach($players as $player){
        if( $player->participa) $inscriptos++;
        if( $player->equipo ) $equipados++;
    }
}
?>

<?php while( $game_query->have_posts() ): $game_query->the_post();?>

    <!-- DATE -->
    <?php if ($game->fecha) : ?>
        <h2>
            <?php echo date_i18n(get_option('date_format'), strtotime($game->fecha)) ?>
        </h2>
    <?php endif ?>

    <!-- FORM -->
    <?php if( $inscriptos < 10 ): ?>
        <form action="" method="post">
            <input type="hidden" name="validancia" value="">
            <input type="hidden" name="partido" value="<?php echo get_the_ID(); ?>" >
            <label for="jugador">Jugador:</label>
            <select name="jugador" id="jugador">
                <?php foreach( $players as $player ): if( !$player->participa ): ?>
                    <option value="<?php echo $player->id; ?>">
                        <?php echo $player->nombre; ?>
                    </option>
                <?php endif; endforeach; ?>
            </select>
            <br>
            <input type="submit" name="inscribir" value="<?php _e('Inscribir', 'fulbito') ?>">
        </form>
    <?php else: ?>
        <p>
            <?php _e('La inscripci&oacute;n cerr&oacute;, te quedaste afuera.', 'fulbito') ?>
        </p>
    <?php endif;?>

    <!-- COMPLETED -->
    <?php if( $inscriptos === 10 ):?>
        <?php if( $equipados === 10 ): ?>
            <h4><?php _e('Equipos', 'fulbito') ?></h4>
            <table>
                <thead>
                    <tr>
                        <th width="50%">
                            <?php _e('Equipo A (Blanco)', 'fulbito') ?>
                        </th>
                        <th width="50%">
                            <?php _e('Equipo B (Coco)', 'fulbito') ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php $promediosEquipo = array();?>
                            <ul>
                                <?php foreach ($players as $player): ?>
                                    <?php if($player->equipo == 1 && $player->participa ): ?>
                                        <?php $promediosEquipo[0][] = $player->promedio; ?>
                                        <li>
                                            <span><?php echo $player->nombre; ?></span>
                                            <?php if($player->suspendido): ?>
                                                <span>
                                                    <i class="fa fa-ban" aria-hidden="true"></i>
                                                </span>
                                            <?php endif;?>
                                            <span>
                                                <?php echo ($player->promedio) ? $player->promedio : 'ns' ;?>
                                            </span>
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
                                <p>
                                    <?php echo number_format((float)$prom_equipo, 2, '.', ''); ?>
                                </p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <ul>
                                <?php foreach ($players as $player):  ?>
                                    <?php if($player->equipo == 2 && $player->participa ): ?>
                                        <?php $promediosEquipo[1][] = $player->promedio; ?>
                                        <li>
                                            <span><?php echo $player->nombre; ?></span>
                                            <?php if($player->suspendido): ?>
                                                <span>
                                                    <i class="fa fa-ban" aria-hidden="true"></i>
                                                </span>
                                            <?php endif;?>
                                            <span>
                                                <?php echo ($player->promedio) ? $player->promedio : 'ns' ;?>
                                            </span>
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
                                <p>
                                    <?php echo number_format((float)$prom_equipo, 2, '.', ''); ?>
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>
               <?php _e('No hay equipos todav&iacute;a.', 'fulbito') ?>
            </p>
        <?php endif;?>
    <?php endif;?>

    <!-- SUBSCRIPTORS LIST -->
    <?php if( $inscriptos && $equipados !== 10 ) : ?>
        <h4>Inscriptos (<?php echo $inscriptos;?>)</h4>
        <ul>
            <?php foreach($players as $player): if( $player->participa ):  ?>
                <li>
                    <span>
                        <?php print_r($player->nombre); ?>
                        <?php if($player->lesion): ?>
                            <i class="fa fa-wheelchair" aria-hidden="true"></i>
                        <?php elseif($player->favorito): ?>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        <?php endif; ?>
                    </span>
                    <span>
                        <?php print_r($player->promedio); ?>
                    </span>
                </li>
            <?php $total ++; endif; endforeach;?>
        </ul>
    <?php elseif( !$inscriptos ) : ?>
        <p>
            <?php _e('No hay inscriptos todav&iacute;a', 'fulbito') ?>
        <p>
    <?php endif; ?>


<?php endwhile;?>
