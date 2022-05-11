<?php
    class RoutesController{
        // Ruta principal

        public function index(){

            include "routes/route.php";

        }

        //Nombre de la BD

        static public function db(){

            return "marketplace";
        }
 
    }
?>