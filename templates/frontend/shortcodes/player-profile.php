<?php
    defined( 'ABSPATH' ) or exit();

    $jugador_ficha = $templateArgs['jugador_ficha'];
    $total_partidos = $templateArgs['jugador_ficha']['partidos']->total;
?>
<div class="fulbito player-profile">
<?php if( $jugador_ficha && is_array($jugador_ficha) ): ?>
    <h2 class="name">
        <?php echo $jugador_ficha['datos']->nombre;?>
    </h2>
    <ul class="data">
        <li>
            <span class="average">
                <?php echo $jugador_ficha['partidos']->stats->promedio ?>%
            </span>
            <span class="points">
                (<?php echo sprintf(__('%d puntos', 'fulbito'), $jugador_ficha['partidos']->stats->puntos) ?>)
            </span>
        </li>
        <?php if($jugador_ficha['datos']->lesion): ?>
            <li class="injured">
                <i class="fas fa-wheelchair"></i>
                <?php
                    echo sprintf(__('Esta lesionado, si eres %s por favor visita a tu veterinario.', 'fulbito'), $jugador_ficha['datos']->nombre )
                ?>
            </li>
        <?php elseif($jugador_ficha['datos']->favorito): ?>
            <li class="favorite">
                <i class="fas fa-star"></i>
                <?php _e('Participante destacado.', 'fulbito') ?>
            </li>
        <?php endif; ?>
        <li class="games">
            <?php
                echo sprintf(__('%d de %d partidos jugados', 'fulbito'), $jugador_ficha['partidos']->jugados, $total_partidos)
            ?>
            (<?php echo round( (int)$jugador_ficha['partidos']->jugados * 100 / (int)$total_partidos);?>%)
            <ul class="stats">
                <li>
                    <?php
                        echo sprintf(__('%d ganados', 'fulbito'), $jugador_ficha['partidos']->stats->ganados)
                    ?>
                </li>
                <li>
                    <?php
                        echo sprintf(__('%d perdidos', 'fulbito'), $jugador_ficha['partidos']->stats->perdidos)
                    ?>
                </li>
                <li>
                    <?php
                        echo sprintf(__('%d empatados', 'fulbito'), $jugador_ficha['partidos']->stats->empatados)
                    ?>
                </li>
            </ul>
        </li>
        <li class="suspensions">
            <?php
                echo sprintf(__('%d suspensiones', 'fulbito'), $jugador_ficha['partidos']->suspensiones)
            ?>
            (<?php echo round( (int)$jugador_ficha['partidos']->suspensiones * 100 / (int)$jugador_ficha['partidos']->jugados, 2 ) ?>%)
        </li>
        <li class="streak">
            <?php _e('Rachas m&aacute;ximas', 'fulbito') ?>
            <ul class="streaks">
                <li class="winning">
                    <?php
                        echo sprintf(
                            __('%d ganados', 'fulbito')
                            , $jugador_ficha['streak']['winning']
                        )
                    ?>
                </li>
                <li class="losing">
                    <?php
                        echo sprintf(
                            __('%d perdidos', 'fulbito')
                            , $jugador_ficha['streak']['losing']
                        )
                    ?>
                </li>
            </ul>
        </li>
        <li class="team">
            <?php _e('Equipos', 'fulbito') ?>
            <ul class="teams">
                <li>
                    <?php
                        echo sprintf(
                            __('%d veces en Equipo A (Blanco)', 'fulbito')
                            , $jugador_ficha['partidos']->teamA
                        )
                    ?>
                </li>
                <li>
                    <?php
                        echo sprintf(
                            __('%d veces en Equipo B (Coco)', 'fulbito')
                            , $jugador_ficha['partidos']->teamB
                        )
                    ?>
                </li>
            </ul>
        </li>
    </ul>
    <h4 class="played-with">
        <?php _e('Jugaste con', 'fulbito') ?>:
    </h4>
    <ul class="players-list">
        <?php foreach($jugador_ficha['relacionados'] as $jugador): if($jugador->cantidad > 10): ?>
        <li>
            <a href="?ft_show_profile=<?php echo $jugador->id;?>">
                <?php echo $jugador->nombre; ?>
            </a>
            <?php echo sprintf(__('%d veces', 'fulbito'), $jugador->cantidad) ?>
        </li>
        <?php endif; endforeach; ?>
    </ul>
<?php else: ?>
    <p class="error"><?php _e('No conozco ese jugador.', 'fulbito') ?></p>
<?php endif; ?>
</div>
