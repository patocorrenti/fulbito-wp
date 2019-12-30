<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FulbitoFrontend Class
 *
 *
 */

class FulbitoFrontend extends FulbitoCommons {

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

        $players = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $game = $this->FulbitoDB->getPartido(get_the_ID())[0];
        $this->ft_get_template('frontend/single-game', ['players' => $players, 'game' => $game]);

        return $content;
    }

    public function addListsGameMetadata($content) {
        global $post;
        if(is_admin() || is_singular() || $post->post_type !== 'ft_partidos'  ) return $content;

        $players = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $game = $this->FulbitoDB->getPartido(get_the_ID())[0];
        $this->ft_get_template('frontend/list-game', ['players' => $players, 'game' => $game]);

        return $content;
    }

    public function shortcode_positionsTable() {
        ob_start();
        if (!get_query_var('ft_show_profile')) :
            $tabla = $this->FulbitoDB->getTabla();
            $this->ft_get_template('frontend/shortcodes/positions-table', ['tabla' => $tabla]);
        else :
            $playerID = get_query_var('ft_show_profile');
            $jugador_ficha = $this->FulbitoDB->getFichaJugador($playerID);
            $total_partidos = $this->FulbitoDB->getTotalPartidos();
            $this->ft_get_template(
                'frontend/shortcodes/player-profile',
                ['playerID' => $playerID, 'jugador_ficha' => $jugador_ficha, 'total_partidos' => $total_partidos]
            );
        endif;
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
            $game = $this->FulbitoDB->getPartido($prox_partido->partidoID)[0];
            $this->ft_get_template(
                'frontend/shortcodes/subscription-form',
                ['game_query' => $game_query, 'players' => $players, 'game' => $game]
            );
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
            wp_verify_nonce( wp_unslash($_POST['_wpnonce']), 'ft_subscribe_player')
            && isset($_POST['jugador'])
            && isset($_POST['partido'])
            && isset($_POST['inscribir'])
            && isset($_POST['validancia'])
            && !$_POST['validancia']
        ):
            $this->FulbitoDB->inscribirJugador( (int)$_POST['jugador'], (int)$_POST['partido'] );
        endif;
    }
}


?>
