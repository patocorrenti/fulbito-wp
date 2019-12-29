<?php
/*
Plugin Name: Fulbito Tools
Plugin URI:
Description: Herramientas para tu fulbito 5
Version: 0.01
Author: Pato Correnti
Author URI: http://patocorrenti.com
License: GPL2
Text Domain: fulbito
Domain Path: /i18n/languages/

Copyright 2016 PatoCorrenti  (email : patocorrenti@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

require_once( 'class.fulbito-db.php' );
require_once('class.fulbito-admin.php');

global $fulbito_data;
$fulbito_data = new FulbitoDB();
$fulbito_admin = new FulbitoAdmin($fulbito_data);

//ACTIVAR plugin ---------------------
register_activation_hook( __FILE__, 'fulbito_tools_activate' );
function fulbito_tools_activate(){
    global $fulbito_data;
    $fulbito_data->install();
}

//DESACTIVAR plugin ---------------------
register_deactivation_hook( __FILE__, 'fulbito_tools_deactivate' );
function fulbito_tools_deactivate(){
    global $fulbito_data;
    $fulbito_data->uninstall();
}

include_once('hooks-frontend.php');


?>
