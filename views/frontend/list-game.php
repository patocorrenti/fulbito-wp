<?php if ($partido->resultado) : ?>
<table width="100%">
    <thead>
        <tr>
            <?php if( $partido->resultado == 1) : ?>
                <th><?php _e('Ganador', 'fulbito') ?></th>
                <th></th>
            <?php elseif( $partido->resultado == 2 ) : ?>
                <th></th>
                <th><?php _e('Ganador', 'fulbito') ?></th>
            <?php elseif( $partido->resultado == 3 ) : ?>
                <th colspan="2"><?php _e('Empate', 'fulbito') ?></th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="50%">
                <ul>
                <?php foreach ($jugadores as $jugador): ?>
                    <?php if($jugador->equipo == 1 && $jugador->participa ): ?>
                    <li>
                        <?php echo $jugador->nombre; ?>
                        <?php echo ($jugador->promedio) ? '('.$jugador->promedio.')' : ''; ?>
                        <?php if($jugador->suspendido): ?>
                            <div>
                                <?php _e('Suspendido', 'fulbito') ?>
                            </div>
                        <?php endif;?>
                    </li>
                    <?php endif; ?>
                <?php endforeach ?>
                </ul>
            </td>
            <td width="50%">
                <ul>
                    <?php foreach ($jugadores as $jugador): ?>
                        <?php if($jugador->equipo == 2 && $jugador->participa ): ?>
                        <li>
                            <?php echo $jugador->nombre; ?>
                            <?php echo ($jugador->promedio) ? '('.$jugador->promedio.')' : ''; ?>
                            <?php if($jugador->suspendido): ?>
                                <div>
                                    <?php _e('Suspendido', 'fulbito') ?>
                                </div>
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
    <p>
        <?php _e('Este partido a&uacute;n no se jug&oacute;', 'fulbito') ?>
    </p>
<?php endif; ?>
