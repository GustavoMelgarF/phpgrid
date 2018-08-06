<?php

//include_once("css.php");
include_once("registro.php");

class Tabla {

    private $parametros = array();
    private $datos = array();
    private $fila = array();
    private $camposMostrar = array();
    private $paginador = array();
    private $sustituciones = array();
    private $camposSelect = array();
    private $filasPagina;
    private $desde;
	private $campoOrdenar;
    private $ordenOrdenar;
    private $tabla_cabecera = "";
    private $tabla_cuerpo = "";
    private $tabla_html = "";
    private $paginador_html = "";
    private $registro = null;

    private $filtro = false;


    function __construct($tabla, $parametros, $camposMostrar, $filasPagina, $desde, $filtro = false, $sustituciones, $camposSelect, $campoOrdenar, $ordenOrdenar){
        $this->registro = new Registro();
        $this->filtro = $filtro;
        $this->camposMostrar = $camposMostrar;
        $this->filasPagina = $filasPagina;
        $this->desde = $desde;
        $this->sustituciones = $sustituciones;
        $this->camposSelect = $camposSelect;
		$this->campoOrdenar = $campoOrdenar;
		$this->ordenOrdenar = $ordenOrdenar;
        $this->datos = $this->registro->obtenerRegistroVarios($tabla, $camposMostrar, $filasPagina, $desde, $parametros, $campoOrdenar, $ordenOrdenar);
        $this->paginador = $this->registro->obtenerTotalRegistros($tabla, $parametros);
        !$filtro ? $this->dibujarTabla() : $this->dibujarTablaFiltro();
        print json_encode(array("tabla" => $this->tabla_html, "paginador" => $this->paginador_html));
    }

    public function dibujarTabla(){
        //new Css();

        if ($this->datos['resultado'] == "exito"){
            $this->datos = $this->datos['datos']; 
            $this->dibujarDatosCabecera();
            $this->dibujarDatosCuerpo();
            $this->dibujarDatos();
        }else{
            $this->dibujarError();
        }
    }

    public function dibujarTablaFiltro(){
        //new Css();

        if ($this->datos['resultado'] == "exito"){
            $this->datos = $this->datos['datos']; 
            //$this->dibujarDatosCabecera();
            $this->dibujarDatosCuerpoFiltro();
            $this->dibujarDatosFiltro();
        }else{
            $this->dibujarError();
        }
    }

    private function dibujarError(){
        print_r($this->datos);
    }

    private function dibujarDatosCabecera(){
        $this->tabla_cabecera .= "<thead><tr>";
        foreach($this->datos as $this->fila){
            foreach($this->fila as $clave => $valor){
                $this->tabla_cabecera .= "<th><span class='titulo_cabecera'>".strtoupper(str_replace("_", " ", $clave))."</span><span class='titulo_ordenar titulo_ordenar_".strtoupper($clave)."'></span></th>";
            }
            break;
        }
        //botones editar y elminar
        $this->tabla_cabecera .= "<th>EDITAR</th><th>ELIMINAR</th>";
        $this->tabla_cabecera .= "</tr>";

        ///dibujar inputs filtro
        $this->tabla_cabecera .= "<tr>";
        foreach($this->datos as $this->fila){
            foreach($this->fila as $clave => $valor){
                //file_put_contents("log.txt", strtoupper($clave).print_r($this->camposSelect, true)."----", FILE_APPEND);
                if (array_key_exists($clave, $this->camposSelect)){
                    $this->tabla_cabecera .= $this->dibujar_filtro_select($clave);
                }else{
                    if ($this->tipoInput($clave) == "filtro_fecha")
                        $this->tabla_cabecera .= "<td class='fila_filtro'><table><tr><td>Min</td><td><input type='text' id='".$clave."_inferior' class='filtro ".$this->tipoInput($clave)."'></td></tr><tr><td>Max</td><td><input type='text' id='".$clave."_superior' class='".$this->tipoInput($clave)."'></td></tr></table></td>";
                    else
                        $this->tabla_cabecera .= "<td class='fila_filtro'><input type='text' id='".$clave."' class='filtro ".$this->tipoInput($clave)."'></td>";
                }
            }
            break;
        }
        //botones editar y elminar
        $this->tabla_cabecera .= "<td class='fila_filtro'>&nbsp;</td><td class='fila_filtro'>&nbsp;</td>";
        $this->tabla_cabecera .= "</tr></thead>";
    }

    private function dibujarDatosCuerpo(){
        $this->tabla_cuerpo .= "<tbody>";
        foreach($this->datos as $this->fila){
            if (is_array($this->fila)){
                $this->tabla_cuerpo .= "<tr class='fila_datos'>";
                foreach($this->fila as $clave => $valor){
                    $valor = $this->sustituciones(array($clave => $valor));
                    $this->tabla_cuerpo .= "<td>".$this->comprobar_fecha_humanos($this->comprobar_fecha_hora_humanos($valor))."</td>";
                }
                //botones editar y elminar
                $this->tabla_cuerpo .= "<td><img class='editar'></td><td><img class='eliminar'></td>";
                $this->tabla_cuerpo .= "</tr>";
            }
        }
        $this->tabla_cuerpo .= "</tbody>";
        $this->dibujarPaginador();
    }

    private function dibujarDatosCuerpoFiltro(){
        foreach($this->datos as $this->fila){
            if (is_array($this->fila)){
                $this->tabla_cuerpo .= "<tr class='fila_datos'>";
                foreach($this->fila as $clave => $valor){
                    $valor = $this->sustituciones(array($clave => $valor));
                    $this->tabla_cuerpo .= "<td>".$this->comprobar_fecha_humanos($this->comprobar_fecha_hora_humanos($valor))."</td>";
                }
                //botones editar y elminar
                $this->tabla_cuerpo .= "<td><img class='editar'></td><td><img class='eliminar'></td>";
                $this->tabla_cuerpo .= "</tr>";
            }
        }
        $this->dibujarPaginador();
    }

