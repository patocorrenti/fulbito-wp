(function($){

$(function() {
//FIXME*1: esto debe controlarse server-side
if($('#partido_fields').length){

    var participantes = [];
    calcularParticipantes();
    cargaInicial();

    // EVENTOS //
    $('#seleccionar_favoritos').click(seleccionarFavoritos);
    $('#participantes input[type="checkbox"]').change( calcularParticipantes );
    $('#boton_mezclar').click(mezclarEquipos);
    $('#boton_promediar_simple').click(promediarEquiposSimple);
    $('#boton_promediar').click(promediarEquipos);
    $('#equipos select').change(comboChange);

    function seleccionarFavoritos(){
        $('#participantes input[data-favorito="1"]').prop('checked', true);
        calcularParticipantes();
        return false;
    }

    function calcularParticipantes(){

        var vm = $(this);

        //reseteo participantes
        participantes = [];

        //reviso los check para juntar los participantes
        $('#participantes input[type="checkbox"]').each(function(){
            if( $(this).is( ":checked" ) ){
                //guardo los participantes en el array
                participantes.push( { jugadorID : $(this).attr('value'), nombre : $(this).data('nombre'), promedio : $(this).data('promedio') } );
            }
        });

        //imprimo cantidad de participantes
        $('#participantes_total').html(participantes.length);

        //si son 10 el partido ya es jugable
        var partidoJugable = ( participantes.length === 10 ) ? true : false;

        //habilito los botones de armado si es jugable
        $('#boton_promediar_simple').prop('disabled', !partidoJugable);
        $('#boton_promediar').prop('disabled', !partidoJugable);
        $('#boton_mezclar').prop('disabled', !partidoJugable);

        //reviso los selects de jugadores
        $('#equipos select').each(function(i){

            var vmCombo = $(this);
            //reseteo todo
            vmCombo.html(''); vmCombo.prop('disabled', true);

            //si es jugable....
            if( partidoJugable === true ){

                //populo con opciones
                vmCombo.append('<option value="">jugador '+(i+1)+'</option>');
                $.each(participantes, function(i, participante){
                    vmCombo.append('<option value="'+participante.jugadorID+'">'+participante.nombre+'</option>')
                });

                //activo el selector
                vmCombo.prop('disabled', false);
            }

        });

        //lista de suspender
        //lo agrego si fue checkeado
        if( vm.prop('checked') ){

            if( !$('#suspendidos p[data-id="'+vm.prop('value')+'"]').length ){

                var check_p = $('<p data-id="'+ vm.prop('value') +'">');
                var check_label = $('<label for="suspendido_'+ vm.prop('value') +'">');
                check_p.prepend(check_label);
                var check_input = $('<input name="suspendido['+ vm.prop('value') +']" value="'+ vm.prop('value') +'" type="checkbox" id="suspendido_'+ vm.prop('value') +'">');
                check_label.html( vm.data('nombre') );
                check_label.prepend(check_input);

                $('#suspendidos').append(check_p);

            }

        }else{ //lo quito si fue descheckeado y estaba

            if( $('#suspendidos p[data-id="'+vm.prop('value')+'"]').length ){
                $('#suspendidos p[data-id="'+vm.prop('value')+'"]').remove();
            }

        }

    }

    function cargaInicial(){

        //array de participantes
        var equipoA = [];
        var equipoB = [];

        //cargo equipo A
        $('#participantes input[data-equipo=1]').each(function(){
            equipoA.push($(this).val())
        });

        //cargo equipo B
        $('#participantes input[data-equipo=2]').each(function(){
            equipoB.push($(this).val())
        });

        //asigno a los selects de A
        $('#equipo_a_combo select').each(function(i){
            $(this).val( equipoA[i] );
        });

        //asigno a los selects de B
        $('#equipo_b_combo select').each(function(i){
            $(this).val( equipoB[i] );
        });

    }

    function promediarEquiposSimple(){

        var equipos     = [];
        var team_blanco = [];
        var team_coco   = [];

        //ordeno los participantes de mejor a peor promedio
        participantes.sort( function (a, b) { return (b.promedio - a.promedio) });

        //itero entre los participantes tomandolos en grupos de 2
        var p = 1;
        for( var i = 0; i <= participantes.length; i+=2 ){

            //de los dos que tomo le doy el mas bueno una iteracion a cada equipo distinto
            if(p%2){
                team_coco.push( participantes[i] );
                team_blanco.push( participantes[i+1] );
            }else{
                team_blanco.push( participantes[i] );
                team_coco.push( participantes[i+1] );
            }

            p++;
        }

        console.log( 'blanco: ' + get_promedio_equipo(team_blanco) );
        console.log( 'coco: ' + get_promedio_equipo(team_coco) );

        //guardo solo los id ordenados, blanco primero
        $.each( team_blanco, function(){ equipos.push( $(this)[0]['jugadorID']); } )
        $.each( team_coco, function(){ equipos.push( $(this)[0]['jugadorID']); } )
        equipos = $.grep(equipos,function(n){ return(n); });

        //asigno los valores con ese orden a los combos
        $('#equipos select').each(function(i){
            $(this).val( equipos[i] );
        });

        return false;
    }

    function promediarEquipos(){

        var equipos     = [];
        var team_blanco = [];
        var team_coco   = [];

        //ordeno los participantes de mejor a peor promedio
        participantes.sort( function (a, b) { return (b.promedio - a.promedio) });


        //itero entre los participantes tomandolos en grupos de 2
        for( var i = 0; i <= participantes.length; i+=2 ){

            //de los 2 que tomo le doy el mejor al de peor promedio
            if( get_promedio_equipo( team_blanco ) >= get_promedio_equipo( team_coco ) ){
                team_coco.push( participantes[i] );
                team_blanco.push( participantes[i+1] );
            }
            else{
                team_blanco.push( participantes[i] );
                team_coco.push( participantes[i+1] );
            }
        }

        console.log( 'blanco: ' + get_promedio_equipo(team_blanco) );
        console.log( 'coco: ' + get_promedio_equipo(team_coco) );

        //guardo solo los id ordenados, blanco primero
        $.each( team_blanco, function(){ equipos.push( $(this)[0]['jugadorID']); } )
        $.each( team_coco, function(){ equipos.push( $(this)[0]['jugadorID']); } )
        equipos = $.grep(equipos,function(n){ return(n); });

        //asigno los valores con ese orden a los combos
        $('#equipos select').each(function(i){
            $(this).val( equipos[i] );
        });

        return false;
    }

    //recibe un json de equipo devuelve un promedio de equipo
    function get_promedio_equipo(obj){

        var valor = 0;
        var cantidad = 0;

        $.each(obj, function(){
            if( $(this)[0]['promedio'] ){
                valor += $(this)[0]['promedio'];
                cantidad ++;
            }
        })

        return valor/cantidad;

    }

    function mezclarEquipos(){

        var randomTeam = [];

        //guardo los id de participantes en un array
        $.each(participantes, function(i, item) {
            randomTeam.push(item.jugadorID);
        });

        //lo mezclo
        arrayShuffle(randomTeam);

        //asigno los valores con ese orden a los combos
        $('#equipos select').each(function(i){
            $(this).val( randomTeam[i] );
        });

        return false;
    }

    function comboChange(){

        var vm = $(this);
        var vmValue = vm.prop('value');

        //lo comparo con los otros selects
        $('#equipos select').each(function(i){
            //excepto con este mismo
            if( vm[0] !== $(this)[0] ){
                //si ya estaba seleccionado en otro combo, lo pongo en cero
                if( $(this).prop('value') === vmValue )
                    $(this).val('');
            }
        });

    }

    //reordena un array al azar
    function arrayShuffle( myArray ){
        for(var j, x, i = myArray.length; i; j = Math.floor(Math.random() * i), x = myArray[--i], myArray[i] = myArray[j], myArray[j] = x);
        return myArray;
    }

}
});

})(jQuery);