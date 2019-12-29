<?php if(is_array($tabla)): ?>
    <table class="ft_tabla_posiciones" width="100%">
        <thead>
            <tr>
                <th class="nombre">
                    <?php echo _x('Nombre', 'Position Table header for name', 'fulbito') ?>
                </th>
                <th class="jugados">
                    <?php echo _x('J', 'Position Table header for Played games', 'fulbito') ?>
                </th>
                <th class="ganados">
                    <?php echo _x('G', 'Position Table header for Wined games', 'fulbito') ?>
                </th>
                <th class="empatados">
                    <?php echo _x('E', 'Position Table header for Tied games', 'fulbito') ?>
                </th>
                <th class="perdidos">
                    <?php echo _x('P', 'Position Table header for Lost games', 'fulbito') ?>
                </th>
                <th class="puntos">
                    <?php echo _x('Pts', 'Position Table header for Points', 'fulbito') ?>
                </th>
                <th class="promedio">
                    <?php echo _x('Prom.', 'Position Table header for Average', 'fulbito') ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $tabla as $key => $player ):?>
            <tr>
                <td class="nombre">
                    <a href="?ft_show_profile=<?php echo $player->id;?>">
                        <?php echo $player->nombre ?>
                        <?php if($player->lesion): ?>
                            <i class="fa fa-wheelchair" aria-hidden="true"></i>
                        <?php elseif($player->favorito): ?>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        <?php endif; ?>
                    </a>
                </td>
                <td class="jugados"><?php echo $player->jugados ?></td>
                <td class="ganados"><?php echo $player->ganados ?></td>
                <td class="empatados"><?php echo $player->empatados ?></td>
                <td class="perdidos"><?php echo $player->perdidos ?></td>
                <td class="puntos"><?php echo $player->puntos ?></td>
                <td class="promedio"><?php echo $player->promedio ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif;?>
