<?php
namespace PCorrenti\Fulbito;

use PCorrenti\Fulbito\Commons;

if ( ! defined( 'ABSPATH' ) ) exit;

class Frontend extends Commons {

    var $FulbitoDB;

    // Fulbito DB class instance needs to be inyected
    function __construct($FulbitoDB) {

        $this->FulbitoDB = $FulbitoDB;

        add_action( 'wp_enqueue_scripts', [$this,'enqueue_scripts']);

        // Game - single - Add metadata
        add_filter( 'the_content', [$this, 'addSingleGameMetadata']);
        // Game - lists - Add metadata
        add_filter( 'the_content', [$this, 'addListsGameMetadata']);
        add_filter('get_the_excerpt', [$this, 'forceFullContent'], 10, 2);
        // Add shortcodes
        add_shortcode('fulbito_tabla', [$this,'shortcode_positionsTable']);
        add_shortcode('fulbito_inscripcion', [$this,'shortcode_subscriptionForm']);
        // Subscribe player from frontend subscription form
        add_action('init', [$this, 'subscribePlayer']);
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'fontsAwesome', plugins_url('../assets/vendor/fontsawesome/js/all.min.js', __FILE__));
    }

    public function addSingleGameMetadata($content) {
        if(is_admin() || !is_singular('ft_partidos')) return $content;

        $players = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $game = $this->FulbitoDB->getPartido(get_the_ID())[0];
        
        return $content . $this->ft_template('frontend/single-game', ['players' => $players, 'game' => $game]);
    }

    public function addListsGameMetadata($content) {
        global $post;
        if(is_admin() || is_singular() || $post->post_type !== 'ft_partidos'  ) return $content;

        $players = $this->FulbitoDB->getJugadores(get_the_ID(), 1);
        $game = $this->FulbitoDB->getPartido(get_the_ID())[0];

        return $content . $this->ft_template('frontend/list-game', ['players' => $players, 'game' => $game]);
    }

    public function forceFullContent($excerpt, $post) {
        if ($post->post_type === 'ft_partidos') {
            return apply_filters('the_content', $post->post_content);
        }
        return $excerpt;
    }

    public function shortcode_positionsTable() {
        if (!get_query_var('ft_show_profile')) :
            $tabla = $this->FulbitoDB->getTabla();

            return $this->ft_template('frontend/shortcodes/positions-table', ['tabla' => $tabla]);
        else :
            $playerID = get_query_var('ft_show_profile');
            $jugador_ficha = $this->FulbitoDB->getFichaJugador($playerID);
            
            return $this->ft_template(
                'frontend/shortcodes/player-profile',
                ['jugador_ficha' => $jugador_ficha]
            );
        endif;
    }

    public function shortcode_subscriptionForm() {
        // There must be a not-played game
        $prox_partido = $this->FulbitoDB->getPartidoSinJugar();

        if( !empty($prox_partido) ):
            $args = array( 'post_type'=>'ft_partidos', 'p'=>$prox_partido->partidoID );
            $game_query = new \WP_Query( $args );
            $players = $this->FulbitoDB->getJugadores( $prox_partido->partidoID, 1 );
            $game = $this->FulbitoDB->getPartido($prox_partido->partidoID)[0];

            return $this->ft_template(
                'frontend/shortcodes/subscription-form',
                ['game_query' => $game_query, 'players' => $players, 'game' => $game]
            );
            
        else:
            return $this->ft_template('frontend/shortcodes/no-game');
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
