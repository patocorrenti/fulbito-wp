<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FulbitoDB Class
 *
 * Handles the plugin tables and SQL queries
 */

class FulbitoDB {

    var $wpdb;
    var $tables = array();

    function FulbitoDB() {
        // Get global object to work with DB
        $this->wpdb = $GLOBALS['wpdb'];
        // Table names
        $this->tables['jugadores'] = $this->wpdb->prefix . 'fulbito_tools_jugadores';
        $this->tables['equipos'] = $this->wpdb->prefix . 'fulbito_tools_equipos';
        $this->tables['partidos'] = $this->wpdb->prefix . 'fulbito_tools_partidos';
        $this->tables['tabla'] = $this->wpdb->prefix . 'fulbito_tools_tabla';
    }

    function install(){

        // Create plugin tables

        // Players
        $sql = sprintf(
            'CREATE TABLE %s ( id INT NOT NULL AUTO_INCREMENT , nombre VARCHAR(100) , email VARCHAR(255) , favorito INT(1) NOT NULL, activo INT(1) NOT NULL , PRIMARY KEY (id) );',
            $this->tables['jugadores']
        );
        $this->wpdb->query($sql);

        // Teams
        $sql = sprintf(
            'CREATE TABLE %s ( partidoID BIGINT(20) NOT NULL , jugadorID INT(11) NOT NULL , equipo INT(1), suspendido INT(1), PRIMARY KEY (partidoID, jugadorID) );',
            $this->tables['equipos']
        );
        $this->wpdb->query($sql);

        // Games
        $sql = sprintf(
            'CREATE TABLE %s ( partidoID BIGINT(20) NOT NULL , resultado INT(1), color_equipo_1 INT(6), color_equipo_2 INT(6), PRIMARY KEY (partidoID) );',
            $this->tables['partidos']
        );
        $this->wpdb->query($sql);

        // Positions table
        $sql = sprintf(
            'CREATE TABLE %s ( partidoID BIGINT(20) NOT NULL, jugadorID INT NOT NULL, jugados INT(5), ganados INT(5), empatados INT(5), perdidos INT(5), puntos INT(10), promedio FLOAT(10) );',
            $this->tables['tabla']
        );
        $this->wpdb->query($sql);

        // Regenerate tables (in case there are played games)
        $this->regenerarTablas();
    }

    function uninstall(){
        // Delete the tables
        foreach($this->tables as $table):
            $sql = sprintf( "DROP TABLE %s", $table );
            //$this->wpdb->query($sql);
        endforeach;
    }

    // Edit players
    function editJugadores($form_post){
        foreach($form_post['jugadores'] as $key=>$jugador_data):
            $this->wpdb->update( $this->tables['jugadores'], $jugador_data, array( 'id' => $key ) );
        endforeach;
    }

    // salva un partido (disparado por un save de post type partido)
    function salvarPartido( $postID, $post ){

        //Guardo los jugadores en cada equipo o como participantes
        if( count($post['participantes']) ):

            $idRegistrado = array();

            $jugadoresEquipoA = ( $post['jugadoresEquipoA'] ) ? $post['jugadoresEquipoA'] : array();
            $jugadoresEquipoB = ( $post['jugadoresEquipoB'] ) ? $post['jugadoresEquipoB'] : array();


            foreach( $post['participantes'] as $jugador ):

                $suspendido = ( $post['suspendido'][$jugador] ) ? 1 : 0;

                $equipo = 0;
                if( in_array( $jugador, $jugadoresEquipoA ) ) $equipo = 1;
                if( in_array( $jugador, $jugadoresEquipoB ) ) $equipo = 2;

                $this->wpdb->replace( $this->tables['equipos'], array( 'partidoID' => $postID, 'jugadorID' => (int)$jugador, 'equipo' => (int)$equipo, 'suspendido' => $suspendido  ) );

                $idRegistrados[] = (int)$jugador;

            endforeach;

        endif;

        //Borro cualquier jugador que no me hayan enviado
        $participantesStr = (is_array($post['participantes'])) ? implode( ',', $post['participantes'] ) : '0';
        $sql = sprintf( 'DELETE FROM %s WHERE jugadorID NOT IN (%s) AND partidoID = %d', $this->tables['equipos'],  $participantesStr, $postID );
        $this->wpdb->query($sql);

        //Guardo los metadatos del partido
        $this->wpdb->replace( $this->tables['partidos'], array( 'partidoID' => $postID, 'resultado' => (int)$post['resultado'] ) );

        //Actualizo la tabla de posiciones
        $this->salvarTabla($postID);

    }

