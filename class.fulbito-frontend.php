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

        // Game - single - Add metadata
        add_filter( 'the_content', [$this, 'addSingleGameMetadata']);
        // Game - lists - Add metadata
        add_filter( 'the_content', [$this, 'addListsGameMetadata']);
        // Add shortcodes
        add_shortcode('tabla_posiciones', [$this,'shortcode_positionsTable']);
        add_shortcode('inscripcion', [$this,'shortcode_subscriptionForm']);
        add_shortcode('ficha_personal', [$this,'shortcode_playerProfile']);
        // Subscribe player from frontend subscription form
        add_action('init', [$this, 'subscribePlayer']);
    }

    public function addSingleGameMetadata($content) {
        if(is_admin() || !is_singular('ft_partidos')) return $content;

        $jugadores = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $partido = $this->FulbitoDB->getPartido(get_the_ID())[0];

        include_once('views/frontend/single-game.php');

        return $content;
    }

    public function addListsGameMetadata($content) {
        global $post;
        if(is_admin() || is_singular() || $post->post_type !== 'ft_partidos'  ) return $content;

        $jugadores = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $partido = $this->FulbitoDB->getPartido(get_the_ID())[0];

        include('views/frontend/list-game.php');

        return $content;
    }

    public function shortcode_positionsTable() {
        ob_start();
        $tabla = $this->FulbitoDB->getTabla();
        include_once('views/frontend/shortcodes/positions-table.php');
        return ob_get_clean();
    }

    public function shortcode_subscriptionForm() {
        ob_start();
        // There must be a not-played game
        $prox_partido = $this->FulbitoDB->getPartidoSinJugar();
        if( $prox_partido ):
            $args = array( 'post_type'=>'ft_partidos', 'p'=>$prox_partido->partidoID );
            $game_query = new WP_Query( $args );
            $players = $this->FulbitoDB->getJugadores( $prox_partido->partidoID, 1 );
            include_once('views/frontend/shortcodes/subscription-form.php');
        else:
            echo '<p>';
                _e('Todav&iacute;a no se carg&oacute; el pr&oacute;ximo partido, perro!.','fulbito');
            echo '</p>';
        endif;
        return ob_get_clean();
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

    public function shortcode_playerProfile() {
        ob_start();
        // FIXME fix use of $_GET!! <- use wp query vars instead
        if( isset($_GET['jugador']) && $_GET['jugador'] ):
            $jugadorID = (int)$_GET['jugador'];
            $jugador_ficha = $this->FulbitoDB->getFichaJugador($jugadorID);
            $total_partidos = $this->FulbitoDB->getTotalPartidos();
            include_once('views/frontend/shortcodes/player-profile.php');
        else:
            _e('Qu&eacute; ficha?', 'fulbito');
        endif;
        return ob_get_clean();
    }
}


?>
