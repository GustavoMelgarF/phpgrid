<?php

class Conexion{
    private static $servidor = "localhost";
    private static $usuario = "user";
    private static $contrasena = "passw";
    public static $baseDatos = "database";

    private $conexion = null;

    protected $sql;
    protected $resultadoQuery;
    protected $resultadoDatos;
    protected $errorQuery;
    protected $filaResultados = array();

    


    private function abrirConexion(){
        if ($this->conexion == null)
            $this->conexion = new mysqli(
                self::$servidor, 
                self::$usuario,
                self::$contrasena,
                self::$baseDatos
                ); 
    }

    private function cerrarConexion(){
        if ($this->conexion != null){
            $this->conexion->close();
            $this->conexion = null;
        }
    }

    public function ejecutarQuerySimple(){
        if ($this->conexion == null)
            $this->abrirConexion();

        $this->resultadoQuery = $this->$this->conexion->query($this->sql);
        if ($this->resultadoQuery){
            $this->resultado = $this->conexion->affected_rows;
            $this->cerrarConexion();

            return array("resultado"=> "exito", "datos" => $this->resultado);
        }else{
            $this->errorQuery = $this->conexion->error;
            $this->cerrarConexion();

            return array("resultado"=> "fallo", "datos" => $this->errorQuery);
        }
    }

    public function obtenerResultadoUnico(){
        if ($this->conexion == null)
            $this->abrirConexion();

        $this->resultadoQuery = $this->conexion->query($this->sql);
        if ($this->resultadoQuery){
            $this->filaResultados = $this->resultadoQuery->fetch_assoc();
            $this->cerrarConexion();

            return array("resultado"=> "exito", "datos" => $this->filaResultados);
        }else{
            $this->errorQuery = $this->conexion->error;
            $this->cerrarConexion();

            return array("resultado"=> "fallo", "datos" => $this->sql.$this->errorQuery);
        }
    }

    public function obtenerResultadoMultiple(){
        if ($this->conexion == null)
            $this->abrirConexion();

        $this->resultadoQuery = $this->conexion->query($this->sql);
        if ($this->resultadoQuery){
            $this->filaResultados = array();
            
            while($this->filaResultados[] = $this->resultadoQuery->fetch_assoc()){}

            $this->cerrarConexion();

            return array("resultado"=> "exito", "datos" => $this->filaResultados);
        }else{
            $this->errorQuery = $this->conexion->error;
            $this->cerrarConexion();

            return array("resultado"=> "fallo", "datos" => $this->sql.$this->errorQuery);
        }
    }
    
}