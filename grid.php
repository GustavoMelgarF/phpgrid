<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $parametros = array();

    $filtro = false;

    $tabla = "";

    $filas_pagina = 10;

    $desde = 0;
	
	$campo_ordenar = '';
	
	$orden_ordenar = '';

    $sustituciones = array();

    $campos_select = array();

    if (isset($_REQUEST['tabla']))
        $tabla = $_REQUEST['tabla'];

    if (isset($_REQUEST['filtro']) && $_REQUEST['filtro'] == 1)
        $filtro = true;

    if (isset($_REQUEST['campos_mostrar']))
        $campos_mostrar = json_decode($_REQUEST['campos_mostrar']);

    if (isset($_REQUEST['filas_pagina']))
        $filas_pagina = $_REQUEST['filas_pagina'];

    if (isset($_REQUEST['desde']))
        $desde = $_REQUEST['desde'];
		
	if (isset($_REQUEST['campo_ordenar']))
        $campo_ordenar = $_REQUEST['campo_ordenar'];
		
	if (isset($_REQUEST['orden_ordenar']))
        $orden_ordenar = $_REQUEST['orden_ordenar'];

    if (isset($_REQUEST['sustituciones'])){
        $sustituciones = json_decode($_REQUEST['sustituciones']);
        if (count($sustituciones) > 0){
            $sustituciones_ = array();
            foreach($sustituciones as $campo => $sustitucion){
                $sustituciones_[$campo] = get_object_vars($sustitucion);
            }
            $sustituciones = $sustituciones_;
        }
    }

    if (isset($_REQUEST['campos_select'])){
        $campos_select = json_decode($_REQUEST['campos_select']);
        if (count($campos_select) > 0){
            $campos_select_ = array();
            foreach($campos_select as $campo => $campo_select){
                $campos_select_[$campo] = get_object_vars($campo_select);
            }
            $campos_select = $campos_select_;
        }
    } 

    if (isset($_REQUEST['parametros']) && count(get_object_vars(json_decode($_REQUEST['parametros']))) > 0){

    	$parametros_recogida = json_decode($_REQUEST['parametros']);

        $parametros_recogida = get_object_vars($parametros_recogida);

	    foreach($parametros_recogida as $clave => $valor){
	    	$parametros[$clave] = fecha_maquina($valor);
	    }
	}



    function fecha_maquina($fecha){
        ///comprobamos si es fecha
        $fecha_array = explode("/", $fecha);
        if (strlen($fecha) == 10 && 
            count($fecha_array) == 3 && 
            strlen($fecha_array[2]) == 4 && 
            strlen($fecha_array[1]) == 2 && 
            strlen($fecha_array[0]) == 2
        ){
            return $fecha_array[2]."-".$fecha_array[1]."-".$fecha_array[0];
        }else
            return $fecha;
    }


    include_once ("php/tabla.php");

    $tabla = new Tabla($tabla, $parametros, $campos_mostrar, $filas_pagina, $desde, $filtro, $sustituciones, $campos_select, $campo_ordenar, $orden_ordenar);

 ?>
