<?php if( $jugador_ficha && is_array($jugador_ficha) ): ?>
    <h2>
        <?php echo $jugador_ficha['datos']->nombre;?>
    </h2>
    <ul>
        <li>
            <?php if($jugador_ficha['datos']->lesion): ?>
                <i class="fa fa-wheelchair" aria-hidden="true"></i>
                <?php
                    echo sprintf(__('Esta lesionado, si eres %s por favor visita a tu veterinario.', 'fulbito'), $jugador_ficha['datos']->nombre )
                ?>
            <?php elseif($jugador_ficha['datos']->favorito): ?>
                <i class="fa fa-star" aria-hidden="true"></i>
                <?php _e('Todos los lunes ah&iacute; en hora y vestido como corresponde.', 'fulbito') ?>
            <?php endif; ?>
        </li>
        <li>
            <?php
                echo sprintf(__('%d de %d partidos jugados', 'fulbito'), $jugador_ficha['partidos']->jugados, $total_partidos)
            ?>
            (<?php echo round( (int)$jugador_ficha['partidos']->jugados * 100 / (int)$total_partidos);?>%)
        </li>
        <li>
            <?php
                echo sprintf(__('%d suspensiones', 'fulbito'), $jugador_ficha['partidos']->suspensiones)
            ?>
            (<?php echo round( (int)$jugador_ficha['partidos']->suspensiones * 100 / (int)$jugador_ficha['partidos']->jugados, 2 ) ?>%)
        </li>
        <li>
            <?php
                echo sprintf(
                    __('%d en Equipo A (Blanco) y %d en Equipo B (Coco)', 'fulbito')
                    , $jugador_ficha['partidos']->blanco
                    , $jugador_ficha['partidos']->coco
                )
            ?>
        </li>
    </ul>
    <br><br>
    <h3>
        <?php _e('Jugaste con', 'fulbito') ?>:
    </h3>
    <ul>
        <?php foreach($jugador_ficha['relacionados'] as $jugador): if($jugador->cantidad > 10): ?>
        <li>
            <a href="<?php bloginfo('url') ?>/ficha/?jugador=<?php echo $jugador->id;?>">
                <?php echo $jugador->nombre; ?>
            </a>
            <?php echo sprintf(__('%d veces', 'fulbito'), $jugador->cantidad) ?>
        </li>
        <?php endif; endforeach; ?>
    </ul>
<?php else: ?>
    <p><?php _e('No conozco ese jugador.', 'fulbito') ?></p>
<?php endif; ?>
