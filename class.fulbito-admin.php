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

        // Add settings page on admin menu
        add_action('admin_menu', [$this, 'addSettingsPage']);
        // Add custom fields to partidos custom post
        add_action('edit_form_after_title', [$this,'addGameForm']);
        // Save metadata for partidos
        add_action('save_post', [$this, 'saveGameMetadata']);
        // Delete metadata for partidos
        add_action('delete_post', [$this, 'deleteGameMetadata']);

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
        //FIXME reading the $_POST data is not OK!!! -> use wp query vars instead
        if( isset($_POST['editar_jugadores']) && $_POST['editar_jugadores'] == 'Guardar cambios' )
            $this->FulbitoDB->editJugadores($_POST);

        //FIXME reading the $_POST data is not OK!!! -> use wp query vars instead
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

    public function saveGameMetadata($postId) {
        global $post;
        if ($post->post_type != 'ft_partidos') return;

        //FIXME reading the $_POST data is not OK!!! -> use wp query vars instead
        $this->FulbitoDB->salvarPartido( $postId, $_POST );
    }

    public function deleteGameMetadata( $postId ) {
        global $post;
        if ($post->post_type != 'ft_partidos') return;

        $this->FulbitoDB->deletePartido( $postId );
    }
}
