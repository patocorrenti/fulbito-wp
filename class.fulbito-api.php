<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FulbitoAPI Class
 *
 *
 */

class FulbitoAPI {

    var $FulbitoDB;
    var $baseUri;

    // Fulbito DB class instance needs to be inyected
    function FulbitoAPI($FulbitoDB) {
        $this->FulbitoDB = $FulbitoDB;
        $this->baseUri = 'fulbito/v1';

        // Register routes
        add_action( 'rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerSettings() {
       register_setting( 'fulbito_settings_api', 'enable_api');
    }

    public function registerRoutes() {
        // Table
        register_rest_route( $this->baseUri, '/table', array(
            'methods' => 'GET',
            'callback' => [$this, 'getTable'],
        ) );
        // Players
        register_rest_route( $this->baseUri, '/player/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'getPlayerProfile'],
        ) );

    }

    public function getTable() {
        $table = $this->FulbitoDB->getTabla();
        if ( empty( $table ) ) {
            return new WP_Error( 'no_table', 'No table', array( 'status' => 404 ) );
        }
        return $table;
    }

    public function getPlayerProfile($data) {
        $profile = $this->FulbitoDB->getFichaJugador($data['id']);
        if ( empty( $profile ) ) {
            return new WP_Error( 'no_player', 'Player dont exists', array( 'status' => 404 ) );
        }
        return $profile;
    }

}
