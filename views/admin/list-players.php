<?php defined( 'ABSPATH' ) or exit(); ?>

<div class="wrap">
    <h1><?php _e('Configuraci&oacute;n', 'fulbito') ?></h1>
    <h3><?php _e('Tabla de posiciones', 'fulbito') ?></h3>
    <form method="post" action="">
        <p class="description">
            (<?php _e('Utilizar esto cuando se cambia el resultado de un partido viejo', 'fulbito') ?>)
        </p>
        <p class="submit">
            <input
                class="button button-primary"
                type='submit'
                name='regenerar_tabla'
                value='<?php _e('Regenerar tabla de posiciones', 'fulbito') ?>'
            />
        </p>
    </form>
    <form method="post" action="">
        <p class="submit">
            <input
                class="button button-primary"
                type='submit'
                name='migrar_fechas'
                value='<?php _e('Migrar fechas de ACF a Fulbito', 'fulbito') ?>'
            />
        </p>
    </form>
    <hr>
    <h3>
        <?php _e('Jugadores', 'fulbito') ?>
        <a href="#" class="page-title-action">
            <?php _e('A&ntilde;adir nuevo', 'fulbito') ?>
        </a>
    </h3>
    <form method="post" action="">
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th scope="col">
                        <?php _e('Nombre', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Email', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Favorito', 'fulbito') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Activo', 'fulbito') ?>
                    </th>
                </tr>
            </thead>
            <tbody id="tabla_jugadores">
                <?php foreach($players as $player): ?>
                    <tr class="editar_fila">
                        <td>
                            <span><?php echo($player->nombre); ?></span>
                        </td>
                        <td class="editable">
                            <input
                                type="email"
                                name="jugadores[<?php echo $player->id; ?>][email]"
                                value="<?php echo($player->email); ?>"
                            />
                        </td>
                        <td class="editable">
                            <select name="jugadores[<?php echo $player->id; ?>][favorito]">
                                <option value="1" <?php if($player->favorito) echo 'selected'; ?> >
                                    <?php _e('Si', 'fulbito') ?>
                                </option>
                                <option value="0" <?php if(!$player->favorito) echo 'selected'; ?> >
                                    <?php _e('No', 'fulbito') ?>
                                </option>
                            </select>
                        </td>
                        <td class="editable">
                            <select name="jugadores[<?php echo $player->id; ?>][activo]">
                                <option value="1" <?php if($player->activo) echo 'selected'; ?>>
                                    <?php _e('Si', 'fulbito') ?>
                                </option>
                                <option value="0" <?php if(!$player->activo) echo 'selected'; ?>>
                                    <?php _e('No', 'fulbito') ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="submit">
            <input class="button button-primary" type='submit' name='editar_jugadores' value='<?php _e('Guardar cambios', 'fulbito') ?>' />
        </p>
    </form>
</div>
