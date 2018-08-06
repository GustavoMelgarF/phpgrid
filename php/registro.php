<?php
include_once ("conexion.php");

class Registro extends Conexion {
    


    public function obtenerRegistroUnico(
        $tabla, $parametros
    ){
        $campos = $this->obtenerCampos($tabla);
        ////construcción where sql
        $where_sql = "";

        $parametros_temp = array();

        foreach($parametros as $clave => $valor){
            $clave = str_replace(array("_inferior", "_superior"), "", $clave);
            if (trim($valor) != "")
                $parametros_temp[$clave] = $valor;
        }

        foreach($campos as $fila){
            $campo = $fila['campo'];
            $tipo = $fila['tipo'];

            if (strpos(strtoupper($tipo), 'DATE') !== false) {
                if (array_key_exists($campo, $parametros_temp) && $parametros_temp[$campo] != false){
                    $where_sql_temp = "";

                    if (array_key_exists($campo."_inferior", $parametros) && trim($campo."_inferior") != "")
                        $where_sql .= " AND `{$campo}` >= '".$parametros[$campo."_inferior"]."'";

                    if (array_key_exists($campo."_superior", $parametros) && trim($campo."_superior") != "")
                        $where_sql .= " AND `{$campo}` <= '".$parametros[$campo."_superior"]."'";
                }
            }else{
                if (array_key_exists($campo, $parametros) && $parametros[$campo] != false) 
                    $where_sql .= " AND upper(`{$campo}`) LIKE '%".strtoupper($parametros[$campo])."%' ";
            }
        }

        

        $this->sql = "
            SELECT * FROM `{$tabla}` WHERE 1 ".$where_sql;

        return $this->obtenerResultadoUnico();
    }

    public function obtenerRegistroVarios(
        $tabla, $camposMostrar, $filasPagina, $desde, $parametros, $campoOrdenar, $ordenOrdenar
    ){
        
        $campos = $this->obtenerCampos($tabla);
        ////construcción where sql
        $where_sql = "";

        $camposSelect = "";

        $parametros_temp = array();

        if (count($camposMostrar) > 0){
            foreach($camposMostrar as $campo){
                $camposSelect .= "`".$campo."`".", ";
            }
            $camposSelect = rtrim($camposSelect, ', ');
        }else{
            $camposSelect = "*";
        }   

        foreach($parametros as $clave => $valor){
            $clave = str_replace(array("_inferior", "_superior"), "", $clave);
            if (trim($valor) != "")
                $parametros_temp[$clave] = $valor;
        }

        foreach($campos as $fila){
            $campo = $fila['campo'];
            $tipo = $fila['tipo'];

            if (strpos(strtoupper($tipo), 'DATE') !== false) {
                if (array_key_exists($campo, $parametros_temp) && $parametros_temp[$campo] != false){
                    $where_sql_temp = "";

                    if (array_key_exists($campo."_inferior", $parametros) && trim($campo."_inferior") != "")
                        $where_sql .= " AND `{$campo}` >= '".$parametros[$campo."_inferior"]."'";

                    if (array_key_exists($campo."_superior", $parametros) && trim($campo."_superior") != "")
                        $where_sql .= " AND `{$campo}` <= '".$parametros[$campo."_superior"]."'";
                }
            }elseif (strpos(strtoupper($tipo), 'INT') !== false) {
                if (array_key_exists($campo, $parametros) && $parametros[$campo] != false) 
                    $where_sql .= " AND upper(`{$campo}`) = '".$parametros[$campo]."' ";
            }else{
                if (array_key_exists($campo, $parametros) && $parametros[$campo] != false) 
                    $where_sql .= " AND upper(`{$campo}`) LIKE '%".strtoupper($parametros[$campo])."%' ";
            }
        }  

        $this->sql = "
            SELECT ".$camposSelect." FROM `{$tabla}` WHERE 1 ".$where_sql;
			
		if ($campoOrdenar != "")
			$this->sql .= " ORDER BY upper(`".$campoOrdenar."`) ";
			
		if ($campoOrdenar != "" && $ordenOrdenar != "")
			$this->sql .= $ordenOrdenar;
			
		$this->sql .= " LIMIT ".$desde." , ".$filasPagina;

        return $this->obtenerResultadoMultiple();
    }

    public function obtenerTotalRegistros($tabla, $parametros){

        $campos = $this->obtenerCampos($tabla);
        ////construcción where sql
        $where_sql = "";

        $parametros_temp = array();

        foreach($parametros as $clave => $valor){
            $clave = str_replace(array("_inferior", "_superior"), "", $clave);
            if (trim($valor) != "")
                $parametros_temp[$clave] = $valor;
        }

        foreach($campos as $fila){
            $campo = $fila['campo'];
            $tipo = $fila['tipo'];

            if (strpos(strtoupper($tipo), 'DATE') !== false) {
                if (array_key_exists($campo, $parametros_temp) && $parametros_temp[$campo] != false){
                    $where_sql_temp = "";

                    if (array_key_exists($campo."_inferior", $parametros) && trim($campo."_inferior") != "")
                        $where_sql .= " AND `{$campo}` >= '".$parametros[$campo."_inferior"]."'";

                    if (array_key_exists($campo."_superior", $parametros) && trim($campo."_superior") != "")
                        $where_sql .= " AND `{$campo}` <= '".$parametros[$campo."_superior"]."'";
                }
            }elseif (strpos(strtoupper($tipo), 'INT') !== false) {
                if (array_key_exists($campo, $parametros) && $parametros[$campo] != false) 
                    $where_sql .= " AND upper(`{$campo}`) = '".$parametros[$campo]."' ";
            }else{
                if (array_key_exists($campo, $parametros) && $parametros[$campo] != false) 
                    $where_sql .= " AND upper(`{$campo}`) LIKE '%".strtoupper($parametros[$campo])."%' ";
            }
        }

        $this->sql = "
            SELECT count(*) AS total  FROM `{$tabla}` WHERE 1 ".$where_sql
        ;

        return $this->obtenerResultadoUnico();

    }

    private function obtenerCampos($tabla){
        $this->sql = "select column_name as campo, column_type as tipo from information_schema.columns where table_schema='".self::$baseDatos."' AND table_name='".$tabla."'";
        $campos = $this->obtenerResultadoMultiple();
        $campos = $campos['datos'];
        return $campos;
    }
}