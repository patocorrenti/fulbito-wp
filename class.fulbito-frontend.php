<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FulbitoFrontend Class
 *
 *
 */

class FulbitoFrontend {

    var $FulbitoDB;

    // Fulbito DB class instance needs to be inyected
    function FulbitoFrontend($FulbitoDB) {

        $this->FulbitoDB = $FulbitoDB;

        // Subscribe player from frontend subscription form
        add_action('init', [$this, 'subscribePlayer']);
        // Add shortcodes
        add_shortcode('tabla_posiciones', [$this,'shortcode_positionsTable']);
        add_shortcode('inscripcion', [$this,'shortcode_subscriptionForm']);
        add_shortcode('ficha_personal', [$this,'shortcode_playerProfile']);
    }


    public function subscribePlayer() {
        // FIXME fix use of $_POST!!! <- use wp query vars instead
        if(
            isset($_POST['jugador'])
            && isset($_POST['partido'])
            && isset($_POST['inscribir'])
            && isset($_POST['validancia'])
            && !$_POST['validancia']
        ):
            $this->FulbitoDB->inscribirJugador( (int)$_POST['jugador'], (int)$_POST['partido'] );
        endif;
    }

    public function shortcode_positionsTable() {
        ob_start();
        $tabla = $this->FulbitoDB->getTabla();
        include_once('views/shortcodes/html-positions-table.php');
        return ob_get_clean();
    }

    public function shortcode_subscriptionForm() {
        ob_start();
        // There must be a not-played game
        $prox_partido = $this->FulbitoDB->getPartidoSinJugar();
        if( $prox_partido ):
            $args = array( 'post_type'=>'ft_partidos', 'p'=>$prox_partido->partidoID );
            $partido_query = new WP_Query( $args );
            $jugadores = $this->FulbitoDB->getJugadores( $prox_partido->partidoID, 1 );
            include_once('views/shortcodes/html-subscription-form.php');
        else:
            echo '<p>';
            _e('Todav&iacute;a no se carg&oacute; el pr&oacute;ximo partido, perro!.','fulbito');
            echo '</p>';
        endif;
        return ob_get_clean();
    }

    public function shortcode_playerProfile() {
        ob_start();
        // FIXME fix use of $_GET!! <- use wp query vars instead
        if( isset($_GET['jugador']) && $_GET['jugador'] ):
            $jugadorID = (int)$_GET['jugador'];
            $jugador_ficha = $this->FulbitoDB->getFichaJugador($jugadorID);
            $total_partidos = $this->FulbitoDB->getTotalPartidos();
            include_once('views/shortcodes/html-player-profile.php');
        else:
            _e('Qu&eacute; ficha?', 'fulbito');
        endif;
        return ob_get_clean();
    }
}



// FIXME : wrap this into frontend class
function load_partido_metadata($query) {

    if( $query->is_main_query() && $post->post_type != 'ft_partidos' )
        add_action( 'the_post', 'get_partido_metadata' );

}
add_action( 'loop_start', 'load_partido_metadata' );
function get_partido_metadata($post){
    global $fulbito_data;
    // FIXME don't use globals!!! <-- use wp query vars instead
    global $jugadores;
    global $partido;
    $jugadores = $fulbito_data->getJugadores($post->ID, 1);
    $partido = $fulbito_data->getPartido($post->ID);
    $partido = $partido[0];
}



?>
