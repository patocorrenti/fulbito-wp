<?php
/*
Plugin Name: Fulbito
Plugin URI:
Description: Herramientas para tu Fulbito 5
Version: 0.6
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

require_once( __DIR__ . '/vendor/autoload.php');

use PCorrenti\Fulbito\DB;
use PCorrenti\Fulbito\Admin;
use PCorrenti\Fulbito\Frontend;
use PCorrenti\Fulbito\APIExtend;

$FulbitoDB = new DB();
new Admin($FulbitoDB);
new Frontend($FulbitoDB);

if (get_option('enable_api') === 'on') {
    new APIExtend($FulbitoDB);
}

?>
