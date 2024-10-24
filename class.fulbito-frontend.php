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
    function __construct($FulbitoDB) {

        $this->FulbitoDB = $FulbitoDB;

        add_action( 'wp_enqueue_scripts', [$this,'enqueue_scripts']);

        // Game - single - Add metadata
        add_filter( 'the_content', [$this, 'addSingleGameMetadata']);
        // Game - lists - Add metadata
        add_filter( 'the_content', [$this, 'addListsGameMetadata']);
        // Add shortcodes
        add_shortcode('fulbito_tabla', [$this,'shortcode_positionsTable']);
        add_shortcode('fulbito_inscripcion', [$this,'shortcode_subscriptionForm']);
        // Subscribe player from frontend subscription form
        add_action('init', [$this, 'subscribePlayer']);
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'fontsAwesome', plugins_url('assets/vendor/fontsawesome/js/all.min.js', __FILE__));
    }

    public function addSingleGameMetadata($content) {
        if(is_admin() || !is_singular('ft_partidos')) return $content;

        $players = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $game = $this->FulbitoDB->getPartido(get_the_ID())[0];
        ob_start();
        $this->ft_get_template('frontend/single-game', ['players' => $players, 'game' => $game]);

        return $content . ob_get_clean();
    }

    public function addListsGameMetadata($content) {
        global $post;
        if(is_admin() || is_singular() || $post->post_type !== 'ft_partidos'  ) return $content;

        $players = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $game = $this->FulbitoDB->getPartido(get_the_ID())[0];
        ob_start();
        $this->ft_get_template('frontend/list-game', ['players' => $players, 'game' => $game]);

        return $content . ob_get_clean();
    }

    public function shortcode_positionsTable() {
        if (!get_query_var('ft_show_profile')) :
            $tabla = $this->FulbitoDB->getTabla();
            ob_start();
            $this->ft_get_template('frontend/shortcodes/positions-table', ['tabla' => $tabla]);
            return ob_get_clean();
        else :
            $playerID = get_query_var('ft_show_profile');
            $jugador_ficha = $this->FulbitoDB->getFichaJugador($playerID);
            ob_start();
            $this->ft_get_template(
                'frontend/shortcodes/player-profile',
                ['jugador_ficha' => $jugador_ficha]
            );
            return ob_get_clean();
        endif;
    }

    public function shortcode_subscriptionForm() {
        // There must be a not-played game
        $prox_partido = $this->FulbitoDB->getPartidoSinJugar();

        if( !empty($prox_partido) ):
            $args = array( 'post_type'=>'ft_partidos', 'p'=>$prox_partido->partidoID );
            $game_query = new WP_Query( $args );
            $players = $this->FulbitoDB->getJugadores( $prox_partido->partidoID, 1 );
            $game = $this->FulbitoDB->getPartido($prox_partido->partidoID)[0];
            ob_start();
            $this->ft_get_template(
                'frontend/shortcodes/subscription-form',
                ['game_query' => $game_query, 'players' => $players, 'game' => $game]
            );
            return ob_get_clean();
        else:
            echo '<p>';
                _e('Todav&iacute;a no se carg&oacute; el pr&oacute;ximo partido, perro!.','fulbito');
            echo '</p>';
        endif;
    }

    public function subscribePlayer() {
        // FIXME fix use of $_POST!!! <- use wp query vars instead
        if(
            isset($_POST['jugador'])
            && isset($_POST['partido'])
            && isset($_POST['inscribir'])
            && isset($_POST['validancia'])
            && !$_POST['validancia']
            && wp_verify_nonce( wp_unslash($_POST['_wpnonce']), 'ft_subscribe_player')
        ):
            $this->FulbitoDB->inscribirJugador( (int)$_POST['jugador'], (int)$_POST['partido'] );
        endif;
    }
}


?>
