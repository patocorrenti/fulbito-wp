<?php

// CARGAR partido metadata ---------------------
function load_partido_metadata($query) {

    if( $query->is_main_query() && $post->post_type != 'ft_partidos' )
        add_action( 'the_post', 'get_partido_metadata' );

}
add_action( 'loop_start', 'load_partido_metadata' );
function get_partido_metadata($post){
    global $fulbito_data;
    // TODO: corregir este global
    global $jugadores;
    global $partido;
    $jugadores = $fulbito_data->getJugadores($post->ID, 1);
    $partido = $fulbito_data->getPartido($post->ID);
    $partido = $partido[0];
}

//auto inscripcion de jugadores
function inscribir_jugador() {
    if( isset($_POST['jugador']) && isset($_POST['partido']) && isset($_POST['inscribir']) && isset($_POST['validancia']) && !$_POST['validancia']  ):

        global $fulbito_data;
        $fulbito_data->inscribirJugador( (int)$_POST['jugador'], (int)$_POST['partido'] );

    endif;
}
add_action('init', inscribir_jugador);

//Shortcode para cargar la tabla de posiciones
function shortcode_tabla() {

    global $fulbito_data;
    $tabla = $fulbito_data->getTabla();
    include_once('templates/shortcode-tabla.html');

}
add_shortcode('tabla_posiciones', 'shortcode_tabla');

//Shortcode para inscripcion al partido
function shortcode_inscripcion() {

    //tiene que existir un partido creado sin jugar
    global $fulbito_data;
    $prox_partido = $fulbito_data->getPartidoSinJugar();
    if( $prox_partido ):

        $args = array( 'post_type'=>'ft_partidos', 'p'=>$prox_partido->partidoID );
        $partido_query = new WP_Query( $args );
        $jugadores = $fulbito_data->getJugadores( $prox_partido->partidoID, 1 );
        include_once('templates/shortcode-inscripcion.html');

    else:

        echo '<p>Todav&iacute;a no se carg&oacute; el pr&oacute;ximo partido, perro!.</p>';

    endif;

}
add_shortcode('inscripcion', 'shortcode_inscripcion');

//Shortcode para cargar la ficha de una persona
function shortcode_ficha () {

    if( isset($_GET['jugador']) && $_GET['jugador'] ):

        $jugadorID = (int)$_GET['jugador'];

        global $fulbito_data;
        global $total_partidos;

        $jugador_ficha = $fulbito_data->getFichaJugador($jugadorID);
        $total_partidos = $fulbito_data->getTotalPartidos();

        include_once('templates/shortcode-ficha.html');

    else:
        echo "Qu&eacute; ficha?";
    endif;

}
add_shortcode('ficha_personal', 'shortcode_ficha');

?>
