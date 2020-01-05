<?php
    defined( 'ABSPATH' ) or exit();

    $playerID = $templateArgs['playerID'];
    $jugador_ficha = $templateArgs['jugador_ficha'];
    $total_partidos = $templateArgs['total_partidos'];
?>
<div class="fulbito player-profile">
<?php if( $jugador_ficha && is_array($jugador_ficha) ): ?>
    <h2 class="name">
        <?php echo $jugador_ficha['datos']->nombre;?>
    </h2>
    <ul class="data">
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
                <?php _e('Todos los lunes ah&iacute; en hora y vestido como corresponde.', 'fulbito') ?>
            </li>
        <?php endif; ?>
        <li class="games">
            <?php
                echo sprintf(__('%d de %d partidos jugados', 'fulbito'), $jugador_ficha['partidos']->jugados, $total_partidos)
            ?>
            (<?php echo round( (int)$jugador_ficha['partidos']->jugados * 100 / (int)$total_partidos);?>%)
        </li>
        <li class="suspensions">
            <?php
                echo sprintf(__('%d suspensiones', 'fulbito'), $jugador_ficha['partidos']->suspensiones)
            ?>
            (<?php echo round( (int)$jugador_ficha['partidos']->suspensiones * 100 / (int)$jugador_ficha['partidos']->jugados, 2 ) ?>%)
        </li>
        <li class="teams">
            <?php
                echo sprintf(
                    __('%d en Equipo A (Blanco) y %d en Equipo B (Coco)', 'fulbito')
                    , $jugador_ficha['partidos']->blanco
                    , $jugador_ficha['partidos']->coco
                )
            ?>
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
