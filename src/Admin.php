<?php
namespace PCorrenti\Fulbito;

use PCorrenti\Fulbito\Commons;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin extends Commons {

    var $FulbitoDB;

    // Fulbito DB class instance needs to be inyected
    function __construct($FulbitoDB) {

        $this->FulbitoDB = $FulbitoDB;

        // Enable Plugin
        register_activation_hook( __FILE__, [$this, 'fulbito_tools_activate']);
        // Disable Plugin
        register_deactivation_hook( __FILE__, [$this, 'fulbito_tools_deactivate']);

        // Register query vars
        add_filter( 'query_vars', [$this, 'registerQueryVars']);

        // Register settings
        add_action( 'admin_init', [$this, 'registerSettings']);

        // Create post type partidos
        add_action( 'init', [$this, 'registerPostTypePartidos']);
        // Disable Gutenberg Editor for partidos
        add_filter( 'use_block_editor_for_post_type', [$this,'disableGutenberg'], 10, 2);
        // Add players page on admin menu
        add_action('admin_menu', [$this, 'addPlayersPage']);
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

    public function registerSettings() {
       register_setting( 'fulbito_settings', 'enable_api');
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
                    'show_in_rest'       => true,
                    'rest_base'          => 'games',
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

    public function disableGutenberg( $current_status, $post_type ) {
        // Disabled post types
        $disabled_post_types = array( 'ft_partidos');
        // Change $can_edit to false for any post types in the disabled post types array
        if ( in_array( $post_type, $disabled_post_types, true ) ) {
            $current_status = false;
        }
        return $current_status;
    }

    public function addPlayersPage() {
        add_submenu_page(
            'edit.php?post_type=ft_partidos',
            __('Jugadores | Fulbito', 'fulbito'), // Page title
            __('Jugadores', 'fulbito'), // Menu title
            'edit_posts', // Capability
            'players', // Page slug
            array($this, 'playersPage'), //function
            4 // Position
        );
    }

    public function playersPage() {
        // Add new player
        if(
            !empty($_POST['ft_action'])
            && $_POST['ft_action'] === 'ft_add_player'
            && strlen($_POST['nombre']) > 2
            && wp_verify_nonce( wp_unslash($_POST['_wpnonce']), 'ft_add_player')
        )
            $this->FulbitoDB->addJugador(esc_sql($_POST['nombre']));

        // Players edition
        if(
            !empty($_POST['ft_action'])
            && $_POST['ft_action'] === 'ft_edit_players'
            && wp_verify_nonce( wp_unslash($_POST['_wpnonce']), 'ft_edit_players')
        )
            $this->FulbitoDB->editJugadores($_POST);

        // Show players list
        $players = $this->FulbitoDB->getJugadores();
        $this->ft_get_template('admin/players', ['players' => $players]);
    }

    public function addSettingsPage() {
        add_submenu_page(
            'edit.php?post_type=ft_partidos',
            __('Ajustes | Fulbito', 'fulbito'), // Page title
            __('Ajustes', 'fulbito'), // Menu title
            'edit_posts', // Capability
            'options', // Page slug
            array($this, 'settingsPage'), //function
            5 // Position
        );
    }

    public function settingsPage() {
        // Table regeneration
        if(
            !empty($_POST['ft_action'])
            && $_POST['ft_action'] === 'ft_regenerar_tabla'
            && wp_verify_nonce( wp_unslash($_POST['_wpnonce']), 'ft_regenerar_tabla')
        )
            $this->FulbitoDB->regenerarTablas();

        // Show players list
        $players = $this->FulbitoDB->getJugadores();
        $this->ft_get_template('admin/settings', ['players' => $players]);
    }

    public function addGameForm($post){
        global $post;
        if ($post->post_type !== 'ft_partidos') return;

        $players = $this->FulbitoDB->getJugadores($post->ID);
        $game = $this->FulbitoDB->getPartido($post->ID)[0];

        $this->ft_get_template('admin/games-form', ['players' => $players, 'game' => $game]);
    }

    public function saveGameMetadata($postId) {
        global $post;

        if (
            empty($post)
            || $post->post_type != 'ft_partidos'
            || empty($_POST['ftnonce'])
        )
            return;

        // FIXME ADD NONCE
        if( wp_verify_nonce( wp_unslash($_POST['ftnonce']), 'ft_game_metadata') )
            $this->FulbitoDB->salvarPartido( $postId, $_POST );
    }

    function enqueueAdminScripts() {
        wp_enqueue_script( 'fulbitojs', plugins_url('../assets/js/fulbito-tools.js', __FILE__) , array( 'jquery' ) );
    }

    public function deleteGameMetadata( $postId ) {
        global $post;
        if ($post->post_type != 'ft_partidos') return;

        $this->FulbitoDB->deletePartido( $postId );
    }
}


