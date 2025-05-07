<?php
    defined( 'ABSPATH' ) or exit();

    $tabla = $templateArgs['tabla'];
?>
<div class="fulbito table">
<?php if(is_array($tabla)): ?>
    <table class="position-table" width="100%">
        <thead class="header">
            <tr>
                <th class="nombre">
                    <?php esc_html_e(_x('Nombre', 'Position Table header for name', 'fulbito')) ?>
                </th>
                <th class="played">
                    <?php esc_html_e(_x('J', 'Position Table header for Played games', 'fulbito')) ?>
                </th>
                <th class="won">
                    <?php esc_html_e(_x('G', 'Position Table header for Wined games', 'fulbito')) ?>
                </th>
                <th class="tied">
                    <?php esc_html_e(_x('E', 'Position Table header for Tied games', 'fulbito')) ?>
                </th>
                <th class="lost">
                    <?php esc_html_e(_x('P', 'Position Table header for Lost games', 'fulbito')) ?>
                </th>
                <th class="points">
                    <?php esc_html_e(_x('Pts', 'Position Table header for Points', 'fulbito')) ?>
                </th>
                <th class="average">
                    <?php esc_html_e(_x('Prom.', 'Position Table header for Average', 'fulbito')) ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $tabla as $key => $player ):?>
            <tr>
                <td class="name">
                    <a href="?ft_show_profile=<?php echo esc_attr($player->id);?>">
                        <?php esc_html_e($player->nombre) ?>
                        <?php if($player->lesion): ?>
                            <i class="fas fa-wheelchair"></i>
                        <?php elseif($player->favorito): ?>
                            <i class="fas fa-star"></i>
                        <?php endif; ?>
                    </a>
                </td>
                <td class="played"><?php esc_html_e($player->jugados) ?></td>
                <td class="won"><?php esc_html_e($player->ganados) ?></td>
                <td class="tied"><?php esc_html_e($player->empatados) ?></td>
                <td class="lost"><?php esc_html_e($player->perdidos) ?></td>
                <td class="points"><?php esc_html_e($player->puntos) ?></td>
                <td class="average"><?php esc_html_e($player->promedio) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif;?>
</div>
