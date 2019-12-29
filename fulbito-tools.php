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


//FIXME*1: este script deberia cargarse solo al administrar post tipo partidos
function fulbito_tools_scripts() {
    wp_enqueue_script( 'fulbitojs', plugins_url('/js/fulbito-tools.js', __FILE__) , array( 'jquery' ) );
}
add_action( 'admin_init', 'fulbito_tools_scripts' );


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



//crea el post type "Partidos" ---------------------
add_action( 'init', 'add_post_type_partidos' );
function add_post_type_partidos() {

    $labels = array(
                'name'               => _x( 'Partidos', 'post type general name'),
                'singular_name'      => _x( 'Partido', 'post type singular name'),
                'menu_name'          => _x( 'Partidos', 'admin menu'),
                'name_admin_bar'     => _x( 'Partidos', 'add new on admin bar'),
                'add_new'            => __( 'A&ntilde;adir nuevo'),
                'add_new_item'       => __( 'A&ntilde;adir nuevo partido'),
                'new_item'           => __( 'Nueva partido'),
                'edit_item'          => __( 'Editar partido'),
                'view_item'          => __( 'Ver partido'),
                'search_items'       => __( 'Buscar partido'),
                'not_found'          => __( 'No se encontraron partidos.')
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


/* Solo con ACF instalado */
if(function_exists("register_field_group"))
{
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
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array (
            'position' => 'acf_after_title',
            'layout' => 'no_box',
            'hide_on_screen' => array (
            ),
        ),
        'menu_order' => 0,
    ));
}



?>
