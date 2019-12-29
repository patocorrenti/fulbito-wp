<?php
//calculo totales
$inscriptos = 0;
$equipados  = 0;
if( is_array( $jugadores ) ){
    foreach($jugadores as $jugador){
        if( $jugador->participa) $inscriptos++;
        if( $jugador->equipo ) $equipados++;
    }
}
?>

<?php while( $partido_query->have_posts() ): $partido_query->the_post();?>

    <?php $date = new DateTime( get_field('fecha', false, false) ); ?>
    <p>Se juega el <?php echo $date->format('j M Y'); ?><p>

    <?php if( $inscriptos == 10 ):?>
        <section class="armado">
            <?php if( $equipados == 10 ): // EQUIPOS ---------------------------------------- ?>
                <h2>Equipos</h2>
                <div class="equipos">
                    <div class="equipo">
                        <header>
                            <h3>
                                <i class="fa fa-shield blancoshield" aria-hidden="true"></i>
                                Blanco
                            </h3>
                            <?php if( $partido->resultado == 1): ?>
                                <span class="ganador">
                                    <i class="fa fa-trophy" aria-hidden="true"></i>
                                    GANADOR
                                </span>
                            <?php endif;?>
                        </header>
                        <ul>
                            <?php $promediosEquipo = array();?>
                            <?php foreach ($jugadores as $jugador): ?>
                                    <?php if($jugador->equipo == 1 && $jugador->participa ): ?>
                                        <?php $promediosEquipo[0][] = $jugador->promedio; ?>
                                        <li>
                                            <span class="nombre"><?php echo $jugador->nombre; ?></span>
                                            <?php if($jugador->suspendido): ?>
                                                <span class="suspendido">
                                                    <i class="fa fa-ban" aria-hidden="true"></i>
                                                </span>
                                            <?php endif;?>
                                            <span class="promedio">
                                                <?php echo ($jugador->promedio) ? $jugador->promedio : 'ns' ;?>
                                            </span>
                                        </li>
                                    <?php endif; ?>
                            <?php endforeach ?>
                        </ul>
                        <?php
                        $prom_equipo = 0;
                        $promediosEquipo[0] = array_filter($promediosEquipo[0]);
                        if( count($promediosEquipo[0]) ): $prom_equipo = array_sum($promediosEquipo[0])/count($promediosEquipo[0]);
                        ?>
                            <div class="promedio_total">
                                <?php echo number_format((float)$prom_equipo, 2, '.', ''); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="equipo">
                        <header>
                            <h3>
                                <i class="fa fa-bolt" aria-hidden="true"></i>
                                Coco
                            </h3>
                            <?php if( $partido->resultado == 2): ?>
                                <span class="ganador">
                                    <i class="fa fa-trophy" aria-hidden="true"></i>
                                    GANADOR
                                </span>
                            <?php endif;?>
                        </header>
                        <ul>
                            <?php foreach ($jugadores as $jugador):  ?>
                                <?php if($jugador->equipo == 2 && $jugador->participa ): ?>
                                    <?php $promediosEquipo[1][] = $jugador->promedio; ?>
                                    <li>
                                        <span class="nombre"><?php echo $jugador->nombre; ?></span>
                                        <?php if($jugador->suspendido): ?>
                                            <span class="suspendido">
                                                <i class="fa fa-ban" aria-hidden="true"></i>
                                            </span>
                                        <?php endif;?>
                                        <span class="promedio">
                                            <?php echo ($jugador->promedio) ? $jugador->promedio : 'ns' ;?>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach ?>
                        </ul>
                        <?php
                        $prom_equipo = 0;
                        $promediosEquipo[1] = array_filter($promediosEquipo[1]);
                        if( count($promediosEquipo[1]) ): $prom_equipo = array_sum($promediosEquipo[1])/count($promediosEquipo[1]);
                        ?>
                            <div class="promedio_total">
                                <?php echo number_format((float)$prom_equipo, 2, '.', ''); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="no_hay">
                    No hay equipos todav&iacute;a.
                </div>
            <?php endif;?>
        </section>
    <?php endif;?>

    <section class="inscripcion">
        <?php if( $inscriptos < 10 ): // FORMULARIO DE INSCRIPCION ---------------------------------------- ?>
            <form action="" method="post">
                <input type="hidden" name="validancia" value="">
                <input type="hidden" name="partido" value="<?php echo get_the_ID(); ?>" >
                <label for="jugador">Jugador:</label>
                <select name="jugador" id="jugador">
                    <?php foreach( $jugadores as $jugador ): if( !$jugador->participa ): ?>
                        <option value="<?php echo $jugador->id; ?>">
                            <?php echo $jugador->nombre; ?>
                        </option>
                    <?php endif; endforeach; ?>
                </select>
                <input type="submit" name="inscribir" value="Inscribir">
            </form>
        <?php else: ?>
            <div class="no_hay">
                La inscripci&oacute;n cerr&oacute;, te quedaste afuera.
            </div>
        <?php endif;?>
    </section>

    <section class="inscriptos">
        <?php if( $inscriptos ): // LISTA DE INSCRIPTOS ---------------------------------------- ?>
            <h2>Inscriptos (<?php echo $inscriptos;?>)</h2>
            <ul class="lista_inscriptos">
                <?php foreach($jugadores as $jugador): if( $jugador->participa ):  ?>
                    <li>
                        <span class="nombre">
                            <?php print_r($jugador->nombre); ?>
                            <?php if($jugador->lesion): ?>
                                <i class="fa fa-wheelchair" aria-hidden="true"></i>
                            <?php elseif($jugador->favorito): ?>
                                <i class="fa fa-star" aria-hidden="true"></i>
                            <?php endif; ?>
                        </span>
                        <span class="promedio">
                            <?php print_r($jugador->promedio); ?>
                        </span>
                    </li>
                <?php $total ++; endif; endforeach;?>
            </ul>
        <?php else: ?>
            <div class="no_hay">
                No hay inscriptos todav&iacute;a
            </div>
        <?php endif; ?>
    </section>


<?php endwhile;?>
