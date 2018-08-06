<?php

    class Css{
        private $css;

        function __construct(){
            $this->construirCss();
            print $this->css;
        }

        private function construirCss(){
            $this->css = "
                <style>
                    .tabla_datos{
                        width: auto;
                        border-spacing: 0px;
                        border-collapse: separate;
                        padding: 5px;
                    }

                    .tabla_datos th{
                        text-align: left;
                        background-color: #333;
                        color: #fff;
                        padding: 5px;
                    }

                    .fila_filtro{
                        border-bottom: 2px solid #888;
                        margin-botton: 5px;
                    }

                    .fila_datos td{
                        text-align: left;
                        padding: 5px;
                        border-bottom: 1px solid #ddd;
                    }
                    .editar, .eliminar{
                        cursor: pointer
                    }

                    .fila_datos:hover{
                        background-color: #eee;
                    }

                    .fila_filtro input{
                        max-width:80px;
                    }
                
                </style>
            ";
        }
    }