    //elimina un partido
    function deletePartido($post_id){
        $this->wpdb->delete( $this->tables['equipos'] , array( 'partidoID' => (int)$post_id ) );
        $this->wpdb->delete( $this->tables['partidos'] , array( 'partidoID' => (int)$post_id ) );
    }

    function inscribirJugador( $jugadorID, $partidoID ){
        $this->wpdb->insert(
                        $this->tables['equipos'],
                        array( 'partidoID' => $partidoID , 'jugadorID' => $jugadorID, 'suspendido' => 0 ),
                        array( '%d', '%d' )
                    );
    }


    // GETTERS ---------------------------------------------------------

    // trae todos los jugadores (para admin) o si se le pasa un ID lo hace en relacion a un partido, se le puede pedir que ordene por promedio
    function getJugadores($partidoID=false, $order_promedio=false){

        $order_promedio = ( $order_promedio ) ? 'tabla.promedio DESC,' : '';

        if($partidoID)
            $sql = sprintf(" SELECT jugador.id, jugador.nombre, jugador.email, jugador.favorito, jugador.lesion, jugador.activo, partido.suspendido, partido.equipo, IF(partido.partidoID,1,0) AS participa, tabla.promedio
                                FROM %s AS jugador
                                LEFT JOIN %s AS partido ON partido.jugadorID = jugador.id AND partido.partidoID = %d
                                LEFT JOIN %s AS tabla ON tabla.jugadorID = jugador.id AND tabla.partidoID = %d
                                GROUP BY jugador.id
                                ORDER BY %s jugador.nombre ASC;",
                                $this->tables['jugadores'], $this->tables['equipos'], $partidoID, $this->tables['tabla'], $partidoID, $order_promedio
                            );
        else
            $sql = sprintf( "SELECT id, nombre, email, favorito, activo FROM %s ORDER BY favorito DESC, nombre ASC;", $this->tables['jugadores'] );

        $results = $this->wpdb->get_results($sql);

        return $results;

    }

    function getFichaJugador($jugadorID){

        /* data basica */
        $sql = sprintf('SELECT nombre, favorito, lesion FROM %s WHERE id = %d;', $this->tables['jugadores'], $jugadorID);
        $result = $this->wpdb->get_results($sql);
        $return['datos'] = $result[0];

        /* data de partidos */
        $sql = sprintf('SELECT
                                COUNT(partidoID) jugados,
                                COUNT(NULLIF(suspendido!=1,1)) suspensiones,
                                COUNT(NULLIF(equipo!=1,1)) blanco,
                                COUNT(NULLIF(equipo!=2,1)) coco
                                FROM %s WHERE jugadorID = %d;'
                                ,$this->tables['equipos'], $jugadorID
                        );
        $result = $this->wpdb->get_results($sql);
        $return['partidos'] = $result[0];

        /* data de jugadores relacionados */
        $sql = sprintf('SELECT
                                eq2.jugadorID id, jug.nombre nombre, COUNT(*) AS cantidad
                                FROM %s eq1
                                JOIN %s eq2 ON eq1.partidoID = eq2.partidoID
                                AND eq1.equipo = eq2.equipo
                                AND eq1.jugadorID <> eq2.jugadorID
                                JOIN %s jug ON jug.id = eq2.jugadorID
                                WHERE eq1.jugadorID = %d
                                GROUP BY eq2.jugadorID
                                ORDER BY cantidad DESC;'
                                ,$this->tables['equipos'], $this->tables['equipos'], $this->tables['jugadores'], $jugadorID
                        );
        $result = $this->wpdb->get_results($sql);
        $return['relacionados'] = $result;

        return $return;

    }

    function getTotalPartidos(){
        $sql = sprintf('SELECT COUNT(partidoID) as total FROM %s', $this->tables['partidos']);
        $results = $this->wpdb->get_results($sql);
        return $results[0]->total;
    }

    function getPartido($partidoID){

        $sql = sprintf( 'SELECT resultado, color_equipo_1, color_equipo_2 FROM %s WHERE partidoID = %d', $this->tables['partidos'], $partidoID );
        $results = $this->wpdb->get_results($sql);
        return $results;
    }

    function getPartidoSinJugar(){

        $sql = sprintf( 'SELECT partidoID FROM %s WHERE resultado = 0 LIMIT 1', $this->tables['partidos'] );
        $results = $this->wpdb->get_results($sql);
        return $results[0];
    }