    private function tipoInput($clave){
        $devolver = "filtro_texto";
        if (strpos(strtoupper($clave), 'FECHA') !== false) {
            $devolver = "filtro_fecha";
        }
        return $devolver;
    }

    private function dibujar_filtro_select($clave){
        $select_html = "<td class='fila_filtro'><select id='".$clave."' class='filtro filtro_texto' ><option value=''>[".strtoupper($clave)."]</option>";
        foreach($this->camposSelect[$clave] as $clave => $valor){
            $select_html .= "<option value='".$clave."'>".$valor."</option>";
        }
        $select_html .="</select></td>";
        return $select_html;
    }

    private function comprobar_fecha_hora_humanos($fecha_hora){
        $fecha_array = explode(" ",$fecha_hora);
        if (strlen($fecha_hora) == 19 &&
            count($fecha_array)==2 &&
            strlen($fecha_array[1]) == 8 && 
            strlen($fecha_array[0]) == 10
            )
            return $this->comprobar_fecha_humanos($fecha_array[0])." ".$fecha_array[1];
        else
            return $fecha_hora;
    }

    private function comprobar_fecha_humanos($fecha){
        $fecha_array_fecha = explode("-",$fecha);
        if (strlen($fecha) == 10 && 
            count($fecha_array_fecha) == 3 && 
            strlen($fecha_array_fecha[2]) == 2 && 
            strlen($fecha_array_fecha[1]) == 2 && 
            strlen($fecha_array_fecha[0]) == 4
        )
            return $fecha_array_fecha[2]."/".$fecha_array_fecha[1]."/".$fecha_array_fecha[0];
        else
            return $fecha;          
    }

    private function sustituciones($array_campo_valor){
        if (count($this->sustituciones) > 0){
            if (array_key_exists( key($array_campo_valor) , $this->sustituciones)){
                $array_valores_posibles = $this->sustituciones[ key($array_campo_valor) ];
                if (array_key_exists( $array_campo_valor[ key($array_campo_valor) ] , $array_valores_posibles)){
                    $valor = $array_valores_posibles[ $array_campo_valor[ key($array_campo_valor) ] ];
                    return $valor;
                }else
                   return $array_campo_valor[ key($array_campo_valor) ]; 
            }else
                return $array_campo_valor[ key($array_campo_valor) ];
            
        }else
            return $array_campo_valor[ key($array_campo_valor) ];  
    }

    private function dibujarDatos(){
        
        $this->tabla_html .= "<table class='tabla_datos'>";
        $this->tabla_html .= $this->tabla_cabecera;
        $this->tabla_html .= $this->tabla_cuerpo;
        $this->tabla_html .= "</table>";
    }

    private function dibujarDatosFiltro(){
        $this->tabla_html .= $this->tabla_cuerpo;
    }

    private function dibujarPaginador(){
        if ($this->paginador['resultado'] == "exito"){
            $this->paginador = $this->paginador['datos'];
            $total_registros = $this->paginador['total'];

            $this->paginador_html .= "<table class='tabla_paginador'><tr>";
            $this->paginador_html .= "<td><span class='paginador_total' >Total registros: ".$total_registros."</span>   </td>";
            $num_paginas = 0;
            $final_habilitado = "paginador_deshabilitado";
            $siguiente_habilitado = "paginador_deshabilitado";
            $inicio_habilitado = "paginador_deshabilitado";
            $anterior_habilitado = "paginador_deshabilitado";
            $dato_final = $total_registros;
            
            if ($total_registros > 0){
                $num_paginas = ceil($total_registros / $this->filasPagina);

                $dato_final = ($num_paginas * $this->filasPagina)-$total_registros == 0 ? ($num_paginas * $this->filasPagina)-$this->filasPagina : (($num_paginas-1) * $this->filasPagina);
            }

            if ( $total_registros - (($this->desde) + $this->filasPagina) > 0){
                $final_habilitado = "paginador_habilitado";
                $siguiente_habilitado = "paginador_habilitado";
            }
            if ( ($this->desde) >= $this->filasPagina ){
                $inicio_habilitado = "paginador_habilitado";
                $anterior_habilitado = "paginador_habilitado";
            }
            
            $this->paginador_html .= "<td><span class='paginador_celda paginador_inicio ".$inicio_habilitado."' id='0' >Inicio</span></td>";
            $this->paginador_html .= "<td><span class='paginador_celda paginador_anterior ".$anterior_habilitado."' id='".(($this->desde - $this->filasPagina))."' >Anterior</span></td>";
            $this->paginador_html .= "<td><span class='paginador_celda paginador_num_pagina' >P&aacute;gina ".($total_registros > 0 ? ceil(($this->desde+1) / $this->filasPagina)  : '1')." / ".($total_registros > 0 ? $num_paginas : '1')."</span></td>";
            $this->paginador_html .= "<td><span class='paginador_celda paginador_siguiente ".$siguiente_habilitado."' id='".(($this->desde + $this->filasPagina))."'>Siguiente</span></td>";
            $this->paginador_html .= "<td><span class='paginador_celda paginador_final ".$final_habilitado."' id='".$dato_final."'>Final</span></td>";
            $this->paginador_html .= "</tr></table>";
        }else{
            $this->dibujarError();
        }
    }
}