<?php

// Menu en la administracion ------------------------------------------
add_action('admin_menu', 'admin_menu_add');
function admin_menu_add(){
    add_menu_page(
        'Fulbito Tools',//Titulo de pagina
        'Fulbito',//Titulo en menu
        'administrator',//Rol que puede acceder
        'fulbito-tools',//Slug de la pagina
        'admin_menu_template',//function
        'dashicons-admin-generic',//icon_url
        6//position
    );
}
function admin_menu_template(){

    global $fulbito_data;

    //edicion de jugadores
    if( isset($_POST['editar_jugadores']) && $_POST['editar_jugadores'] == 'Guardar cambios' )
        $fulbito_data->editJugadores($_POST);

    if( isset($_POST['regenerar_tabla']) && $_POST['regenerar_tabla'] )
        $fulbito_data->regenerarTablas();

    //mostrar lista de jugadores
    $jugadores = $fulbito_data->getJugadores();
    include_once('templates/admin-jugadores.html');

}


/*
    PARTIDOS ---------------------------------------------------------------------------------------------------------
*/

// EDITAR partido ------------------------------------------
add_action('edit_form_after_title', 'add_partidos_custom_fields');
function add_partidos_custom_fields( $post ){

    global $post;
    if ($post->post_type != 'ft_partidos') return;

    global $fulbito_data;
    $jugadores = $fulbito_data->getJugadores($post->ID);
    $partido = $fulbito_data->getPartido($post->ID);
    $partido = $partido[0];

    include_once('templates/edit-partido.html');
}

// SALVAR partido ------------------------------------------
function salvar_partido_meta( $post_id ) {

    global $post;
    if ($post->post_type != 'ft_partidos') return;

    global $fulbito_data;
    //FIXME*2 obviamente esto de leer el $_POST no puede estar bien
    $fulbito_data->salvarPartido( $post_id, $_POST );
}
add_action( 'save_post', 'salvar_partido_meta' );

// BORRAR partido ------------------------------------------
function eliminar_partido_meta( $post_id ) {

    global $post;
    if ($post->post_type != 'ft_partidos') return;

    global $fulbito_data;
    $fulbito_data->deletePartido( $post_id );

}
add_action( 'delete_post', 'eliminar_partido_meta' );



?>