    //calcula la tabla de posiciones hasta el partido que le paso y la guarda en la tabla cache
    function salvarTabla($partidoID){

        //borra los datos existentes hasta entonces de tabla de ese partido
        $this->wpdb->delete( $this->tables['tabla'] , array( 'partidoID' => (int)$partidoID ) );

        //carga los nuevos datos de tabla de ese partido
        $sql = sprintf("
                            INSERT INTO %s ( partidoID, jugadorID, jugados, ganados, empatados, perdidos, puntos, promedio )
                            SELECT
                            %d
                            ,jugador.id
                            ,COUNT(jugador.id) jugados
                            ,COUNT(NULLIF(0, (equipo.equipo = partido.resultado))) ganados
                            ,COUNT(NULLIF(0, (partido.resultado = 3))) empatados
                            ,COUNT(NULLIF(0, (equipo.equipo != partido.resultado AND partido.resultado != 3))) perdidos
                            ,((COUNT(NULLIF(0, (equipo.equipo = partido.resultado)))*3)+(COUNT(NULLIF(0, (partido.resultado = 3))))) puntos
                            /* promedio */
                            ,IF(
                                /* condicion (no cuenta promedio gente con menos de 10 jugados) */
                                COUNT( jugador.id) >= 10,
                                /* true */
                                ROUND(
                                    (((COUNT(NULLIF(0, (equipo.equipo = partido.resultado)))*3)+(COUNT(NULLIF(0, (partido.resultado = 3))))) * 100) / (COUNT(jugador.id)*3)
                                    ,1),
                                /* false */
                                NULL
                            ) promedio
                            FROM %s jugador
                            LEFT JOIN %s equipo ON jugador.id = equipo.jugadorID
                            LEFT JOIN %s partido ON equipo.partidoID = partido.partidoID
                            WHERE partido.resultado != 0 AND partido.partidoID <= %d
                            GROUP BY jugador.id
                            ORDER BY jugados DESC, puntos DESC, ganados DESC;",
                            $this->tables['tabla'], $partidoID, $this->tables['jugadores'], $this->tables['equipos'], $this->tables['partidos'], $partidoID
        );

        $this->wpdb->query($sql);

    }

    //obtiene la tabla de la temporada (ultima fecha), se le puede pedir que la calcule en caliente o que la obtenga de la tabla donde se cachea
    function getTabla($calcular=false){

        if( !$calcular ):

            $sql = sprintf("    SELECT
                                jugador.id, jugador.nombre, jugador.lesion, jugador.favorito, tabla.jugados, tabla.ganados, tabla.empatados, tabla.perdidos, tabla.puntos, tabla.promedio
                                FROM %s tabla
                                LEFT JOIN %s jugador ON jugador.id = tabla.jugadorID
                                WHERE tabla.partidoID = ( SELECT MAX( partidoID ) FROM %s ) AND tabla.jugados > 9
                                ORDER BY tabla.jugados DESC, tabla.puntos DESC, tabla.ganados DESC",
                                $this->tables['tabla'], $this->tables['jugadores'], $this->tables['tabla']
                    );

        else :

            $sql = sprintf("    SELECT
                                jugador.id
                                ,jugador.nombre
                                ,COUNT(jugador.id) jugados
                                ,COUNT(NULLIF(0, (equipo.equipo = partido.resultado))) ganados
                                ,COUNT(NULLIF(0, (partido.resultado = 3))) empatados
                                ,COUNT(NULLIF(0, (equipo.equipo != partido.resultado AND partido.resultado != 3))) perdidos
                                ,((COUNT(NULLIF(0, (equipo.equipo = partido.resultado)))*3)+(COUNT(NULLIF(0, (partido.resultado = 3))))) puntos
                                /* promedio */
                                ,IF(
                                    /* condicion (no cuenta promedio gente con menos de 10 jugados) */
                                    COUNT( jugador.id) >= 10,
                                    /* true */
                                    ROUND(
                                        (((COUNT(NULLIF(0, (equipo.equipo = partido.resultado)))*3)+(COUNT(NULLIF(0, (partido.resultado = 3))))) * 100) / (COUNT(jugador.id)*3)
                                        ,1),
                                    /* false */
                                    NULL
                                ) promedio
                                FROM %s jugador
                                LEFT JOIN %s equipo ON jugador.id = equipo.jugadorID
                                LEFT JOIN %s partido ON equipo.partidoID = partido.partidoID
                                WHERE partido.resultado != 0
                                GROUP BY jugador.id
                                ORDER BY jugados DESC, puntos DESC, ganados DESC;",
                                $this->tables['jugadores'], $this->tables['equipos'], $this->tables['partidos']
            );

        endif;

        $results = $this->wpdb->get_results($sql);
        return $results;
    }

    /* regenera todas las tablas cacheadas partido a partido */
    function regenerarTablas(){

        $sql = sprintf( 'SELECT partidoID id FROM %s;', $this->tables['partidos'] );
        $results = $this->wpdb->get_results($sql);

        foreach($results as $r)
            $this->salvarTabla($r->id);
    }


}


?>
