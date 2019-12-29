<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FulbitoAdmin Class
 *
 *
 */

class FulbitoAdmin {

    var $FulbitoDB;

    // Fulbito DB class instance needs to be inyected
    function FulbitoAdmin($FulbitoDB) {

        $this->FulbitoDB = $FulbitoDB;

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

    public function registerPostTypePartidos() {
        $labels = array(
                    'name'               => _x( 'Partidos', 'post type general name', 'fulbito' ),
                    'singular_name'      => _x( 'Partido', 'post type singular name', 'fulbito' ),
                    'menu_name'          => _x( 'Partidos', 'admin menu', 'fulbito' ),
                    'name_admin_bar'     => _x( 'Partidos', 'add new on admin bar', 'fulbito' ),
                    'add_new'            => __( 'A&ntilde;adir nuevo', 'fulbito' ),
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

        // Add Data field if ACF plugin is installed
        // TODO Make this plugin independent form ACF
        if (function_exists("register_field_group")) :
            register_field_group(array (
                'id' => 'acf_partidos',
                'title' => 'Partidos',
                'fields' => array (
                    array (
                        'key' => 'field_56afac78c06b5',
                        'label' => 'Fecha',
                        'name' => 'fecha',
                        'type' => 'date_picker',
                        'required' => 1,
                        'date_format' => 'yymmdd',
                        'display_format' => 'dd/mm/yy',
                        'first_day' => 1,
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'ft_partidos',
                        ),
                    ),
                ),
                'options' => array (
                    'position' => 'acf_after_title',
                    'layout' => 'no_box',
                ),
                'menu_order' => 0,
            ));
        endif;
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

        // Show players list
        $players = $this->FulbitoDB->getJugadores();
        require_once('views/admin/html-players-list.php');
    }

    public function addGameForm($post){
        global $post;
        if ($post->post_type !== 'ft_partidos') return;

        $players = $this->FulbitoDB->getJugadores($post->ID);
        $game = $this->FulbitoDB->getPartido($post->ID)[0];

        include_once('views/admin/html-games-form.php');
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
