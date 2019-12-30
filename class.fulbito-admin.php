<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FulbitoAdmin Class
 *
 *
 */

class FulbitoAdmin extends FulbitoCommons {

    var $FulbitoDB;

    // Fulbito DB class instance needs to be inyected
    function FulbitoAdmin($FulbitoDB) {

        $this->FulbitoDB = $FulbitoDB;

        // Enable Plugin
        register_activation_hook( __FILE__, [$this, 'fulbito_tools_activate']);
        // Disable Plugin
        register_deactivation_hook( __FILE__, [$this, 'fulbito_tools_deactivate']);

        // Register query vars
        add_filter( 'query_vars', [$this, 'registerQueryVars']);

        // Create post type partidos
        add_action( 'init', [$this, 'registerPostTypePartidos']);
        // Add settings page on admin menu
        add_action('admin_menu', [$this, 'addSettingsPage']);
        // Add custom fields to partidos custom post
        add_action('edit_form_after_title', [$this,'addGameForm']);
        // Enqueue administration JS files
        add_action('admin_init', [$this,'enqueueAdminScripts']);
        // Save metadata for partidos
        add_action('save_post', [$this, 'saveGameMetadata']);
        // Delete metadata for partidos
        add_action('delete_post', [$this, 'deleteGameMetadata']);
    }

    public function fulbito_tools_activate() {
        $this->FulbitoDB->install();
    }

    function fulbito_tools_deactivate() {
        $this->FulbitoDB->uninstall();
    }

    public function registerQueryVars($vars) {
        $vars[] = 'ft_show_profile';
        return $vars;
    }

    public function registerPostTypePartidos() {
        $labels = array(
                    'name'               => _x( 'Partidos', 'post type general name', 'fulbito' ),
                    'singular_name'      => _x( 'Partido', 'post type singular name', 'fulbito' ),
                    'menu_name'          => _x( 'Partidos', 'admin menu', 'fulbito' ),
                    'name_admin_bar'     => _x( 'Partidos', 'add new on admin bar', 'fulbito' ),
                    'add_new'            => __( 'A&ntilde;adir partido', 'fulbito' ),
                    'add_new_item'       => __( 'A&ntilde;adir nuevo partido', 'fulbito' ),
                    'new_item'           => __( 'Nueva partido', 'fulbito' ),
                    'edit_item'          => __( 'Editar partido', 'fulbito' ),
                    'view_item'          => __( 'Ver partido', 'fulbito' ),
                    'search_items'       => __( 'Buscar partido', 'fulbito' ),
                    'not_found'          => __( 'No se encontraron partidos.', 'fulbito' )
                );
        $args = array(
                    'labels'             => $labels,
                    'public'             => true,
                    'publicly_queryable' => true,
                    'show_ui'            => true,
                    'show_in_menu'       => true,
                    'query_var'          => true,
                    'rewrite'            => array( 'slug' => 'partidos' ),
                    'has_archive'        => true,
                    'hierarchical'       => false,
                    'menu_position'      => 4,
                    'menu_icon'          => 'dashicons-sos',
                    'supports'           => array( 'title', 'editor')
                );
        register_post_type( 'ft_partidos', $args );
    }

    public function addSettingsPage() {
        add_menu_page(
            __('Fulbito Settings', 'fulbito'), // Page title
            __('Fulbito', 'fulbito'), // Menu title
            'administrator', // User role
            'fulbito-tools', // Page slug
            array($this, 'settingsPageTemplate'), //function
            'dashicons-admin-generic', // Icon_url
            6 // Position
        );
    }

    public function settingsPageTemplate() {
        // Players edition
        // FIXME reading the $_POST data is not OK!!! -> use wp query vars instead
        if( isset($_POST['editar_jugadores']) && $_POST['editar_jugadores'] == 'Guardar cambios' )
            $this->FulbitoDB->editJugadores($_POST);

        // FIXME reading the $_POST data is not OK!!! -> use wp query vars instead
        if( isset($_POST['regenerar_tabla']) && $_POST['regenerar_tabla'] )
            $this->FulbitoDB->regenerarTablas();

        // FIXME reading the $_POST data is not OK!!! -> use wp query vars instead
        if( isset($_POST['migrar_fechas']) && $_POST['migrar_fechas'] )
            $this->FulbitoDB->datesACF2Fulbito();

        // Show players list
        $players = $this->FulbitoDB->getJugadores();
        $this->ft_get_template('admin/list-players', ['players' => $players]);
    }

    public function addGameForm($post){
        global $post;
        if ($post->post_type !== 'ft_partidos') return;

        $players = $this->FulbitoDB->getJugadores($post->ID);
        $game = $this->FulbitoDB->getPartido($post->ID)[0];

        $this->ft_get_template('admin/games-form', ['players' => $players, 'game' => $game]);
    }

    function enqueueAdminScripts() {
        wp_enqueue_script( 'fulbitojs', plugins_url('assets/js/fulbito-tools.js', __FILE__) , array( 'jquery' ) );
    }

    public function saveGameMetadata($postId) {
        global $post;
        if ($post->post_type != 'ft_partidos') return;

        // FIXME reading the $_POST data is not OK!!! -> use wp query vars instead
        $this->FulbitoDB->salvarPartido( $postId, $_POST );
    }

    public function deleteGameMetadata( $postId ) {
        global $post;
        if ($post->post_type != 'ft_partidos') return;

        $this->FulbitoDB->deletePartido( $postId );
    }
}
