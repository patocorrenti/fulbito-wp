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

        add_action( 'rest_api_init', [$this, 'registerRouteTable']);
    }

    public function registerRouteTable() {
      register_rest_route( $this->baseUri, '/table', array(
        'methods' => 'GET',
        'callback' => [$this, 'getTable'],
      ) );
    }

    public function getTable() {
        return $this->FulbitoDB->getTabla();
    }

}
